<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['chat_session'])) {
    $_SESSION['chat_session'] = session_id();
}

$session_id = $_SESSION['chat_session'];
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if it's a registration request
    if (isset($data['register'])) {
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');

        if ($name && $email && $phone) {
            $stmt = $pdo->prepare("INSERT INTO chat_sessions (session_id, customer_name, customer_email, customer_phone) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE customer_name=VALUES(customer_name), customer_email=VALUES(customer_email), customer_phone=VALUES(customer_phone)");
            $stmt->execute([$session_id, $name, $email, $phone]);

            // Welcome message
            $welcome = "Welcome $name! 🌟 I'm your Bloom assistant. What would you like to explore?";
            $stmt = $pdo->prepare("INSERT INTO chat_messages (session_id, sender, message) VALUES (?, 'Bot', ?)");
            $stmt->execute([$session_id, $welcome]);

            echo json_encode(['success' => true, 'reply' => $welcome, 'buttons' => ['🍽️ Food Menu', '🎉 Event Services', '👤 Talk to Human']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'All fields required']);
        }
        exit;
    }

    $user_msg = trim($data['message'] ?? '');
    if (empty($user_msg))
        exit;

    // Save user message
    $stmt = $pdo->prepare("INSERT INTO chat_messages (session_id, sender, message) VALUES (?, 'User', ?)");
    $stmt->execute([$session_id, $user_msg]);

    $bot_reply = "";
    $buttons = [];
    $msg_lower = strtolower($user_msg);

    // Normalize keywords for buttons or typed input
    if (strpos($msg_lower, 'hello') !== false || strpos($msg_lower, 'hi') !== false) {
        $bot_reply = "Hello! 👋 Welcome back to Bloom Africa. How can I pull together for you today?";
        $buttons = ['🍽️ Food Menu', '🎉 Event Services', '📍 Location', '👤 Talk to Human'];
    } elseif (strpos($msg_lower, 'food menu') !== false || $msg_lower == 'menu' || $msg_lower == 'food') {
        $cats = $pdo->query("SELECT DISTINCT category FROM menu_items WHERE available=1")->fetchAll(PDO::FETCH_COLUMN);
        $bot_reply = "Check out our delicious food! 🍗 Which category would you like to see?";
        $buttons = $cats;
        $buttons[] = "◀️ Main Menu";
    } elseif (strpos($msg_lower, 'event services') !== false || $msg_lower == 'service' || $msg_lower == 'wedding') {
        $cats = $pdo->query("SELECT DISTINCT category FROM services WHERE status='Active'")->fetchAll(PDO::FETCH_COLUMN);
        $bot_reply = "We specialize in making your events unforgettable! 💍 Choose a service category:";
        $buttons = $cats;
        $buttons[] = "◀️ Main Menu";
    } elseif (strpos($msg_lower, 'main menu') !== false || strpos($msg_lower, 'back to menu') !== false) {
        // Check Categories
        $is_cat = false;
        $menu_cats = $pdo->query("SELECT DISTINCT category FROM menu_items")->fetchAll(PDO::FETCH_COLUMN);
        $service_cats = $pdo->query("SELECT DISTINCT category FROM services")->fetchAll(PDO::FETCH_COLUMN);

        // Match Menu Category
        foreach ($menu_cats as $cat) {
            if (strpos($msg_lower, strtolower($cat)) !== false) {
                $is_cat = true;
                $stmt = $pdo->prepare("SELECT name, price FROM menu_items WHERE category = ? AND available=1");
                $stmt->execute([$cat]);
                $items = $stmt->fetchAll();
                $bot_reply = "Here are our dishes in **$cat**:\n";
                foreach ($items as $item) {
                    $bot_reply .= "• " . $item['name'] . " - " . number_format($item['price'], 2) . " ETB\n";
                    $buttons[] = "Order " . $item['name'];
                }
                $buttons[] = "🍽️ Other Food";
                $buttons[] = "◀️ Main Menu";
                break;
            }
        }

        // Match Service Category (e.g., Wedding Events)
        if (!$is_cat) {
            foreach ($service_cats as $cat) {
                if (strpos($msg_lower, strtolower($cat)) !== false || ($cat == "Wedding Events" && strpos($msg_lower, "wedding") !== false)) {
                    $is_cat = true;
                    $stmt = $pdo->prepare("SELECT title, description FROM services WHERE category = ? AND status='Active'");
                    $stmt->execute([$cat]);
                    $items = $stmt->fetchAll();
                    $bot_reply = "Our professional offerings for **$cat**:\n";
                    foreach ($items as $item) {
                        $bot_reply .= "💎 " . $item['title'] . "\n";
                        $buttons[] = "Enquire for " . $item['title'];
                    }
                    $buttons[] = "🎉 Other Services";
                    $buttons[] = "◀️ Main Menu";
                    break;
                }
            }
        }

        // Fallback or specific interactions
        if (!$is_cat) {
            if (strpos($msg_lower, 'order') !== false || strpos($msg_lower, 'enquire') !== false) {
                $stmt = $pdo->prepare("INSERT INTO orders (order_details, platform, status) VALUES (?, 'Website', 'Pending')");
                $stmt->execute(["Chatbot Order: " . $user_msg]);
                $bot_reply = "Perfect decision! ✅ I've logged your request: '$user_msg'. Our staff will confirm it with you shortly. Anything else?";
                $buttons = ['🍽️ Food Menu', '🎉 Event Services', '👤 Talk to Human'];
            } elseif (strpos($msg_lower, 'location') !== false || strpos($msg_lower, 'where') !== false) {
                $bot_reply = "We are located in Addis Ababa, Ethiopia! 🇪🇹 Visit us for the full experience. Would you like to see our menu?";
                $buttons = ['🍽️ Food Menu', '👤 Talk to Human'];
            } elseif (strpos($msg_lower, 'wait') !== false || strpos($msg_lower, 'staff') !== false || strpos($msg_lower, 'human') !== false) {
                $bot_reply = "I've sent an alert to our team! 🔔 Someone will join this chat soon. While you wait, check out our latest offers:";
                $buttons = ['🍽️ Food Menu', '🎉 Event Services'];
                $log = $pdo->prepare("INSERT INTO activity_logs (action) VALUES (?)");
                $log->execute(["Priority Chat Request from session: $session_id"]);
            } else {
                $bot_reply = "I'm still learning the Bloom way! 🧠 Please choose an option below to get started:";
                $buttons = ['🍽️ Food Menu', '🎉 Event Services', '👤 Talk to Human'];
            }
        }
    }

    // Save Bot message
    if ($bot_reply) {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (session_id, sender, message) VALUES (?, 'Bot', ?)");
        $stmt->execute([$session_id, $bot_reply]);
    }
    echo json_encode(['success' => true, 'reply' => $bot_reply, 'buttons' => $buttons]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM chat_sessions WHERE session_id = ?");
    $stmt->execute([$session_id]);
    $session = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT sender, message, created_at FROM chat_messages WHERE session_id = ? ORDER BY created_at ASC");
    $stmt->execute([$session_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['registered' => !!$session, 'messages' => $messages, 'customer' => $session]);
}
?>