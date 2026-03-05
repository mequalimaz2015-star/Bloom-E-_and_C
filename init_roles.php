<?php
require 'db.php';
try {
    // Set the first user as Admin if role is NULL or Waiter
    $pdo->exec("UPDATE users SET role = 'Admin' WHERE id = 1");
    // Ensure all users have a role and permissions (default to Waiter if missing)
    $pdo->exec("UPDATE users SET role = 'Waiter' WHERE role IS NULL OR role = ''");
    $pdo->exec("UPDATE users SET permissions = '[]' WHERE permissions IS NULL OR permissions = ''");
    echo "Database roles and permissions initialized!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
