<?php
/**
 * Holt alle Produkte aus der Datenbank
 */
function getProducts($pdo) {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY name');
    return $stmt->fetchAll();
}

/**
 * Holt ein einzelnes Produkt aus der Datenbank
 */
function getProduct($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Aktualisiert den Lagerbestand eines Produkts
 */
function updateProductStock($pdo, $productId, $newStock) {
    $stmt = $pdo->prepare('UPDATE products SET stock = ? WHERE id = ?');
    return $stmt->execute([$newStock, $productId]);
}

/**
 * Gibt die Lagerbestandsklasse zurück
 */
function getStockStatusClass($stock) {
    if ($stock <= 0) return 'out-of-stock';
    if ($stock <= 5) return 'low-stock';
    return 'in-stock';
}

/**
 * Gibt den Lagerbestandstext zurück
 */
function getStockStatusText($stock) {
    if ($stock <= 0) return 'Out of Stock';
    if ($stock <= 5) return 'Low Stock';
    return 'In Stock';
}

/**
 * Berechnet den Gesamtbetrag des Warenkorbs
 */
function calculateTotal($pdo, $cartItems) {
    $total = 0;
    foreach ($cartItems as $productId => $quantity) {
        $product = getProduct($pdo, $productId);
        if ($product) {
            $total += $product['price'] * $quantity;
        }
    }
    return $total;
}

/**
 * Speichert eine neue Bestellung in der Datenbank
 */
function saveOrder($pdo, $userId, $total, $items) {
    try {
        $pdo->beginTransaction();

        // Hauptbestellung speichern
        $stmt = $pdo->prepare('
            INSERT INTO orders (user_id, total, created_at)
            VALUES (?, ?, NOW())
        ');
        $stmt->execute([$userId, $total]);
        $orderId = $pdo->lastInsertId();

        // Bestellpositionen speichern
        $stmt = $pdo->prepare('
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ');

        foreach ($items as $productId => $quantity) {
            $product = getProduct($pdo, $productId);
            if ($product) {
                $stmt->execute([
                    $orderId,
                    $productId,
                    $quantity,
                    $product['price']
                ]);
            }
        }

        $pdo->commit();
        return $orderId;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Validiert eine Skrill-Zahlung
 */
function validateSkrillPayment($data) {
    // Konkateniere die Felder in der richtigen Reihenfolge
    $concatenated = $data['merchant_id'] 
                 . $data['transaction_id']
                 . strtoupper(md5(SKRILL_SECRET_WORD))
                 . $data['mb_amount']
                 . $data['mb_currency']
                 . $data['status'];

    // Generiere den MD5-Hash
    $calculatedHash = strtoupper(md5($concatenated));

    // Vergleiche mit dem empfangenen Hash
    return $calculatedHash === $data['md5sig'];
}

/**
 * Aktualisiert den Bestellstatus
 */
function updateOrderStatus($pdo, $orderId, $status) {
    $stmt = $pdo->prepare('
        UPDATE orders 
        SET status = ?, updated_at = NOW()
        WHERE id = ?
    ');
    return $stmt->execute([$status, $orderId]);
}

/**
 * Loggt Zahlungsereignisse
 */
function logPaymentEvent($type, $data) {
    $logEntry = date('Y-m-d H:i:s') . " | $type | " . json_encode($data) . "\n";
    file_put_contents(__DIR__ . '/../logs/payments.log', $logEntry, FILE_APPEND);
}

/**
 * Validiert die Warenkorbspositionen und ihre Mengen
 */
function validateCartItems($pdo, $cartItems) {
    $errors = [];
    foreach ($cartItems as $productId => $quantity) {
        $product = getProduct($pdo, $productId);
        if (!$product) {
            $errors[] = "Product #$productId not found";
            continue;
        }
        if ($product['stock'] < $quantity) {
            $errors[] = "Not enough stock for {$product['name']}";
        }
    }
    return $errors;
}

/**
 * Formatiert den Preis mit Währung
 */
function formatPrice($price, $currency = '€') {
    return $currency . number_format($price, 2, '.', ',');
}
