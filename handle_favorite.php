<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = (int) $_POST['item_id'];
    $action = $_POST['action'] ?? 'like';
    $email = $_POST['email'] ?? null;

    if ($action === 'like') {
        if (!$email) {
            echo json_encode(['success' => false, 'error' => 'Email is required']);
            exit;
        }

        // Check if already liked by this email to avoid duplicates
        $check = $pdo->prepare("SELECT COUNT(*) FROM favorites WHERE menu_item_id = ? AND customer_email = ?");
        $check->execute([$item_id, $email]);
        if ($check->fetchColumn() == 0) {
            // Insert into favorites
            $stmt = $pdo->prepare("INSERT INTO favorites (menu_item_id, customer_email) VALUES (?, ?)");
            $stmt->execute([$item_id, $email]);

            // Increment likes in menu_items
            $pdo->prepare("UPDATE menu_items SET likes = likes + 1 WHERE id = ?")->execute([$item_id]);

            // Log activity
            $dish = $pdo->query("SELECT name FROM menu_items WHERE id = $item_id")->fetchColumn();
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (action) VALUES (?)");
            $log_stmt->execute(["Customer ($email) loved dish: $dish"]);
        }

        // Set cookie (30 days)
        setcookie('fav_' . $item_id, '1', time() + (86400 * 30), "/");
        setcookie('customer_email', $email, time() + (86400 * 30), "/");

    } else {
        // Decrement likes
        $pdo->prepare("UPDATE menu_items SET likes = GREATEST(0, likes - 1) WHERE id = ?")->execute([$item_id]);

        // Remove from favorites table if email is known
        if ($email) {
            $pdo->prepare("DELETE FROM favorites WHERE menu_item_id = ? AND customer_email = ?")->execute([$item_id, $email]);
        }

        setcookie('fav_' . $item_id, '', time() - 3600, "/");
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>