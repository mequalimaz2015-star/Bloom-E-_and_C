<?php
require_once 'db.php';
try {
    // Change closing_date to DATETIME to support time
    $pdo->exec("ALTER TABLE jobs MODIFY COLUMN closing_date DATETIME");
    echo "Jobs table updated: closing_date changed to DATETIME.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>