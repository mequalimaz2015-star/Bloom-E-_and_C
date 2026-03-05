<?php
require_once 'db.php';

header('Content-Type: application/json');

$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

if (!$date || !$time) {
    echo json_encode([]);
    exit;
}

// Get all tables
$tables = $pdo->query("SELECT * FROM tables ORDER BY table_number ASC")->fetchAll(PDO::FETCH_ASSOC);

// Get reservations for the specific date and time
// For simplicity, we assume a reservation lasts for 2 hours.
$start_time = date('H:i:s', strtotime($time));
$end_time = date('H:i:s', strtotime($time . ' +2 hours'));

$stmt = $pdo->prepare("SELECT table_number FROM reservations 
                       WHERE reservation_date = ? 
                       AND status != 'Rejected'
                       AND (
                           (reservation_time <= ? AND DATE_ADD(reservation_time, INTERVAL 2 HOUR) > ?) OR
                           (reservation_time < ? AND DATE_ADD(reservation_time, INTERVAL 2 HOUR) >= ?)
                       )");
$stmt->execute([$date, $start_time, $start_time, $end_time, $end_time]);
$reserved_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

$result = [];
foreach ($tables as $table) {
    $table['is_available'] = !in_array($table['table_number'], $reserved_tables);
    $result[] = $table;
}

echo json_encode($result);
