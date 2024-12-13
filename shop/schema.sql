-- Datenbank erstellen
CREATE DATABASE IF NOT EXISTS chillzone_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE chillzone_shop;

-- Produkte Tabelle
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    category VARCHAR(50) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Benutzer Tabelle
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bestellungen Tabelle
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Bestellpositionen Tabelle
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Beispiel-Produkte einfügen
INSERT INTO products (name, description, price, image_url, category, stock) VALUES
('Anime Print T-Shirt', 'Stylish anime-themed t-shirt perfect for any fan', 29.99, 'images/product1.jpg', 'clothing', 15),
('Kawaii Hoodie', 'Comfortable and cute hoodie with kawaii design', 49.99, 'images/product2.jpg', 'clothing', 8),
('Manga Collection', 'Complete collection of popular manga series', 79.99, 'images/product3.jpg', 'collectibles', 3),
('Anime Figurine', 'High-quality anime character figurine', 89.99, 'images/product4.jpg', 'collectibles', 0),
('Kawaii Backpack', 'Adorable and practical backpack for everyday use', 39.99, 'images/product5.jpg', 'accessories', 12);
