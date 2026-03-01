<?php
require_once 'db.php';

// Seed initial admin user if table is empty
$check_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ($check_users == 0) {
    $hashed_pw = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['Bloom Admin', 'admin@bloomafrica.com', $hashed_pw]);
    echo "Initial admin user created.\n";
} else {
    echo "Users table already has data.\n";
}
?>