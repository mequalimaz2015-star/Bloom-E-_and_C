<?php
include 'db.php';
$stmt = $pdo->query('DESCRIBE construction_info');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
}
