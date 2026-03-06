import re

with open('chat_handler.php', 'rb') as f:
    content = f.read().decode('utf-8')

# Find the GET section and replace it
get_pattern = r"if \(\$_SERVER\['REQUEST_METHOD'\] === 'GET'\) \{.*?\}\r?\n\?>"

new_get = """if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Incremental poll: only return messages newer than last_id
    if (isset($_GET['poll']) && isset($_GET['last_id'])) {
        $last_id = (int) $_GET['last_id'];
        $stmt = $pdo->prepare("SELECT id, sender, message, image_path, location_lat, location_lng, created_at FROM chat_messages WHERE session_id = ? AND id > ? ORDER BY created_at ASC");
        $stmt->execute([$session_id, $last_id]);
        $new_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'new_messages' => $new_messages]);
        exit;
    }

    // Full history load
    $stmt = $pdo->prepare("SELECT * FROM chat_sessions WHERE session_id = ?");
    $stmt->execute([$session_id]);
    $session = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT id, sender, message, image_path, location_lat, location_lng, created_at FROM chat_messages WHERE session_id = ? ORDER BY created_at ASC");
    $stmt->execute([$session_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['registered' => !!$session, 'messages' => $messages, 'customer' => $session]);
}
?>"""

new_content, count = re.subn(get_pattern, new_get, content, flags=re.DOTALL)
if count > 0:
    with open('chat_handler.php', 'wb') as f:
        f.write(new_content.encode('utf-8'))
    print(f"SUCCESS: replaced {count} occurrence(s)")
else:
    print("NOT FOUND - pattern did not match")
    print("Last 300 chars:")
    print(repr(content[-300:]))
