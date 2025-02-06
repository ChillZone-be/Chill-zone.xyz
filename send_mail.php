<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'includes/Logger.php';
require 'includes/EmailTemplate.php';
require 'includes/RateLimiter.php';

// Disable all output except our JSON response
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Set JSON header at the beginning
//header('Content-Type: application/json');

// Clean any output buffers
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

session_start();

try {
    // Initialize Logger
    $logger = new Logger(__DIR__ . '/logs/email.log');

    // Initialize RateLimiter
    $rateLimiter = new RateLimiter(5, 60);

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Ungültige Anforderung.");
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Prüfe Rate Limit
    if ($rateLimiter->tooManyAttempts('email', $ip)) {
        throw new Exception("Zu viele Versuche. Bitte warten Sie eine Stunde.");
    }
    
    // Honeypot Check
    if (!empty($_POST['website'])) {
        throw new Exception("Spam-Verdacht erkannt.");
    }

    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));
    $token = $_POST['g-recaptcha-response'];

    if (empty($name) || empty($email) || empty($message)) {
        throw new Exception("Bitte füllen Sie alle Felder aus.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Ungültige E-Mail-Adresse.");
    }

    // reCAPTCHA Validierung
    $recaptcha_secret = "6Lec2XMqAAAAAKBPkj_Z4-F2UPTNeZClnVwye74o";
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $recaptcha_secret,
        'response' => $token,
        'remoteip' => $ip
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data),
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($recaptcha_url, false, $context);
    $responseKeys = json_decode($result, true);

    if (!$responseKeys["success"]) {
        throw new Exception("reCAPTCHA-Überprüfung fehlgeschlagen. Bitte versuchen Sie es erneut.");
    }

    // Mail-Konfiguration laden
    $config = require __DIR__ . '/config/mail_config.php';
    
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0; // Disable debug output
    
    // SMTP Konfiguration
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp_username'];
    $mail->Password = $config['smtp_password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $config['smtp_port'];
    $mail->CharSet = 'UTF-8';

    $mail->setFrom($config['mail_from'], $config['mail_from_name']);
    $mail->addAddress($config['mail_to']);
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = "Neue Nachricht von $name";
    
    // E-Mail Template erstellen
    $template = new EmailTemplate();
    $template->setVariable('subject', "Neue Nachricht von $name")
             ->setVariable('name', $name)
             ->setVariable('email', $email)
             ->setVariable('message', $message);
             
    $mail->Body = $template->render();
    $mail->AltBody = "Von: $name ($email)\n\nNachricht:\n$message";

    // Send email
    if($mail->send()) {
        // Redirect to success page instead of JSON response
        header('Location: success.html');
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Fehler beim Senden der E-Mail: ' . $mail->ErrorInfo
        ]);
    }
    
    // Rate Limiter aktualisieren
    $rateLimiter->hit('email', $ip);
    
    // Erfolg loggen
    $logger->success("E-Mail erfolgreich gesendet von: $email (IP: $ip)");
    
} catch (Exception $e) {
    $logger->error("Error: " . $e->getMessage());
    if (isset($mail)) {
        $logger->error("Mailer Error: " . $mail->ErrorInfo);
    }
    
    // Clean output buffer before JSON response
    ob_clean();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Ensure we send only the final JSON
ob_end_flush();
