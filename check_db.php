<?php
require 'db.php';
try {
    $stmt = $pdo->query("DESCRIBE users");
    while ($row = $stmt->fetch()) {
        echo $row['Field'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
