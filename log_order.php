<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'] ?? 'Unknown Item';
    $platform = $_POST['platform'] ?? 'Website';
    $price = $_POST['price'] ?? 0;

    try {
        $stmt = $pdo->prepare("INSERT INTO orders (order_details, total_amount, platform, status) VALUES (?, ?, ?, 'Chat Initiated')");
        $stmt->execute([$item_name, $price, $platform]);

        // Log activity for admin
        $log = $pdo->prepare("INSERT INTO activity_logs (action) VALUES (?)");
        $log->execute(["New chat-based order: $item_name via $platform"]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>