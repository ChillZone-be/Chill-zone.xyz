<?php
session_start();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellung abgebrochen - Chill Zone Shop</title>
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
        <div class="cancel-message">
            <h1>Bestellung abgebrochen</h1>
            <p>Die Zahlung wurde abgebrochen oder es ist ein Fehler aufgetreten.</p>
            <p>Sie können es erneut versuchen oder uns bei Problemen kontaktieren.</p>
            <div class="button-group">
                <a href="cart.php" class="btn">Zurück zum Warenkorb</a>
                <a href="index.php" class="btn">Weiter einkaufen</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Chill Zone Shop. Alle Rechte vorbehalten.</p>
    </footer>
</body>
</html>
