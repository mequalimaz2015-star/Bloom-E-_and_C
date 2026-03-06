<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bloom_africa', 'root', '');
$res = $pdo->query("SELECT * FROM construction_info WHERE id=1")->fetch(PDO::FETCH_ASSOC);
foreach ($res as $k => $v) {
    echo "$k: $v\n";
}
?>