<?php
require_once 'db.php';

try {
    $check = $pdo->query("SHOW COLUMNS FROM services LIKE 'category'")->fetch();
    if (!$check) {
        $pdo->exec("ALTER TABLE services ADD COLUMN category ENUM('Food Delivery', 'Catering Service', 'Wedding Events', 'Birthday Parties', 'Corporate Events', 'Others') DEFAULT 'Others' AFTER icon");
        echo "Added 'category' column to services table.\n";
    } else {
        echo "'category' column already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>