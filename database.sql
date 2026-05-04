CREATE DATABASE IF NOT EXISTS oa_clothing_inventory;
USE oa_clothing_inventory;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('owner','cashier') NOT NULL
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    status VARCHAR(20) DEFAULT 'Active',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    category_id INT,
    size VARCHAR(10) NOT NULL,
    color VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    low_stock_limit INT DEFAULT 5,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20),
    address VARCHAR(255),
    email VARCHAR(100),
    date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sales (
    sale_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NULL,
    payment_method VARCHAR(50) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE sale_details (
    sale_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_sold INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Simple indexing for faster search and reports
CREATE INDEX idx_product_name ON products(product_name);
CREATE INDEX idx_product_size_color ON products(size, color);
CREATE INDEX idx_sale_date ON sales(sale_date);

-- Default accounts
-- Password for both accounts: 12345
INSERT INTO users (username, password, role) VALUES
('owner', '$2y$10$KxmX0WbUtDODiAYLXH48X.f92QNKEDgN91A.0UgZpF1ZG1yuFe.Du', 'owner'),
('cashier', '$2y$10$KxmX0WbUtDODiAYLXH48X.f92QNKEDgN91A.0UgZpF1ZG1yuFe.Du', 'cashier');

INSERT INTO categories (category_name, description) VALUES
('Shirt', 'Clothing shirts'),
('Pants', 'Clothing pants'),
('Dress', 'Clothing dresses');
