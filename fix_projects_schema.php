<?php
require 'db.php';
try {
    // Check existing columns
    $stmt = $pdo->query("DESCRIBE construction_projects");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $needed_columns = [
        'name' => "VARCHAR(255) NOT NULL AFTER id",
        'description' => "TEXT AFTER name",
        'status' => "ENUM('Planning', 'Ongoing', 'Completed', 'On Hold') DEFAULT 'Planning' AFTER description",
        'image_url' => "VARCHAR(255) AFTER status",
        'start_date' => "DATE AFTER image_url",
        'completion_date' => "DATE AFTER start_date"
    ];

    foreach ($needed_columns as $col => $definition) {
        if (!in_array($col, $columns)) {
            $pdo->exec("ALTER TABLE construction_projects ADD COLUMN $col $definition");
            echo "Added column: $col <br>";
        }
    }

    // If 'title' exists but 'name' was missing, migrate data
    if (in_array('title', $columns) && in_array('name', $columns)) {
        $pdo->exec("UPDATE construction_projects SET name = title WHERE name IS NULL OR name = ''");
        echo "Migrated title to name <br>";
    }

    echo "Table schema check complete.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>