<?php
require 'db.php';
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN permissions TEXT AFTER role");
    echo "Permissions column added successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
