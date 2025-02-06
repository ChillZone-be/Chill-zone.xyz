-- Datenbank erstellen
CREATE DATABASE IF NOT EXISTS chillzone_shop;
USE chillzone_shop;

-- Produkte Tabelle
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    category VARCHAR(50),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bestellungen Tabelle
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_email VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bestelldetails Tabelle
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price_at_time DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Beispiel-Produkte einfügen
INSERT INTO products (name, description, price, category, stock) VALUES
('Chill-Zone T-Shirt', 'Comfortable cotton t-shirt with Chill-Zone logo', 24.99, 'Clothing', 50),
('Chill-Zone Hoodie', 'Warm and cozy hoodie perfect for gaming sessions', 49.99, 'Clothing', 30),
('LED Gaming Mouse', 'RGB gaming mouse with programmable buttons', 39.99, 'Electronics', 25),
('Gaming Mousepad', 'Large mousepad with Chill-Zone design', 19.99, 'Accessories', 40),
('Chill-Zone Cap', 'Stylish cap with embroidered logo', 22.99, 'Accessories', 35),
('Gaming Headset', 'High-quality headset for immersive gaming', 79.99, 'Electronics', 20);
