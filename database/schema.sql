-- Tabloları oluştur
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    since DATE NOT NULL,
    revenue DECIMAL(10, 2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,  
    customer_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- Örnek verileri ekle
-- Customers
INSERT INTO customers (id, name, since, revenue) VALUES
(1, 'Türker Jöntürk', '2014-06-28', 492.12),
(2, 'Kaptan Devopuz', '2015-01-15', 1505.95),
(3, 'İsa Sonuyumaz', '2016-02-11', 0.00);

-- Products
INSERT INTO products (id, name, category, price, stock) VALUES
(100, 'Black&Decker A7062 40 Parça Cırcırlı Tornavida Seti', 1, 120.75, 10),
(101, 'Reko Mini Tamir Hassas Tornavida Seti 32''li', 1, 49.50, 10),
(102, 'Viko Karre Anahtar - Beyaz', 2, 11.28, 10),
(103, 'Legrand Antica Anahtar, Beyaz', 2, 11.28, 10),
(104, 'Schneider Asfora Beyaz Komütatör', 2, 11.28, 10);

-- Orders
INSERT INTO orders (customer_id, total) VALUES
(1, 112.80),
(2, 219.75),
(3, 1275.18);

-- Order Items
INSERT INTO order_items (order_id, product_id, quantity, unit_price, total) VALUES
-- Order 1 items
(1, 102, 10, 11.28, 112.80),
-- Order 2 items
(2, 101, 2, 49.50, 99.00),
(2, 100, 1, 120.75, 120.75),
-- Order 3 items
(3, 102, 112, 11.28, 1263.36),
(3, 101, 1, 11.82, 11.82);