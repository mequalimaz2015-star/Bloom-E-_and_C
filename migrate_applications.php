<?php
require_once 'db.php';

try {
    $check = $pdo->query("SHOW COLUMNS FROM job_applications LIKE 'photo_url'")->fetch();
    if (!$check) {
        $pdo->exec("ALTER TABLE job_applications ADD COLUMN photo_url VARCHAR(255) AFTER resume_url");
        echo "Added 'photo_url' column to job_applications.\n";
    } else {
        echo "'photo_url' column already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>