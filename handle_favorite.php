<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = (int) $_POST['item_id'];
    $action = $_POST['action'] ?? 'like';

    if ($action === 'like') {
        // Increment likes
        $pdo->prepare("UPDATE menu_items SET likes = likes + 1 WHERE id = ?")->execute([$item_id]);
        // Set cookie so they can't spam or remember they liked it (30 days)
        setcookie('fav_' . $item_id, '1', time() + (86400 * 30), "/");
        // Log it as a general activity too - optional but user asked "admin see loved item"
        $dish = $pdo->query("SELECT name FROM menu_items WHERE id = $item_id")->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO activity_logs (action, admin_name) VALUES (?, ?)");
        $stmt->execute(["Customer loved dish: $dish", "System/Customer"]);
    } else {
        // Decrement likes
        $pdo->prepare("UPDATE menu_items SET likes = GREATEST(0, likes - 1) WHERE id = ?")->execute([$item_id]);
        setcookie('fav_' . $item_id, '', time() - 3600, "/");
    }
    echo json_encode(['success' => true]);
}
?>