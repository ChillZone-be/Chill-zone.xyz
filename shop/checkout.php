<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Überprüfe ob Warenkorb leer ist
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Berechne Gesamtsumme
$total = 0;
foreach ($_SESSION['cart'] as $productId => $quantity) {
    $product = getProduct($pdo, $productId);
    if ($product) {
        $total += $product['price'] * $quantity;
    }
}

// Skrill API Konfiguration (Beispiel)
define('SKRILL_MERCHANT_ID', 'YOUR_MERCHANT_ID');
define('SKRILL_SECRET_WORD', 'YOUR_SECRET_WORD');
define('SKRILL_API_URL', 'https://pay.skrill.com');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Erstelle Skrill Zahlungsanfrage
    $payment_data = array(
        'pay_to_email' => SKRILL_MERCHANT_ID,
        'transaction_id' => uniqid('order_'),
        'return_url' => 'https://your-domain.com/shop/success.php',
        'cancel_url' => 'https://your-domain.com/shop/cancel.php',
        'status_url' => 'https://your-domain.com/shop/callback.php',
        'language' => 'DE',
        'amount' => $total,
        'currency' => 'EUR',
        'detail1_description' => 'Bestellung bei Chill Zone Shop',
        'detail1_text' => 'Bestellnummer: ' . uniqid('order_')
    );

    // Speichere Bestellung in der Datenbank
    $order_id = saveOrder($pdo, $_SESSION['user_id'] ?? null, $total, $_SESSION['cart']);

    // Leite zur Skrill Zahlungsseite weiter
    header('Location: ' . SKRILL_API_URL . '?' . http_build_query($payment_data));
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Chill Zone Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Lekton&family=Quicksand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Chill Zone Shop</div>
            <div class="nav-links">
                <a href="index.php">Shop</a>
                <a href="cart.php">Warenkorb (<?php echo array_sum($_SESSION['cart'] ?? []); ?>)</a>
            </div>
        </nav>
    </header>

    <main>
        <h1>Checkout</h1>
        
        <div class="checkout-summary">
            <h2>Bestellübersicht</h2>
            <p>Gesamtsumme: <?php echo number_format($total, 2, ',', '.'); ?> €</p>
        </div>

        <form method="post" class="checkout-form">
            <div class="form-group">
                <label for="email">E-Mail Adresse:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="address">Adresse:</label>
                <input type="text" id="address" name="address" required>
            </div>

            <div class="payment-info">
                <h3>Bezahlung mit Skrill</h3>
                <p>Sie werden nach dem Absenden zur sicheren Zahlungsseite von Skrill weitergeleitet.</p>
            </div>

            <button type="submit" class="btn">Jetzt bezahlen</button>
        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Chill Zone Shop. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
