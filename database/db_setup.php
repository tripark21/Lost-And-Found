<?php
// Database Setup Script for Lost & Found System
// Run this once to initialize the database

include '../config/config.php';

// Create database
$create_db = "CREATE DATABASE IF NOT EXISTS lost_found_db";
if ($conn->query($create_db) === TRUE) {
    echo "✓ Database created successfully or already exists.\n";
} else {
    die("✗ Error creating database: " . $conn->error);
}

// Select database
if (!$conn->select_db("lost_found_db")) {
    die("✗ Error selecting database: " . $conn->error);
}

// Create items table
$create_table = "CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_type ENUM('lost', 'found') NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    date_reported DATE NOT NULL,
    location VARCHAR(150) NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(100) NOT NULL,
    contact_phone VARCHAR(20),
    status ENUM('unresolved', 'claimed', 'returned') DEFAULT 'unresolved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_item_type (item_type),
    INDEX idx_status (status),
    INDEX idx_category (category)
)";

if ($conn->query($create_table) === TRUE) {
    echo "✓ Items table created successfully or already exists.\n";
} else {
    die("✗ Error creating table: " . $conn->error);
}

// Insert sample data (optional)
$sample_data = "INSERT INTO items (item_type, item_name, description, category, date_reported, location, contact_name, contact_email, contact_phone, status) 
                VALUES 
                ('lost', 'Samsung Galaxy Phone', 'Blue Samsung phone with blue case, last seen near library', 'Electronics', '2024-01-15', 'Main Library', 'John Doe', 'john@college.edu', '555-0123', 'unresolved'),
                ('found', 'Black Wallet', 'Found in cafeteria with multiple ID cards inside', 'Accessories', '2024-01-14', 'Cafeteria', 'Jane Smith', 'jane@college.edu', '555-0456', 'unresolved')";

if ($conn->query($sample_data) === TRUE) {
    echo "✓ Sample data inserted successfully.\n";
} else {
    // Don't fail if sample data already exists
    echo "✓ Sample data already exists or not inserted.\n";
}

echo "\n✓ Database setup complete! Your Lost & Found system is ready to use.\n";
$conn->close();
?>
