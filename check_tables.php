<?php
require_once 'db.php';

header('Content-Type: application/json');

$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

if (!$date || !$time) {
    echo json_encode([]);
    exit;
}

// Fetch all tables
$stmt = $pdo->query("SELECT * FROM tables ORDER BY table_number");
$all_tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch reserved tables for this date/time
$stmt = $pdo->prepare("SELECT table_number FROM reservations WHERE reservation_date = ? AND reservation_time = ? AND status != 'Rejected'");
$stmt->execute([$date, $time]);
$reserved_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

$available_tables = [];
foreach ($all_tables as $table) {
    $table['is_available'] = !in_array($table['table_number'], $reserved_tables);
    $available_tables[] = $table;
}

echo json_encode($available_tables);
?>