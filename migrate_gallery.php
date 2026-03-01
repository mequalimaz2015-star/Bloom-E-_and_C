<?php
$pdo = new PDO('mysql:host=localhost;dbname=bloom_africa', 'root', '');
$pdo->exec("CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    category VARCHAR(100) DEFAULT 'General',
    title VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "Gallery table created.\n";
?>