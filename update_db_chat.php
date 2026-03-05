<?php
require 'db.php';
try {
    $pdo->exec("ALTER TABLE chat_sessions ADD COLUMN department VARCHAR(50) DEFAULT 'Restaurant'");
    echo "Success: Added department column";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>