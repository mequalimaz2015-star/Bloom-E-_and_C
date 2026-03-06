<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bloom_africa', 'root', '');
$res = $pdo->query('SELECT * FROM company_info')->fetch(PDO::FETCH_ASSOC);
print_r($res);

echo "\n--- Equipment ---\n";
$eq = $pdo->query('SELECT * FROM construction_equipment')->fetchAll(PDO::FETCH_ASSOC);
print_r($eq);
?>