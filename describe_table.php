<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bloom_africa', 'root', '');
$res = $pdo->query("DESCRIBE construction_info")->fetchAll(PDO::FETCH_ASSOC);
foreach ($res as $row) {
    echo $row['Field'] . "\n";
}
?>