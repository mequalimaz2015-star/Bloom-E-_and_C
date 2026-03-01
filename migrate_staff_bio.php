<?php
require_once 'db.php';

try {
    $pdo->exec("ALTER TABLE employees ADD COLUMN bio TEXT AFTER role");
    echo "Successfully added bio column to employees table.\n";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column bio already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>