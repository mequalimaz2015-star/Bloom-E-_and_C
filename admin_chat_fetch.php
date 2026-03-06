<?php
require_once 'session_init.php';
require_once 'db.php';

// Auth check
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    exit('Forbidden');
}

$sid = $_GET['sid'] ?? '';
$last_id = (int) ($_GET['last_id'] ?? 0);

if (!$sid) {
    echo json_encode(['success' => false, 'error' => 'No session ID']);
    exit;
}

// Fetch new messages
$stmt = $pdo->prepare("SELECT id, sender, message, image_path, location_lat, location_lng, created_at FROM chat_messages WHERE session_id = ? AND id > ? ORDER BY created_at ASC");
$stmt->execute([$sid, $last_id]);
$new_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark as read if there are new user messages
$has_user_msg = false;
foreach ($new_messages as $m) {
    if ($m['sender'] === 'User')
        $has_user_msg = true;
}
if ($has_user_msg) {
    $pdo->prepare("UPDATE chat_messages SET is_read = 1 WHERE session_id = ? AND sender = 'User'")->execute([$sid]);
}

// Optional: Fetch updated sidebar session info for all sessions (to update unread counts)
$dept_filter = $_GET['dept'] ?? 'All';
$dept_query = ($dept_filter !== 'All') ? "WHERE s.department = ?" : "";
$q = "SELECT m.session_id, MAX(m.id) as max_id, MAX(m.created_at) as last_msg, s.customer_name, 
      SUM(CASE WHEN m.sender = 'User' AND m.is_read = 0 THEN 1 ELSE 0 END) as unread_count
      FROM chat_messages m 
      LEFT JOIN chat_sessions s ON m.session_id = s.session_id
      $dept_query
      GROUP BY m.session_id ORDER BY last_msg DESC";
$stmt_side = $pdo->prepare($q);
if ($dept_filter !== 'All') {
    $stmt_side->execute([$dept_filter]);
} else {
    $stmt_side->execute();
}
$sessions = $stmt_side->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'messages' => $new_messages,
    'sessions' => $sessions,
    'active_sid' => $sid
]);
