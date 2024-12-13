<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);
    
    switch ($action) {
        case 'add':
            if ($productId > 0) {
                $product = getProduct($pdo, $productId);
                if ($product && $product['stock'] > 0) {
                    if (!isset($_SESSION['cart'][$productId])) {
                        $_SESSION['cart'][$productId] = 1;
                    } else {
                        $_SESSION['cart'][$productId]++;
                    }
                    updateProductStock($pdo, $productId, $product['stock'] - 1);
                }
            }
            break;
            
        case 'remove':
            if (isset($_SESSION['cart'][$productId])) {
                $product = getProduct($pdo, $productId);
                if ($product) {
                    updateProductStock($pdo, $productId, $product['stock'] + $_SESSION['cart'][$productId]);
                    unset($_SESSION['cart'][$productId]);
                }
            }
            break;
            
        case 'update':
            $quantity = (int)($_POST['quantity'] ?? 0);
            if ($quantity > 0) {
                $product = getProduct($pdo, $productId);
                if ($product) {
                    $oldQuantity = $_SESSION['cart'][$productId] ?? 0;
                    $stockDiff = $oldQuantity - $quantity;
                    updateProductStock($pdo, $productId, $product['stock'] + $stockDiff);
                    $_SESSION['cart'][$productId] = $quantity;
                }
            } else {
                unset($_SESSION['cart'][$productId]);
            }
            break;
    }
    
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'cartCount' => array_sum($_SESSION['cart']),
            'total' => calculateTotal($pdo, $_SESSION['cart'])
        ]);
        exit;
    }
    
    header('Location: cart.php');
    exit;
}

$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $product = getProduct($pdo, $productId);
        if ($product) {
            $cartItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $product['price'] * $quantity
            ];
            $total += $product['price'] * $quantity;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Chill Zone</title>
    <link rel="icon" type="image/png" href="../images/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="../images/favicon.svg">
    <link rel="shortcut icon" href="../images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="../images/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-title" content="Chill-zone">
    <link rel="manifest" href="../images/site.webmanifest">
    <link rel="icon" type="image/x-icon" href="../images/default.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #ff0099;
            --secondary: #6c63ff;
            --dark: #1a1a1a;
            --light: #ffffff;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            font-family: 'Segoe UI', sans-serif;
            height: 100%;
        }

        body {
            background: url("../images/animebg.jpg") no-repeat center center fixed;
            background-size: cover;
            color: var(--light);
            margin: 0;
            opacity: 0;
            transition: var(--transition);
            overflow-x: hidden;
            overflow-y: auto;
        }

        body.loaded {
            opacity: 1;
        }

        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
        }

        .overlay {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 2rem;
            z-index: 1000;
        }

        .overlay img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .overlay span {
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-bar {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 2rem;
            padding: 1rem 2rem;
            z-index: 1000;
        }

        .nav-bar a {
            color: var(--light);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-bar a:hover,
        .nav-bar a.active {
            color: var(--primary);
        }

        .container {
            margin-top: 180px;
            padding: 2rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .cart-title {
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .empty-cart {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            margin-top: 2rem;
        }

        .empty-cart p {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .cart-item {
            display: flex;
            gap: 1.5rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
        }

        .cart-item img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
        }

        .item-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .item-details h3 {
            font-size: 1.2rem;
            color: var(--light);
        }

        .quantity {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .quantity button {
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 50%;
            background: var(--primary);
            color: var(--light);
            cursor: pointer;
            transition: var(--transition);
        }

        .quantity button:hover {
            background: var(--secondary);
            transform: scale(1.1);
        }

        .quantity span {
            font-size: 1.1rem;
            min-width: 30px;
            text-align: center;
        }

        .subtotal {
            font-size: 1.1rem;
            color: var(--secondary);
        }

        .remove-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 25px;
            background: rgba(255, 0, 0, 0.2);
            color: var(--light);
            cursor: pointer;
            transition: var(--transition);
            align-self: flex-start;
        }

        .remove-btn:hover {
            background: rgba(255, 0, 0, 0.4);
            transform: translateY(-2px);
        }

        .cart-summary {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            text-align: right;
        }

        .total {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .checkout-btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 25px;
            background: var(--primary);
            color: var(--light);
            font-size: 1.1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .checkout-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .continue-shopping {
            display: inline-block;
            margin-top: 1rem;
            color: var(--light);
            text-decoration: none;
            transition: var(--transition);
        }

        .continue-shopping:hover {
            color: var(--primary);
            transform: translateX(-5px);
        }

        .notification {
            position: fixed;
            bottom: 80px;
            right: 20px;
            background: var(--primary);
            color: white;
            padding: 1rem 2rem;
            border-radius: 25px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
            }

            .cart-item img {
                width: 100%;
                height: 200px;
            }

            .item-details {
                text-align: center;
            }

            .quantity {
                justify-content: center;
            }

            .remove-btn {
                align-self: center;
            }
        }
    </style>
</head>
<body>
    <div class="overlay glass">
        <img src="../images/default.jpeg" alt="Logo">
        <span>Chill-Zone.xyz</span>
    </div>

    <nav class="nav-bar glass">
        <a href="../index.html">Home</a>
        <a href="../about.html">About</a>
        <a href="../blog.html">Blog</a>
        <a href="index.php">Shop</a>
    </nav>

    <div class="container">
        <h1 class="cart-title">Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>
            <div class="empty-cart glass">
                <p>Your cart is empty</p>
                <a href="index.php" class="checkout-btn">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item glass" data-product-id="<?php echo $item['product']['id']; ?>">
                        <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['product']['name']); ?></h3>
                            <div class="price">€<?php echo number_format($item['product']['price'], 2); ?></div>
                            <div class="quantity">
                                <button onclick="updateQuantity(<?php echo $item['product']['id']; ?>, -1)">-</button>
                                <span><?php echo $item['quantity']; ?></span>
                                <button onclick="updateQuantity(<?php echo $item['product']['id']; ?>, 1)">+</button>
                            </div>
                            <div class="subtotal">
                                Subtotal: €<?php echo number_format($item['subtotal'], 2); ?>
                            </div>
                            <button class="remove-btn" onclick="removeItem(<?php echo $item['product']['id']; ?>)">
                                Remove
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary glass">
                <div class="total">
                    Total: €<span id="cartTotal"><?php echo number_format($total, 2); ?></span>
                </div>
                <button class="checkout-btn" onclick="checkout()">Proceed to Checkout</button>
                <br>
                <a href="index.php" class="continue-shopping">← Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('loaded');
        });

        function updateQuantity(productId, change) {
            const item = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
            const quantitySpan = item.querySelector('.quantity span');
            let newQuantity = parseInt(quantitySpan.textContent) + change;
            
            if (newQuantity < 1) {
                removeItem(productId);
                return;
            }
            
            fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update&product_id=${productId}&quantity=${newQuantity}&ajax=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function removeItem(productId) {
            fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&product_id=${productId}&ajax=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function checkout() {
            showNotification('Checkout functionality coming soon!');
        }

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '1';
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 300);
                }, 2000);
            }, 100);
        }
    </script>
</body>
</html>
