<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bloom_africa', 'root', '');
echo "--- Gallery ---\n";
$gal = $pdo->query('SELECT * FROM gallery ORDER BY id DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
print_r($gal);

echo "\n--- Construction Info ---\n";
$info = $pdo->query('SELECT * FROM construction_info WHERE id=1')->fetch(PDO::FETCH_ASSOC);
print_r($info);
?>