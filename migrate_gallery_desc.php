<?php
require_once 'db.php';

try {
    $pdo->exec("ALTER TABLE gallery ADD COLUMN description TEXT AFTER title");
    echo "Successfully added description column to gallery table.\n";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column description already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>