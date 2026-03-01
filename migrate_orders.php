<?php
require_once 'db.php';

try {
    // Modify existing orders table if it exists
    $cols = $pdo->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('platform', $cols)) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN platform ENUM('Website', 'WhatsApp', 'Telegram') DEFAULT 'Website' AFTER total_amount");
        echo "Added 'platform' column.\n";
    }

    // Update ENUM for status
    $pdo->exec("ALTER TABLE orders MODIFY COLUMN status ENUM('Pending', 'Chat Initiated', 'Preparing', 'Ready', 'Delivered', 'Cancelled') DEFAULT 'Pending'");
    echo "Updated status ENUM.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>