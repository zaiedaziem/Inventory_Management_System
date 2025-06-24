CREATE DATABASE inventory_management;
USE inventory_management;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('staff', 'manager', 'admin') DEFAULT 'staff',
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) UNIQUE NOT NULL,
    category VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    minimum_stock INT DEFAULT 10,
    supplier_id INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

INSERT INTO suppliers (name, contact_email, phone) VALUES
('Tech Supplies Co', 'contact@techsupplies.com', '+1-555-0101'),
('Office Equipment Ltd', 'info@officeequip.com', '+1-555-0102');

INSERT INTO users (name, email, password, role, department) VALUES
('John Staff', 'staff@company.com', 'staff123', 'staff', 'warehouse'),
('Jane Manager', 'manager@company.com', 'manager123', 'manager', 'inventory'),
('Admin User', 'admin@company.com', 'admin123', 'admin', 'management');

INSERT INTO products (name, sku, category, price, quantity, minimum_stock, supplier_id, description) VALUES
('Laptop Dell XPS 13', 'DELL-XPS-001', 'electronics', 1299.99, 25, 5, 1, 'High-performance laptop'),
('Office Chair', 'CHAIR-001', 'furniture', 199.99, 50, 10, 2, 'Ergonomic office chair'),
('Wireless Mouse', 'MOUSE-001', 'electronics', 29.99, 8, 15, 1, 'Bluetooth wireless mouse');