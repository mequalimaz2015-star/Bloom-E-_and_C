<?php
require_once 'db.php';
$stmt = $pdo->query("SELECT COUNT(*) FROM construction_features");
if ($stmt->fetchColumn() == 0) {
    $pdo->query("INSERT INTO construction_features (title, description, icon_class) VALUES 
        ('WE DELIVER QUALITY', 'We use premium materials and skilled labor to ensure your building stands the test of time.', 'fa-solid fa-leaf'),
        ('SAFETY FIRST', 'We prioritize the safety of our workers and the integrity of your site.', 'fa-solid fa-shield-cat'),
        ('ALWAYS ON TIME', 'Strict project management keeps us on schedule, every time.', 'fa-solid fa-futbol')
    ");
    echo "Inserted";
} else {
    echo "Already has records";
}
