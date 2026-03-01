<?php
require_once 'db.php';
echo json_encode(['menu' => $pdo->query('SELECT DISTINCT category FROM menu_items')->fetchAll(PDO::FETCH_COLUMN), 'services' => $pdo->query('SELECT DISTINCT category FROM services')->fetchAll(PDO::FETCH_COLUMN)]);
?>