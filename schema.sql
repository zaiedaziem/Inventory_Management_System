CREATE DATABASE jwt_demo;
USE jwt_demo;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert a test user (password: testpass)
INSERT INTO users (username, password)
VALUES ('testuser', 'password'); -- Use password_hash in PHP
