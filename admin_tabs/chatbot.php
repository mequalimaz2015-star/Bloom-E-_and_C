<?php
$sessions = $pdo->query("SELECT m.session_id, MAX(m.created_at) as last_msg, s.customer_name, s.customer_phone,
                        SUM(CASE WHEN m.sender = 'User' AND m.is_read = 0 THEN 1 ELSE 0 END) as unread_count
                        FROM chat_messages m 
                        LEFT JOIN chat_sessions s ON m.session_id = s.session_id
                        GROUP BY m.session_id ORDER BY last_msg DESC")->fetchAll();
$active_sid = $_GET['sid'] ?? ($sessions[0]['session_id'] ?? '');

$messages = [];
$active_customer = null;
if ($active_sid) {
    $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE session_id = ? ORDER BY created_at ASC");
    $stmt->execute([$active_sid]);
    $messages = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM chat_sessions WHERE session_id = ?");
    $stmt->execute([$active_sid]);
    $active_customer = $stmt->fetch();

    // Mark as read
    $pdo->prepare("UPDATE chat_messages SET is_read = 1 WHERE session_id = ? AND sender = 'User'")->execute([$active_sid]);
}
?>

<div style="display: flex; gap: 30px; height: 75vh;">
    <!-- Chat Sidebar -->
    <div class="card" style="width: 320px; display: flex; flex-direction: column; overflow: hidden; padding: 0;">
        <div class="card-header" style="padding: 20px;"><span class="card-title">Live Conversations</span></div>
        <div style="flex: 1; overflow-y: auto;">
            <?php foreach ($sessions as $s): ?>
                <a href="?tab=chatbot&sid=<?= $s['session_id'] ?>"
                    style="display: block; padding: 15px 20px; text-decoration: none; border-bottom: 1px solid #f0f0f0; background: <?= ($active_sid == $s['session_id']) ? '#f8fafc' : 'transparent' ?>; border-left: 4px solid <?= ($active_sid == $s['session_id']) ? 'var(--blue)' : 'transparent' ?>;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                        <span style="font-weight: 600; color: #333; font-size: 13px;">
                            <?= $s['customer_name'] ? htmlspecialchars($s['customer_name']) : 'Guest #' . substr($s['session_id'], 0, 4) ?>
                        </span>
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                            <small
                                style="color: #94a3b8; font-size: 10px;"><?= date('H:i', strtotime($s['last_msg'])) ?></small>
                            <?php if ($s['unread_count'] > 0): ?>
                                <span class="nav-badge"
                                    style="margin: 0; padding: 2px 6px; font-size: 9px;"><?= $s['unread_count'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($s['customer_phone']): ?>
                        <div style="font-size: 11px; color: #64748b;"><i class="fa-solid fa-phone" style="font-size: 9px;"></i>
                            <?= htmlspecialchars($s['customer_phone']) ?></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
            <?php if (empty($sessions)): ?>
                <p style="padding: 20px; color: #888; text-align: center;">No active chats.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Chat Window -->
    <div class="card" style="flex: 1; display: flex; flex-direction: column; padding: 0; overflow: hidden;">
        <?php if ($active_sid): ?>
            <div class="card-header"
                style="background: #fdfdfd; padding: 15px 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div
                        style="width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #64748b; font-weight: 700;">
                        <?= strtoupper(substr($active_customer['customer_name'] ?? 'G', 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight: 700; color: #1e293b; font-size: 14px;">
                            <?= $active_customer['customer_name'] ? htmlspecialchars($active_customer['customer_name']) : 'Guest User' ?>
                        </div>
                        <div style="font-size: 11px; color: #64748b;">
                            <?= htmlspecialchars($active_customer['customer_email'] ?? 'No email provided') ?> •
                            <?= htmlspecialchars($active_customer['customer_phone'] ?? '') ?>
                        </div>
                    </div>
                </div>
                <div class="badge" style="background: #f1f5f9; color: #64748b;">Active Session</div>
            </div>

            <div id="chatBox"
                style="flex: 1; padding: 30px; overflow-y: auto; background: #fff; display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($messages as $m): ?>
                    <div
                        style="max-width: 70%; padding: 12px 18px; border-radius: 12px; font-size: 14px; line-height: 1.5; 
                         <?= ($m['sender'] == 'User') ? 'background: #f1f5f9; color: #1e293b; align-self: flex-start;' : 'background: var(--blue); color: #fff; align-self: flex-end;' ?>">
                        <strong><?= ($m['sender'] == 'User') ? htmlspecialchars($active_customer['customer_name'] ?? 'User') : 'Admin' ?>:</strong><br>
                        <?= nl2br(htmlspecialchars($m['message'])) ?>
                        <div style="font-size: 10px; opacity: 0.7; margin-top: 5px; text-align: right;">
                            <?= date('H:i', strtotime($m['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="padding: 20px; background: #f8fafc; border-top: 1px solid #f0f0f0;">
                <form method="POST" action="admin.php?tab=chatbot&sid=<?= $active_sid ?>" style="display: flex; gap: 15px;">
                    <input type="hidden" name="send_chat_reply" value="1">
                    <input type="hidden" name="session_id" value="<?= $active_sid ?>">
                    <input type="text" name="reply"
                        placeholder="Type your reply to <?= $active_customer['customer_name'] ?? 'customer' ?>..." required
                        style="flex: 1; padding: 12px 20px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <button type="submit" class="btn btn-primary" style="margin: 0; padding: 0 30px;"><i
                            class="fa-solid fa-paper-plane"></i> Send Reply</button>
                </form>
            </div>

            <script>
                const cb = document.getElementById('chatBox');
                cb.scrollTop = cb.scrollHeight;
            </script>
        <?php else: ?>
            <div
                style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8;">
                <i class="fa-solid fa-comment-dots" style="font-size: 50px; margin-bottom: 20px; opacity: 0.3;"></i>
                <p>Select a verified customer to start the conversation.</p>
            </div>
        <?php endif; ?>
    </div>
</div>