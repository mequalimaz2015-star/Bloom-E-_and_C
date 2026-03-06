<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bloom_africa', 'root', '');
$res = $pdo->query("SELECT hero_image, hero_title, about_text FROM construction_info WHERE id=1")->fetch(PDO::FETCH_ASSOC);
print_r($res);
?>