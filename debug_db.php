<?php
require_once 'db.php';
try {
    $res = $pdo->query("SELECT DATABASE()")->fetch();
    echo "Current DB: " . $res[0] . "\n";

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(", ", $tables) . "\n\n";

    foreach (['construction_services', 'services'] as $table) {
        if (in_array($table, $tables)) {
            echo "Schema for $table:\n";
            $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cols as $col) {
                echo "  " . $col['Field'] . " (" . $col['Type'] . ")\n";
            }
        } else {
            echo "$table NOT FOUND\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>