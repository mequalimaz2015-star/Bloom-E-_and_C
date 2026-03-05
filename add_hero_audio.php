<?php
require 'db.php';
try {
    $pdo->exec("ALTER TABLE company_info ADD COLUMN hero_audio VARCHAR(255) DEFAULT NULL;");
    echo "Column hero_audio added to company_info successfully.\n";
} catch (PDOException $e) {
    echo "Error adding hero_audio: " . $e->getMessage() . "\n";
}
?>