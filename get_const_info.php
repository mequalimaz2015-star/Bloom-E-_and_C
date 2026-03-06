<?php
require_once 'db.php';
// Get all columns from construction_info
$stmt = $pdo->query("SHOW COLUMNS FROM construction_info");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "--- Construction Info Table Columns ---\n";
print_r($cols);

$data = $pdo->query("SELECT * FROM construction_info WHERE id=1")->fetch(PDO::FETCH_ASSOC);
echo "\n--- Construction Info Values (Local) ---\n";
print_r($data);
?>