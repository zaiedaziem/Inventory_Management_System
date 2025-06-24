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
    supplier_id INT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

-- Insert suppliers with Malaysian info
INSERT INTO suppliers (name, contact_email, phone, address) VALUES
('Teknologi Peralatan Sdn Bhd', 'contact@tekperalatan.com.my', '+60-3-7890-0101', 'Jalan Perindustrian, 47301 Petaling Jaya, Selangor'),
('Peralatan Pejabat Malaysia', 'info@pejabatperalatan.my', '+60-3-7890-0102', 'No. 5, Jalan Seri, 56100 Kuala Lumpur, Wilayah Persekutuan');

-- Insert users with Malaysian names and emails
INSERT INTO users (name, email, password, role, department) VALUES
('Ahmad Staff', 'ahmad.staff@company.com.my', 'staff123', 'staff', 'warehouse'),
('Siti Manager', 'siti.manager@company.com.my', 'manager123', 'manager', 'inventory'),
('Admin User', 'admin.user@company.com.my', 'admin123', 'admin', 'management');

-- Insert products with Malaysian suppliers
INSERT INTO products (name, sku, category, price, quantity, minimum_stock, supplier_id, description) VALUES
('Laptop Dell XPS 13', 'DELL-XPS-001', 'electronics', 5499.99, 25, 5, 1, 'High-performance laptop'),
('Kerusi Pejabat Ergonomik', 'CHAIR-001', 'furniture', 799.99, 50, 10, 2, 'Ergonomic office chair'),
('Maus Wayarles', 'MOUSE-001', 'electronics', 129.99, 8, 15, 1, 'Bluetooth wireless mouse');
