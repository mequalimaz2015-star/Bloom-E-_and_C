<?php
require 'db.php';
try {
    // Check existing columns
    $stmt = $pdo->query("DESCRIBE construction_equipment");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('serial_number', $columns)) {
        $pdo->exec("ALTER TABLE construction_equipment ADD COLUMN serial_number VARCHAR(100) AFTER name");
        echo "Added column: serial_number <br>";
    } else {
        echo "Column 'serial_number' already exists. <br>";
    }

    echo "Construction equipment schema fix complete.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>