<?php
require 'db.php';
try {
    $pdo->exec("ALTER TABLE construction_info ADD COLUMN hero_video VARCHAR(255) DEFAULT NULL");
    echo "Success: Added hero_video column to construction_info";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>