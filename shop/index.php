<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$products = getProducts($pdo);

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    foreach ($products as &$product) {
        $product['category'] = $product['category'] ?? 'uncategorized';
        $product['stock'] = $product['stock'] ?? 0;
    }
    echo json_encode(['success' => true, 'products' => $products]);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $productId = (int)$_POST['product_id'];
    $product = getProduct($pdo, $productId);
    
    if ($product && $product['stock'] > 0) {
        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = 1;
        } else {
            $_SESSION['cart'][$productId]++;
        }
        updateProductStock($pdo, $productId, $product['stock'] - 1);
        
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'cartCount' => array_sum($_SESSION['cart']),
                'message' => 'Added to cart!'
            ]);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Chill-Zone.xyz</title>
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
            background: linear-gradient(135deg, var(--dark), #2a2a2a);
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

        .shop-title {
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .filters {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .search-box input {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            font-size: 1rem;
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .category-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .category-btn {
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            cursor: pointer;
            transition: var(--transition);
        }

        .category-btn:hover,
        .category-btn.active {
            background: var(--primary);
            transform: translateY(-2px);
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .product-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            transition: var(--transition);
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .product-category {
            font-size: 0.9rem;
            color: var(--primary);
        }

        .product-name {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .price {
            font-size: 1.1rem;
            color: var(--secondary);
        }

        .stock-status {
            font-size: 0.9rem;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            text-align: center;
        }

        .in-stock { background: rgba(0, 255, 0, 0.2); }
        .low-stock { background: rgba(255, 165, 0, 0.2); }
        .out-of-stock { background: rgba(255, 0, 0, 0.2); }

        .btn {
            padding: 0.8rem;
            border: none;
            border-radius: 10px;
            background: var(--primary);
            color: var(--light);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .btn:hover:not(:disabled) {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .cart {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
            z-index: 1000;
        }

        .cart:hover {
            background: var(--secondary);
            transform: translateY(-5px);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--secondary);
            color: var(--light);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .nav-bar {
                padding: 1rem;
                gap: 1rem;
            }

            .container {
                padding: 1rem;
            }

            .products {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
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
        <a href="index.php" class="active">Shop</a>
    </nav>

    <div class="container">
        <h1 class="shop-title">Shop</h1>

        <div class="filters glass">
            <div class="search-box">
                <input type="text" placeholder="Search products..." onkeyup="filterProducts()">
            </div>
            <div class="category-buttons">
                <button class="category-btn active" onclick="filterByCategory('all')">All</button>
                <button class="category-btn" onclick="filterByCategory('clothing')">Clothing</button>
                <button class="category-btn" onclick="filterByCategory('accessories')">Accessories</button>
                <button class="category-btn" onclick="filterByCategory('collectibles')">Collectibles</button>
            </div>
        </div>

        <div class="products">
            <?php foreach ($products as $product): ?>
            <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="product-info">
                    <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                    <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="price">€<?php echo number_format($product['price'], 2); ?></div>
                    <div class="stock-status <?php echo getStockStatusClass($product['stock']); ?>">
                        <?php echo getStockStatusText($product['stock']); ?>
                    </div>
                    <button class="btn" onclick="addToCart(<?php echo $product['id']; ?>)"
                            <?php echo $product['stock'] === 0 ? 'disabled' : ''; ?>>
                        <?php echo $product['stock'] === 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="cart" onclick="showCart()">
            🛒
            <span class="cart-count" id="cartCount"><?php echo array_sum($_SESSION['cart']); ?></span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('loaded');
        });

        function filterProducts() {
            const searchTerm = document.querySelector('.search-box input').value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const name = product.querySelector('.product-name').textContent.toLowerCase();
                const category = product.dataset.category.toLowerCase();
                const visible = name.includes(searchTerm) || category.includes(searchTerm);
                product.style.display = visible ? 'block' : 'none';
            });
        }

        function filterByCategory(category) {
            document.querySelectorAll('.category-btn').forEach(btn => 
                btn.classList.remove('active')
            );
            event.target.classList.add('active');
            
            const products = document.querySelectorAll('.product-card');
            products.forEach(product => {
                if (category === 'all' || product.dataset.category === category) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        function addToCart(productId) {
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&product_id=${productId}&ajax=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cartCount').textContent = data.cartCount;
                    showNotification(data.message);
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error adding to cart');
            });
        }

        function showCart() {
            window.location.href = 'cart.php';
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
