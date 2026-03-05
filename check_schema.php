<?php
require 'db.php';
$stmt = $pdo->query('SHOW COLUMNS FROM construction_info');
print_r(array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field'));
?>