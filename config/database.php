<?php
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=chillzone_shop;charset=utf8mb4',
        'root', // Standard MySQL Benutzername
        '',     // Leeres Passwort für lokale Entwicklung
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        )
    );
} catch (PDOException $e) {
    die('Verbindungsfehler: ' . $e->getMessage());
}
