<?php
require_once 'db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS chat_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(255) NOT NULL,
        sender ENUM('User', 'Bot', 'Admin') NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Chat messages table created successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>