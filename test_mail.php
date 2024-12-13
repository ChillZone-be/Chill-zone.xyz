<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Setzen der Umgebungsvariablen
putenv("SMTP_USERNAME=chillteam@web.de");
putenv("SMTP_PASSWORD=Kleiner2006/");

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->SMTPDebug = 2; // Debug-Ausgabe aktivieren
    
    // Konfiguration laden
    $config = require_once 'config.php';
    
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp_username'];
    $mail->Password = $config['smtp_password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $config['smtp_port'];

    $mail->setFrom('chillteam@web.de', 'Test');
    $mail->addAddress('chillteam@web.de', 'Test');

    $mail->isHTML(false);
    $mail->Subject = 'Test E-Mail';
    $mail->Body    = 'Dies ist eine Test-E-Mail um die SMTP-Verbindung zu überprüfen.';

    $mail->send();
    echo "Test-E-Mail wurde erfolgreich gesendet!\n";
} catch (Exception $e) {
    echo "Fehler beim Senden der Test-E-Mail: {$mail->ErrorInfo}\n";
}
?>
