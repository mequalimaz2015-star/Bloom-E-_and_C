<?php
require_once 'db.php';

try {
    $pdo->exec("ALTER TABLE jobs ADD COLUMN closing_date DATE AFTER description");
    echo "Successfully added closing_date column to jobs table.\n";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column closing_date already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>