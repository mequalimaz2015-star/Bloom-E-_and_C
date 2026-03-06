<?php
require 'db.php';
$tables_title = ['construction_services', 'construction_projects', 'jobs', 'services', 'gallery', 'construction_features'];
foreach ($tables_title as $t) {
    try {
        $affected = $pdo->exec("DELETE t1 FROM $t t1 INNER JOIN $t t2 WHERE t1.id > t2.id AND t1.title = t2.title");
        echo "Cleaned $affected from $t\n";
    } catch (Exception $e) {
    }
}

$tables_name = ['menu_items', 'team_members', 'construction_equipment'];
foreach ($tables_name as $t) {
    $col = ($t == 'construction_equipment') ? 'serial_number' : 'name';
    try {
        $affected = $pdo->exec("DELETE t1 FROM $t t1 INNER JOIN $t t2 WHERE t1.id > t2.id AND t1.$col = t2.$col");
        echo "Cleaned $affected from $t\n";
    } catch (Exception $e) {
    }
}

try {
    $affected = $pdo->exec("DELETE t1 FROM construction_testimonials t1 INNER JOIN construction_testimonials t2 WHERE t1.id > t2.id AND t1.client_name = t2.client_name");
    echo "Cleaned $affected from construction_testimonials\n";
} catch (Exception $e) {
}
?>