<?php
require_once 'db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS chat_sessions (
        session_id VARCHAR(255) PRIMARY KEY,
        customer_name VARCHAR(255) NOT NULL,
        customer_email VARCHAR(255) NOT NULL,
        customer_phone VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Chat sessions table created successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>