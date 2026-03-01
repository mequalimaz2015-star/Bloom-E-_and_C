<?php
$pdo = new PDO('mysql:host=localhost;dbname=bloom_africa', 'root', '');

// ALTER reservations
try {
    $pdo->exec("ALTER TABLE reservations ADD COLUMN IF NOT EXISTS table_number INT AFTER reservation_time");
} catch (Exception $e) {
}

// Create tables table
$pdo->exec("CREATE TABLE IF NOT EXISTS tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number INT NOT NULL UNIQUE,
    capacity INT NOT NULL,
    status ENUM('Available', 'Occupied', 'Reserved') DEFAULT 'Available'
)");

// Seed tables if empty
$count = $pdo->query("SELECT COUNT(*) FROM tables")->fetchColumn();
if ($count == 0) {
    for ($i = 1; $i <= 15; $i++) {
        $capacity = ($i <= 5) ? 2 : (($i <= 10) ? 4 : 8);
        $pdo->exec("INSERT INTO tables (table_number, capacity) VALUES ($i, $capacity)");
    }
}

echo "Reservation migration complete.\n";
?>