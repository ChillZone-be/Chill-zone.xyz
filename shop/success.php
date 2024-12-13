<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Überprüfe ob eine Bestellung vorhanden war
if (empty($_SESSION['last_order_id'])) {
    header('Location: index.php');
    exit;
}

// Hole Bestelldetails
$orderId = $_SESSION['last_order_id'];

// Lösche Warenkorb nach erfolgreicher Bestellung
unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellung erfolgreich - Chill Zone Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Lekton&family=Quicksand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Chill Zone Shop</div>
            <div class="nav-links">
                <a href="index.php">Shop</a>
                <a href="cart.php">Warenkorb</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="success-message">
            <h1>Vielen Dank für Ihre Bestellung!</h1>
            <p>Ihre Bestellung mit der Nummer #<?php echo $orderId; ?> wurde erfolgreich abgeschlossen.</p>
            <p>Sie erhalten in Kürze eine Bestätigungs-E-Mail mit allen Details.</p>
            <a href="index.php" class="btn">Zurück zum Shop</a>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Chill Zone Shop. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
