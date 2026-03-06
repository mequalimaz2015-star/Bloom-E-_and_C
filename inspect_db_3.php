<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bloom_africa', 'root', '');
echo "--- Construction Equipment (All) ---\n";
$eq = $pdo->query('SELECT * FROM construction_equipment')->fetchAll(PDO::FETCH_ASSOC);
foreach ($eq as $row) {
    echo "ID: {$row['id']} - Name: {$row['name']} - Image: {$row['image_url']}\n";
}
?>