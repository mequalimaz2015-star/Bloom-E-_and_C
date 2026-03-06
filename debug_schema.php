<?php
require 'db.php';
$stmt = $pdo->query("DESCRIBE construction_projects");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($columns, JSON_PRETTY_PRINT);
?>