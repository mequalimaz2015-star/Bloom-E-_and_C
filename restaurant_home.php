<?php
require_once 'db.php';
// Fetch menu items
$stmt = $pdo->query("SELECT * FROM menu_items WHERE available = 1");
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch Gallery items
$stmt = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
$gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get unique gallery categories for filters
$gallery_categories = $pdo->query("SELECT DISTINCT category FROM gallery")->fetchAll(PDO::FETCH_COLUMN);

// Fetch Services
$services_list = $pdo->query("SELECT * FROM services WHERE status='Active' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
// Get unique service categories for navigation and filters
$service_categories = $pdo->query("SELECT DISTINCT category FROM services WHERE status='Active' AND category IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
// Handle Reservation Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reserve_action'])) {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $guests = $_POST['guests'] ?? '';
    $table_number = $_POST['table_number'] ?? null;
    // Check if table is actually available (Server-side validation)
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE reservation_date = ? AND reservation_time = ? AND table_number = ? AND status != 'Rejected'");
    $check_stmt->execute([$date, $time, $table_number]);
    $is_booked = $check_stmt->fetchColumn();
    if ($is_booked > 0) {
        $error_msg = "Error: This table is already reserved for the selected date and time.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO reservations (customer_name, phone, reservation_date, reservation_time, guests, table_number) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $phone, $date, $time, $guests, $table_number])) {
            $msg = "Reservation submitted successfully! Your table #$table_number is locked.";
        }
    }
}
// Fetch Team Members
$team_members = $pdo->query("SELECT * FROM team_members ORDER BY order_index ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Company Info
$company = $pdo->query("SELECT * FROM company_info WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
// Handle Job Application Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_job'])) {
    $job_id = $_POST['job_id'];
    $app_name = $_POST['applicant_name'];
    $app_email = $_POST['email'];
    $app_phone = $_POST['phone'];
    $app_gpa = $_POST['gpa'] ?? null;
    $app_message = $_POST['message'] ?? '';
    $resume_url = "";
    $photo_url = "";
    // ... (rest of the upload logic, I'll keep it exactly the same)
    // Handle Resume (CV) Upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $filename = $_FILES['resume']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $upload_dir = "uploads/applications/resumes/";
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0777, true);
            $new_filename = "cv_" . time() . "_" . uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $upload_dir . $new_filename)) {
                $resume_url = $upload_dir . $new_filename;
            }
        }
    }

    // Handle Photo Upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $upload_dir = "uploads/applications/photos/";
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0777, true);
            $new_filename = "photo_" . time() . "_" . uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $new_filename)) {
                $photo_url = $upload_dir . $new_filename;
            }
        }
    }
    $stmt = $pdo->prepare("INSERT INTO job_applications (job_id, applicant_name, email, phone, gpa, resume_url, photo_url, cover_letter) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$job_id, $app_name, $app_email, $app_phone, $app_gpa, $resume_url, $photo_url, $app_message])) {
        $msg = "Application submitted successfully! We will contact you soon.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloom Africa Restaurant | Best Ethiopian Restaurant in Addis Ababa</title>
    <meta name="description"
        content="Bloom Africa Restaurant offers an authentic and modern dining experience with the finest African dishes in Addis Ababa. Book a table or order online today.">
    <meta name="keywords"
        content="Ethiopian Restaurant, Addis Ababa, Doro Wat, Kitfo, Traditional African Cuisine, Best Restaurant, Order Online">

    <!-- Open Graph for Social Media -->
    <meta property="og:title" content="Bloom Africa Restaurant | Best Ethiopian Restaurant in Addis Ababa">
    <meta property="og:description"
        content="A modern and authentic dining experience fusing rich African heritage with contemporary culinary arts.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://bloomafrica.com">
    <meta property="og:image"
        content="https://images.unsplash.com/photo-1544148103-0773bb108726?q=80&w=2070&auto=format&fit=crop">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,600;1,600&display=swap"
        rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #dfb180;
            --primary-dark: #c29565;
            --accent: #9a6852;
            --bg-dark: #fbf8f5;
            /* Flipped to white-ish */
            --text-light: #1a1512;
            /* Flipped to charcoal */
            --bg-section: rgba(223, 177, 128, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #fbf8f5;
            /* Warm Porcelain - Premium Light Theme */
            color: #1a1512;
            scroll-behavior: smooth;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, #ffffff, #fdfaf7);
            z-index: 999999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.8s;
            gap: 60px;
        }

        .preloader.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        .loader-container {
            position: relative;
            width: 120px;
            height: 120px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader-circle {
            width: 80px;
            height: 80px;
            border: 4px solid rgba(223, 177, 128, 0.1);
            border-top: 4px solid var(--primary);
            border-bottom: 4px solid var(--primary);
            border-radius: 50%;
            animation: rotateLoader 1.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
        }

        .loader-inner {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 3px solid transparent;
            border-left: 3px solid rgba(223, 177, 128, 0.3);
            border-right: 3px solid rgba(223, 177, 128, 0.3);
            border-radius: 50%;
            animation: rotateLoader 1s linear infinite reverse;
            opacity: 0.8;
        }

        @keyframes rotateLoader {
            0% {
                transform: rotate(0deg) scale(1);
            }

            50% {
                transform: rotate(180deg) scale(1.15);
            }

            100% {
                transform: rotate(360deg) scale(1);
            }
        }

        .loader-text {
            position: absolute;
            bottom: -30px;
            font-size: 11px;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--primary);
            font-weight: 900;
            opacity: 0.9;
            animation: pulseText 2s ease-in-out infinite;
        }

        @keyframes pulseText {

            0%,
            100% {
                opacity: 0.4;
                transform: translateY(0);
                letter-spacing: 5px;
            }

            50% {
                opacity: 1;
                transform: translateY(-2px);
                letter-spacing: 7px;
            }
        }

        /* Animated Background Layer */
        .site-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -10;
            background: linear-gradient(to bottom, #fbf8f5, #f5f0eb);
        }

        .bg-illustration {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: -9;
            background: url('bloom_bg_premium.png') center/cover no-repeat;
            opacity: 1;
            filter: brightness(0.7) contrast(1.1) saturate(1.1);
            /* Slightly darker to make text pop more */
            animation: panBackground 180s linear infinite alternate;
            pointer-events: none;
        }

        @keyframes panBackground {
            0% {
                transform: scale(1.1) translate(0, 0);
            }

            100% {
                transform: scale(1.2) translate(-2%, -2%);
            }
        }

        /* Twinkling Stars */
        .stars-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -8;
            pointer-events: none;
        }

        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            opacity: 0;
            animation: twinkle var(--duration) ease-in-out infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0;
                transform: scale(0.5);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.2);
            }
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Playfair Display', serif;
        }

        /* Navigation */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            background: rgba(42, 31, 24, 0.8);
            /* Warm Brown */
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(239, 177, 128, 0.1);
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 400;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ── Nav Dropdown – Click-toggle accordion ── */
        .nav-dropdown {
            position: relative;
            display: inline-block;
        }

        .nav-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 7px;
            background: none;
            border: none;
            color: #fff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            padding: 6px 4px;
            letter-spacing: 0.3px;
            transition: color 0.2s;
        }

        .nav-dropdown-btn:hover {
            color: var(--primary);
        }

        .nav-dropdown-btn .nd-icon {
            font-size: 14px;
            color: var(--primary);
            opacity: 0.85;
        }

        .nav-dropdown-btn .nd-chevron {
            font-size: 10px;
            margin-left: 2px;
            transition: transform 0.3s ease;
        }

        .nav-dropdown.open .nd-chevron {
            transform: rotate(180deg);
        }

        /* The panel */
        .nav-dropdown-panel {
            display: none;
            position: absolute;
            top: calc(100% + 12px);
            left: 50%;
            transform: translateX(-50%);
            background: #1a1512;
            border: 1px solid rgba(223, 177, 128, 0.2);
            border-radius: 14px;
            min-width: 230px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            z-index: 9999;
            overflow: hidden;
            animation: ndFadeIn 0.2s ease;
        }

        .nav-dropdown.open .nav-dropdown-panel {
            display: block;
        }

        @keyframes ndFadeIn {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* Arrow tip */
        .nav-dropdown-panel::before {
            content: '';
            position: absolute;
            top: -7px;
            left: 50%;
            transform: translateX(-50%) rotate(45deg);
            width: 13px;
            height: 13px;
            background: #1a1512;
            border-left: 1px solid rgba(223, 177, 128, 0.2);
            border-top: 1px solid rgba(223, 177, 128, 0.2);
        }

        /* Panel header label */
        .nd-panel-header {
            padding: 14px 18px 10px;
            font-size: 10px;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 1px solid rgba(223, 177, 128, 0.1);
        }

        /* Each link row */
        .nav-dropdown-panel a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 18px;
            color: rgba(255, 255, 255, 0.85) !important;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.18s, color 0.18s, padding-left 0.18s;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        .nav-dropdown-panel a:last-child {
            border-bottom: none;
        }

        .nav-dropdown-panel a .nd-item-icon {
            width: 30px;
            height: 30px;
            background: rgba(223, 177, 128, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: var(--primary);
            flex-shrink: 0;
            transition: background 0.18s;
        }

        .nav-dropdown-panel a:hover {
            background: rgba(223, 177, 128, 0.08);
            color: var(--primary) !important;
            padding-left: 22px;
        }

        .nav-dropdown-panel a:hover .nd-item-icon {
            background: rgba(223, 177, 128, 0.2);
        }

        @keyframes slideUp {}

        .chat-header {
            padding: 20px;
            background: #dfb180;
            color: #000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .chat-bubble {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 15px;
            font-size: 14px;
            line-height: 1.5;
        }

        .chat-bubble.user {
            align-self: flex-end;
            background: rgba(223, 177, 128, 0.2);
            color: #dfb180;
            border-bottom-right-radius: 2px;
        }

        .chat-bubble.bot,
        .chat-bubble.admin {
            align-self: flex-start;
            background: #251d18;
            color: #fff;
            border-bottom-left-radius: 2px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .chat-bubble.admin {
            border-left: 3px solid #dfb180;
        }

        .chat-options {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chat-option-btn {
            background: rgba(223, 177, 128, 0.05);
            color: #dfb180;
            border: 1px solid rgba(223, 177, 128, 0.4);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Inter', sans-serif;
            font-weight: 500;
        }

        .chat-option-btn:hover {
            background: #dfb180;
            color: #000;
            border-color: #dfb180;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(223, 177, 128, 0.2);
        }

        .chat-input-area {
            padding: 15px;
            display: flex;
            gap: 10px;
            background: #251d18;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .chat-input-area input {
            flex: 1;
            padding: 10px 15px;
            background: #1a1512;
            border: 1px solid #444;
            border-radius: 20px;
            color: #fff;
            outline: none;
            font-size: 14px;
        }

        .chat-send-btn {
            background: #dfb180;
            color: #000;
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: 0.2s;
        }

        .chat-send-btn:hover {
            background: #fff;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Services Section Styles */
        .services-section {
            padding: 100px 5%;
            background: transparent;
            /* Changed from solid color */
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .service-card {
            background: #1a1512;
            border-radius: 20px;
            overflow: hidden;
            transition: 0.4s;
            border: 1px solid rgba(223, 177, 128, 0.1);
            position: relative;
        }

        .service-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
            box-shadow: 0 10px 30px rgba(223, 177, 128, 0.1);
        }

        .service-media {
            height: 220px;
            position: relative;
            overflow: hidden;
        }

        .service-media img,
        .service-media video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }

        .service-card:hover .service-media img {
            transform: scale(1.1);
        }

        .service-info {
            padding: 25px;
            text-align: center;
        }

        .service-icon {
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: -55px auto 20px;
            position: relative;
            z-index: 2;
            box-shadow: 0 5px 15px rgba(223, 177, 128, 0.3);
        }

        .service-info h3 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .service-info p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 14px;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        /* Careers Section */
        .careers-section {
            padding: 80px 10%;
            background: transparent;
            /* Transparent to show background */
        }

        .job-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .job-card {
            background: #251d18;
            padding: 30px;
            border-radius: 15px;
            border: 1px solid rgba(239, 177, 128, 0.1);
            transition: 0.3s;
        }

        .job-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

        .job-card h3 {
            color: var(--primary);
            margin-bottom: 10px;
        }

        .job-meta {
            display: flex;
            gap: 15px;
            font-size: 0.9rem;
            color: #beb0b0ff;
            margin-bottom: 15px;
        }

        .job-desc {
            font-size: 0.95rem;
            color: #ddd;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .apply-btn {
            display: inline-block;
            background: var(--primary);
            color: #120e0c;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            cursor: pointer;
            border: none;
        }

        .apply-btn:hover {
            background: var(--primary-dark);
        }

        .app-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .app-modal-content {
            background: #1e1814;
            padding: 40px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            position: relative;
            border: 1px solid var(--primary);
        }

        .close-app {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
        }

        .btn-reserve {
            background: var(--primary);
            color: #121212;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-reserve:hover {
            background: var(--text-light);
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 20px rgba(223, 177, 128, 0.4);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(42, 31, 24, 0), rgba(42, 31, 24, 0.2));
            /* Almost fully clear */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 20px;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 4.5rem;
            margin-bottom: 20px;
            color: var(--primary);
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 600px;
            margin-bottom: 40px;
            color: #ddd;
        }

        /* Section Global */
        section {
            padding: 100px 5%;
        }

        /* About Section */
        #about {
            background-color: transparent;
        }

        .about-top {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 80px auto;
        }

        .about-top h2 {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .about-top p {
            color: #ddd;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .about-images {
            position: relative;
            height: 550px;
            perspective: 1000px;
        }

        .about-img-main,
        .about-img-sub1,
        .about-img-sub2 {
            position: absolute;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .about-img-main {
            width: 55%;
            height: 80%;
            left: 5%;
            top: 10%;
            z-index: 1;
            transform: rotateY(15deg) translateZ(-50px);
        }

        .about-img-sub1 {
            width: 45%;
            height: 40%;
            right: 0;
            top: 5%;
            z-index: 2;
            border: 6px solid var(--bg-section);
            transform: rotateY(-10deg) translateZ(80px);
        }

        .about-img-sub2 {
            width: 48%;
            height: 45%;
            right: 15%;
            bottom: 5%;
            z-index: 3;
            border: 6px solid rgba(255, 255, 255, 0.05);
            transform: rotateY(-5deg) translateZ(120px) rotateX(5deg);
        }

        .about-images:hover .about-img-main {
            transform: rotateY(0deg) translateZ(0px) scale(1.05);
            z-index: 4;
        }

        .about-images:hover .about-img-sub1 {
            transform: rotateY(0deg) translateZ(20px) translate(-10%, 10%);
        }

        .about-images:hover .about-img-sub2 {
            transform: rotateY(0deg) translateZ(40px) translate(10%, -10%);
        }

        .about-text h2 {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .about-text h3 {
            font-size: 1.3rem;
            color: #fff;
            margin-bottom: 25px;
            font-weight: 400;
            line-height: 1.5;
        }

        .about-text p {
            color: #aaa;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        @media(max-width: 992px) {
            .about-content {
                grid-template-columns: 1fr;
            }

            .about-images {
                height: 500px;
                margin-bottom: 40px;
            }

            .about-img-main {
                width: 70%;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        /* Floating Social Buttons */
        .float-btn-group {
            position: fixed;
            bottom: 40px;
            right: 40px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            align-items: center;
            z-index: 1000;
        }

        .whatsapp-float,
        .telegram-float {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            text-align: center;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #FFF;
            position: relative;
        }

        .whatsapp-float {
            background-color: #25d366;
            box-shadow: 0px 4px 15px rgba(37, 211, 102, 0.4);
        }

        .whatsapp-float:hover {
            transform: scale(1.15) translateY(-4px);
            box-shadow: 0px 8px 25px rgba(37, 211, 102, 0.6);
            color: #fff;
        }

        .telegram-float {
            background-color: #229ED9;
            box-shadow: 0px 4px 15px rgba(34, 158, 217, 0.4);
        }

        .telegram-float:hover {
            transform: scale(1.15) translateY(-4px);
            box-shadow: 0px 8px 25px rgba(34, 158, 217, 0.6);
            color: #fff;
        }

        /* Tooltip labels */
        .float-btn-group a::before {
            content: attr(data-tooltip);
            position: absolute;
            right: 72px;
            background: rgba(0, 0, 0, 0.75);
            color: #fff;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-family: 'Outfit', sans-serif;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .float-btn-group a:hover::before {
            opacity: 1;
        }

        /* Gallery Section */
        .gallery {
            padding-top: 50px;
            padding-bottom: 100px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 40px;
        }


        @media(max-width: 576px) {
            .gallery-grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* Gallery Filter Styles */
        .gallery-filters {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(223, 177, 128, 0.2);
            color: #fff;
            padding: 10px 25px;
            border-radius: 30px;
            cursor: pointer;
            transition: 0.3s;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary);
            color: #121212;
            border-color: var(--primary);
            box-shadow: 0 5px 15px rgba(223, 177, 128, 0.3);
        }

        /* Premium Gallery Card Layout (User's Requested Style) */
        #gallery_section {
            background: #fdfaf7;
            /* Creamy light background for contrast */
            padding: 100px 5% !important;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 35px;
            margin-top: 50px;
        }

        .gallery-card {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0, 0, 0, 0.03);
            text-align: left;
            perspective: 1000px;
        }

        .gallery-card:hover {
            transform: translateY(-15px) rotateX(2deg) rotateY(-2deg);
            box-shadow: -10px 25px 60px rgba(0, 0, 0, 0.15);
        }

        .gallery-card-media {
            height: 260px;
            overflow: hidden;
            position: relative;
            transform-style: preserve-3d;
        }

        .gallery-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
            transform: translateZ(20px);
        }

        .gallery-card:hover .gallery-card-media img {
            transform: scale(1.1) translateZ(40px);
        }

        .gallery-card-content {
            padding: 25px 30px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .gallery-card-content h4 {
            color: #1a1512;
            font-size: 1.4rem;
            font-family: 'Playfair Display', serif;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .gallery-card-content p {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 25px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            opacity: 0.85;
        }

        .gallery-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f5f5f5;
            padding-top: 20px;
            margin-top: auto;
        }

        .gallery-card-footer .date {
            font-size: 0.85rem;
            color: #999;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .gallery-card-footer .read-more {
            color: #e74c3c;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .gallery-card-footer .read-more:hover {
            gap: 10px;
            color: #c0392b;
        }

        .gallery-card.hidden {
            display: none;
        }

        .section-title {
            text-align: center;
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 50px;
        }

        /* Menu Section */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .menu-item {
            background: rgba(26, 21, 18, 0.4);
            /* Transparent */
            backdrop-filter: blur(5px);
            border: 1px solid rgba(223, 177, 128, 0.1);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            position: relative;
        }

        .menu-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
        }

        .menu-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-bottom: 4px solid var(--primary);
        }

        .menu-content {
            padding: 25px;
        }

        .menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .menu-header h3 {
            font-size: 1.5rem;
            margin: 0;
        }

        .price {
            color: var(--primary);
            font-weight: 800;
            font-size: 1.2rem;
        }

        .menu-desc {
            color: #aaa;
            font-size: 0.95rem;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .menu-order-btns {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .buy-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #25D366;
            color: white;
            padding: 10px 18px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .buy-btn:hover {
            background: #1DA851;
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 10px 20px rgba(37, 211, 102, 0.4);
        }

        .telegram-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #229ED9;
            color: white;
            padding: 10px 18px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .telegram-btn:hover {
            background: #1a7fb5;
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 10px 20px rgba(34, 158, 217, 0.4);
        }

        .fav-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(223, 177, 128, 0.3);
            color: #fff;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 1.2rem;
        }

        .fav-btn:hover {
            background: #fff;
            color: #e74c3c;
            transform: scale(1.2);
            border-color: #e74c3c;
        }

        .fav-btn.active {
            background: #fff;
            color: #e74c3c;
            border-color: #e74c3c;
            box-shadow: 0 0 15px rgba(231, 76, 60, 0.4);
        }

        /* Reservation */
        .reservation {
            background: rgba(26, 21, 18, 0.6);
            /* Transparent glass effect */
            backdrop-filter: blur(10px);
            border: 1px solid rgba(223, 177, 128, 0.2);
            border-radius: 20px;
            padding: 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .res-info h2 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .submit-btn {
            background: var(--primary);
            color: #121212;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: var(--text-light);
            color: var(--primary-dark);
            transform: translateY(-5px) scale(1.03);
            box-shadow: 0 10px 20px rgba(223, 177, 128, 0.4);
        }

        /* Map */
        .map-section {
            padding: 0;
            height: 400px;
            filter: grayscale(80%) invert(100%) hue-rotate(180deg);
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Footer */
        footer {
            background: rgba(5, 7, 10, 0.8);
            /* Transparent dark navy */
            backdrop-filter: blur(10px);
            padding: 50px 5%;
            text-align: center;
            border-top: 1px solid rgba(239, 177, 128, 0.1);
        }

        .socials {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .socials a {
            color: #fff;
            font-size: 1.5rem;
            transition: 0.3s;
        }

        .socials a:hover {
            color: var(--primary);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 40px 8%;
            background: rgba(0, 0, 0, 0.6);
            border-top: 2px solid rgba(239, 177, 128, 0.1);
            font-size: 1.1rem;
            color: #ccc;
            flex-wrap: wrap;
            gap: 30px;
        }

        .footer-social-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .social-icon-btn {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(223, 177, 128, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
        }

        .social-icon-btn:hover {
            background: var(--primary);
            color: #000;
            border-color: var(--primary);
            transform: translateY(-5px) rotate(8deg);
            box-shadow: 0 5px 15px rgba(223, 177, 128, 0.4);
        }

        .developer-credit {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .dev-photo {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: 2px solid var(--primary);
            object-fit: cover;
            box-shadow: 0 0 20px rgba(223, 177, 128, 0.3);
            transition: 0.3s;
        }

        .dev-photo:hover {
            transform: scale(1.1);
            border-color: #fff;
        }

        @media (max-width: 768px) {
            .footer-bottom {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
        }

        /* Enhanced Notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2ecc71;
            color: #fff;
            padding: 20px 40px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            z-index: 10000;
            box-shadow: 0 15px 35px rgba(46, 204, 113, 0.3);
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideInRight 0.5s cubic-bezier(0.18, 0.89, 0.32, 1.28) forwards;
        }

        .notification i {
            font-size: 1.5rem;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(120%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                transform: translateY(-20px);
                opacity: 0;
            }
        }

        @media(max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 3rem;
            }

            .reservation {
                grid-template-columns: 1fr;
                padding: 30px;
            }
        }

        /* Chatbot CSS */
        .float-btn-group {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 10000;
        }

        .chat-toggle {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: #dfb180;
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            transition: 0.3s;
            pointer-events: auto;
        }

        .chat-toggle:hover {
            transform: scale(1.1) rotate(10deg);
        }

        .chatbot-container {
            position: fixed;
            bottom: 100px;
            /* Above the toggles */
            right: 30px;
            z-index: 10001;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            font-family: 'Inter', sans-serif;
            pointer-events: none;
        }

        .chat-window {
            width: 350px;
            height: 500px;
            background: #1a1512;
            border-radius: 20px;
            margin-bottom: 20px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(223, 177, 128, 0.1);
            animation: slideUp 0.4s ease;
            pointer-events: auto;
        }

        /* Favorite Modal Styles */
        .fav-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 11000;
        }

        .fav-modal-content {
            background: #1e1814;
            padding: 40px;
            border-radius: 24px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            border: 1px solid var(--primary);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            animation: favSlideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes favSlideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .fav-modal h2 {
            color: var(--primary);
            margin-bottom: 15px;
            font-family: 'Playfair Display', serif;
        }

        .fav-modal p {
            color: #ccc;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .fav-input {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            margin-bottom: 20px;
            font-family: inherit;
            outline: none;
            text-align: center;
        }

        .fav-input:focus {
            border-color: var(--primary);
        }

        .fav-submit-btn {
            background: var(--primary);
            color: #000;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }

        .fav-submit-btn:hover {
            background: #fff;
            transform: scale(1.02);
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <style>
        .hero-carousel {
            height: 100vh;
            width: 100%;
        }

        .hero-slide {
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }

        .hero-slide::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .hero-content-wrapper {
            position: relative;
            z-index: 2;
            padding: 0 20px;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: var(--primary);
        }

        .swiper-pagination-bullet-active {
            background: var(--primary);
        }
    </style>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="loader-container">
            <div class="loader-circle"></div>
            <div class="loader-inner"></div>
            <div class="loader-text">Bloom</div>
        </div>
    </div>

    <div class="site-background"></div>
    <div class="bg-illustration"></div>
    <div class="stars-container" id="starsContainer"></div>

    <script>
        // Generate twinkling stars
        const container = document.getElementById('starsContainer');
        const starCount = 150;
        for (let i = 0; i < starCount; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            const size = Math.random() * 2 + 1;
            star.style.width = `${size}px`;
            star.style.height = `${size}px`;
            star.style.left = `${Math.random() * 100}%`;
            star.style.top = `${Math.random() * 100}%`;
            star.style.setProperty('--duration', `${Math.random() * 3 + 2}s`);
            star.style.animationDelay = `${Math.random() * 5}s`;
            container.appendChild(star);
        }
    </script>

    <?php if (!empty($msg)): ?>
        <div class="notification" id="appNotification">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <strong style="display: block; font-size: 1.1rem;">Success!</strong>
                <span style="opacity: 0.9; font-weight: 400;"><?= htmlspecialchars($msg) ?></span>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const notify = document.getElementById('appNotification');
                if (notify) {
                    notify.style.animation = 'fadeOut 0.6s forwards';
                    setTimeout(() => notify.remove(), 600);
                }
            }, 6000);
        </script>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="notification" style="background: #e74c3c;">
            <?= htmlspecialchars($error_msg) ?>
        </div>
    <?php endif; ?>

    <nav>
        <a href="#" class="logo"><i class="fa-solid fa-leaf"></i> Bloom Africa</a>
        <div class="nav-links">

            <!-- Home Dropdown -->
            <div class="nav-dropdown" id="nd-home">
                <button class="nav-dropdown-btn" onclick="toggleNavDropdown('nd-home')">
                    <i class="fa-solid fa-house nd-icon"></i>
                    Home
                    <i class="fa-solid fa-chevron-down nd-chevron"></i>
                </button>
                <div class="nav-dropdown-panel">
                    <div class="nd-panel-header">Select View</div>
                    <a href="index.php">
                        <span class="nd-item-icon"><i class="fa-solid fa-images"></i></span>
                        Home Carousel
                    </a>
                    <a href="home_image.php">
                        <span class="nd-item-icon"><i class="fa-regular fa-image"></i></span>
                        Home Image
                    </a>
                    <a href="home_video.php">
                        <span class="nd-item-icon"><i class="fa-solid fa-video"></i></span>
                        Home Video
                    </a>
                    <div style="border-top: 1px solid rgba(255,255,255,0.08); margin-top: 5px;"></div>
                    <a href="Construction/index.php" style="color: #f39c12 !important;">
                        <span class="nd-item-icon" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;">
                            <i class="fa-solid fa-hard-hat"></i>
                        </span>
                        Bloom Construction
                    </a>
                </div>
            </div>

            <!-- About Us Dropdown -->
            <div class="nav-dropdown" id="nd-about">
                <button class="nav-dropdown-btn" onclick="toggleNavDropdown('nd-about')">
                    <i class="fa-solid fa-circle-info nd-icon"></i>
                    About Us
                    <i class="fa-solid fa-chevron-down nd-chevron"></i>
                </button>
                <div class="nav-dropdown-panel">
                    <div class="nd-panel-header">Our Story</div>
                    <a href="#ceo-message">
                        <span class="nd-item-icon"><i class="fa-solid fa-user-tie"></i></span>
                        Message from CEO
                    </a>
                    <a href="#history">
                        <span class="nd-item-icon"><i class="fa-solid fa-timeline"></i></span>
                        Our History
                    </a>
                    <a href="#team">
                        <span class="nd-item-icon"><i class="fa-solid fa-users"></i></span>
                        Our Team
                    </a>
                </div>
            </div>

            <a href="#menu"><i class="fa-solid fa-utensils"
                    style="font-size:13px; color:var(--primary); margin-right:4px;"></i>Menu</a>

            <!-- Our Services Dropdown -->
            <div class="nav-dropdown" id="nd-services">
                <button class="nav-dropdown-btn" onclick="toggleNavDropdown('nd-services')">
                    <i class="fa-solid fa-concierge-bell nd-icon"></i>
                    Our Services
                    <i class="fa-solid fa-chevron-down nd-chevron"></i>
                </button>
                <div class="nav-dropdown-panel">
                    <div class="nd-panel-header">Browse Services</div>
                    <a href="#services" onclick="filterServices('all'); closeAllNavDropdowns();">
                        <span class="nd-item-icon"><i class="fa-solid fa-border-all"></i></span>
                        All Services
                    </a>
                    <?php if (!empty($service_categories)): ?>
                        <?php foreach ($service_categories as $cat): ?>
                            <a href="#services"
                                onclick="filterServices('<?= htmlspecialchars($cat) ?>'); closeAllNavDropdowns();">
                                <span class="nd-item-icon"><i class="fa-solid fa-tag"></i></span>
                                <?= htmlspecialchars($cat) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="#services" onclick="filterServices('Food Delivery'); closeAllNavDropdowns();"><span
                                class="nd-item-icon"><i class="fa-solid fa-truck"></i></span>Food Delivery</a>
                        <a href="#services" onclick="filterServices('Catering Service'); closeAllNavDropdowns();"><span
                                class="nd-item-icon"><i class="fa-solid fa-utensils"></i></span>Catering Service</a>
                        <a href="#services" onclick="filterServices('Wedding Events'); closeAllNavDropdowns();"><span
                                class="nd-item-icon"><i class="fa-solid fa-heart"></i></span>Wedding Events</a>
                        <a href="#services" onclick="filterServices('Birthday Parties'); closeAllNavDropdowns();"><span
                                class="nd-item-icon"><i class="fa-solid fa-cake-candles"></i></span>Birthday Parties</a>
                        <a href="#services" onclick="filterServices('Corporate Events'); closeAllNavDropdowns();"><span
                                class="nd-item-icon"><i class="fa-solid fa-briefcase"></i></span>Corporate Events</a>
                    <?php endif; ?>
                </div>
            </div>

            <a href="#gallery_section"><i class="fa-solid fa-camera"
                    style="font-size:13px; color:var(--primary); margin-right:4px;"></i>Gallery</a>
            <a href="#reservation"><i class="fa-solid fa-calendar-check"
                    style="font-size:13px; color:var(--primary); margin-right:4px;"></i>Reservations</a>
            <a href="#careers"><i class="fa-solid fa-briefcase"
                    style="font-size:13px; color:var(--primary); margin-right:4px;"></i>Careers</a>
        </div>
        <a href="#reservation" class="btn-reserve">Book Table</a>
    </nav>

    <script>
        function toggleNavDropdown(id) {
            const el = document.getElementById(id);
            const isOpen = el.classList.contains('open');
            // Close all first
            document.querySelectorAll('.nav-dropdown').forEach(d => d.classList.remove('open'));
            // Toggle the clicked one
            if (!isOpen) el.classList.add('open');
        }
        function closeAllNavDropdowns() {
            document.querySelectorAll('.nav-dropdown').forEach(d => d.classList.remove('open'));
        }
        // Close when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.nav-dropdown')) closeAllNavDropdowns();
        });
    </script>

    <header class="hero hero-carousel swiper" id="home">
        <div class="swiper-wrapper">
            <!-- Slide 1 (Dynamic from DB) -->
            <div class="swiper-slide hero-slide"
                style="background-image: url('<?= htmlspecialchars($company['hero_image'] ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1974&auto=format&fit=crop') ?>');">
                <div class="hero-content-wrapper">
                    <h1><?= htmlspecialchars($company['hero_title'] ?: 'Taste the Soul of Africa') ?></h1>
                    <p><?= htmlspecialchars($company['hero_subtitle'] ?: 'A modern and authentic dining experience fusing rich African heritage with contemporary culinary arts.') ?>
                    </p>
                    <a href="#menu" class="btn-reserve"
                        style="padding: 15px 40px; font-size:1.2rem;"><?= htmlspecialchars($company['hero_button_text'] ?: 'Explore Menu') ?></a>
                </div>
            </div>
            <!-- Slide 2 (Dynamic from DB) -->
            <div class="swiper-slide hero-slide"
                style="background-image: url('<?= htmlspecialchars($company['hero2_image'] ?: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop') ?>');">
                <div class="hero-content-wrapper">
                    <h1><?= htmlspecialchars($company['hero2_title'] ?: 'Exquisite Fine Dining') ?></h1>
                    <p><?= htmlspecialchars($company['hero2_subtitle'] ?: 'Experience the perfect harmony of tradition and luxury in every bite.') ?>
                    </p>
                    <a href="#reservation" class="btn-reserve"
                        style="padding: 15px 40px; font-size:1.2rem;"><?= htmlspecialchars($company['hero2_button_text'] ?: 'Book a Table') ?></a>
                </div>
            </div>
            <!-- Slide 3 (Dynamic from DB) -->
            <div class="swiper-slide hero-slide"
                style="background-image: url('<?= htmlspecialchars($company['hero3_image'] ?: 'https://images.unsplash.com/photo-1544148103-0773bb108726?q=80&w=2070&auto=format&fit=crop') ?>');">
                <div class="hero-content-wrapper">
                    <h1><?= htmlspecialchars($company['hero3_title'] ?: 'Authentic Flavors') ?></h1>
                    <p><?= htmlspecialchars($company['hero3_subtitle'] ?: 'Savor the rich heritage of African cuisine crafted by world-class chefs.') ?>
                    </p>
                    <a href="#menu" class="btn-reserve"
                        style="padding: 15px 40px; font-size:1.2rem;"><?= htmlspecialchars($company['hero3_button_text'] ?: 'Our Specialities') ?></a>
                </div>
            </div>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Add Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const swiper = new Swiper('.hero-carousel', {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                }
            });
        });
    </script>

    <section id="about">
        <div class="about-top">
            <h2>About Our Restaurant</h2>
            <p><?= htmlspecialchars($company['about_subtitle'] ?: 'Discover our story and passion for authentic African cuisine, where every dish is a celebration of heritage.') ?>
            </p>
        </div>

        <div class="about-content">
            <div class="about-images">
                <img src="<?= htmlspecialchars($company['about_image_main'] ?: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop') ?>"
                    class="about-img-main" alt="Restaurant interior" loading="lazy">
                <img src="<?= htmlspecialchars($company['about_image_sub1'] ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1974&auto=format&fit=crop') ?>"
                    class="about-img-sub1" alt="Patio" loading="lazy">
                <img src="<?= htmlspecialchars($company['about_image_sub2'] ?: 'https://images.unsplash.com/photo-1544148103-0773bb108726?q=80&w=2070&auto=format&fit=crop') ?>"
                    class="about-img-sub2" alt="Dish plating" loading="lazy">
            </div>

            <div class="about-text">
                <h2>About <?= htmlspecialchars($company['company_name']) ?></h2>
                <h3><?= htmlspecialchars($company['about_subtitle'] ?: 'Two Decades of Culinary Mastery, Crafted with Passion and Served with Heart.') ?>
                </h3>
                <p><?= nl2br(htmlspecialchars($company['about_text'] ?: 'A Journey of Culinary Craftsmanship - Since our doors first opened, Bloom Africa has been defined by a commitment to the craft of cooking.')) ?>
                </p>
            </div>
        </div>

        <div style="text-align:center; padding: 40px 0;">
            <p><i class="fa-solid fa-map-marker-alt" style="color:var(--primary);"></i>
                <?= htmlspecialchars($company['address']) ?></p>
        </div>
    </section>

    <!-- Message from CEO Section -->
    <section id="ceo-message"
        style="padding: 100px 5%; background: var(--bg-dark); border-top: 1px solid rgba(223,177,128,0.1);">
        <div class="about-content" style="display: flex; align-items: center; gap: 80px; flex-wrap: wrap;">
            <div class="ceo-image-wrapper" style="flex: 1; position: relative; min-width: 300px;">
                <div
                    style="position: absolute; top: -20px; left: -20px; width: 100%; height: 100%; border: 2px solid var(--primary); border-radius: 20px; z-index: 1;">
                </div>
                <img src="<?= htmlspecialchars(($company['ceo_image'] ?? '') ?: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=1974&auto=format&fit=crop') ?>"
                    alt="CEO Image"
                    style="width: 100%; border-radius: 20px; position: relative; z-index: 2; box-shadow: 0 30px 60px rgba(0,0,0,0.15); display: block;">
            </div>

            <div class="ceo-text" style="flex: 1.2; min-width: 300px;">
                <span
                    style="font-size: 11px; color: var(--primary); font-weight: 800; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Leadership
                    Message</span>
                <h2
                    style="font-family:'Playfair Display', serif; font-size: 2.8rem; color: #1a1512; margin-bottom: 5px;">
                    Message from <?= htmlspecialchars(($company['ceo_name'] ?? '') ?: 'The CEO') ?></h2>
                <h4 style="font-size: 1.1rem; color: #666; margin-bottom: 30px; font-weight: 500; font-style: italic;">
                    <?= htmlspecialchars(($company['ceo_title'] ?? '') ?: 'Owner and CEO, Bloom Africa') ?>
                </h4>

                <div style="font-size: 1.8rem; color: var(--primary); margin-bottom: 20px; opacity: 0.5;"><i
                        class="fa-solid fa-quote-left"></i></div>

                <p style="font-size: 1.15rem; line-height: 1.8; color: #444; margin-bottom: 30px;">
                    <?= nl2br(htmlspecialchars(mb_strimwidth(($company['ceo_message'] ?? ''), 0, 350, "..."))) ?>
                </p>

                <div
                    style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 40px; height: 1px; background: var(--primary);"></div>
                        <span
                            style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: #1a1512; font-weight: 700;">With
                            Gratitude, <?= htmlspecialchars($company['ceo_name'] ?? 'The CEO') ?></span>
                    </div>
                    <a href="javascript:void(0)" class="read-more"
                        style="background: var(--primary); color: #000; padding: 12px 25px; border-radius: 30px; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: 0.3s;"
                        onclick="openCEOModal('<?= addslashes(($company['ceo_image'] ?? '') ?: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=1974&auto=format&fit=crop') ?>', 'Leadership Message', 'Message from <?= addslashes($company['ceo_name'] ?? 'The CEO') ?>', '<?= addslashes(str_replace(["\r", "\n"], ' ', $company['ceo_message'] ?? '')) ?>', '<?= addslashes($company['ceo_title'] ?? 'Owner and CEO') ?>')">
                        Read Full Message <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Our History Section -->
    <section id="history" style="padding: 100px 5%; background: #fffdfb; border-top: 1px solid rgba(223,177,128,0.05);">
        <div style="text-align: center; max-width: 900px; margin: 0 auto;">
            <span
                style="font-size: 11px; color: var(--primary); font-weight: 800; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Our
                Journey</span>
            <h2 style="font-family:'Playfair Display', serif; font-size: 3rem; color: #1a1512; margin-bottom: 30px;">
                <?= htmlspecialchars($company['history_title'] ?: 'Our Rich History') ?>
            </h2>
            <div style="width: 80px; height: 2px; background: var(--primary); margin: 0 auto 40px;"></div>
            <p style="font-size: 1.2rem; line-height: 1.9; color: #555; margin-bottom: 25px;">
                <?= nl2br(htmlspecialchars($company['history_text1'])) ?>
            </p>
            <p style="font-size: 1.2rem; line-height: 1.9; color: #555;">
                <?= nl2br(htmlspecialchars($company['history_text2'])) ?>
            </p>
        </div>
    </section>

    <!-- Our Team Section -->
    <section id="team"
        style="padding: 100px 5%; background: var(--bg-dark); border-top: 1px solid rgba(223,177,128,0.05);">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-family:'Playfair Display', serif; font-size: 3rem; color: #1a1512;">The Minds Behind the
                Magic</h2>
            <p style="color: #666; margin-top: 10px;">Our dedicated team of world-class chefs and hospitality experts.
            </p>
        </div>

        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 40px; max-width: 1200px; margin: 0 auto;">
            <?php foreach ($team_members as $tm): ?>
                <div style="text-align: center;">
                    <div
                        style="width: 200px; height: 200px; border-radius: 50%; overflow: hidden; margin: 0 auto 20px; border: 3px solid var(--primary);">
                        <img src="<?= htmlspecialchars($tm['image_url'] ?: 'https://images.unsplash.com/photo-1583394838336-acd977730f90?q=80&w=1968&auto=format&fit=crop') ?>"
                            style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <h3 style="color: var(--primary); font-size: 1.4rem; margin-bottom: 5px;">
                        <?= htmlspecialchars($tm['name']) ?>
                    </h3>
                    <p style="color: #888; font-size: 0.9rem; text-transform: uppercase;">
                        <?= htmlspecialchars($tm['role']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Careers Section -->
    <section id="careers" class="careers-section">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-family:'Playfair Display', serif; font-size: 3rem; color: var(--primary);">Join Our Team
            </h2>
            <p>We are always looking for passionate individuals to join the Bloom Africa family.</p>
        </div>

        <div class="job-grid">
            <?php
            // Fetch only active jobs that haven't reached their closing date/time
            $active_jobs = $pdo->query("SELECT * FROM jobs WHERE status = 'Active' AND (closing_date IS NULL OR closing_date >= NOW()) ORDER BY id DESC")->fetchAll();
            if (count($active_jobs) > 0):
                foreach ($active_jobs as $job):
                    $time_left = strtotime($job['closing_date']) - time();
                    $days = floor($time_left / (60 * 60 * 24));
                    $hours = floor(($time_left % (60 * 60 * 24)) / (60 * 60));
                    $mins = floor(($time_left % (60 * 60)) / 60);
                    $is_urgent = ($days < 10);
                    ?>
                    <div class="job-card" style="<?= $is_urgent ? 'border-top: 3px solid #ef4444;' : '' ?>">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap: 10px;">
                            <h3 style="margin-bottom:10px; flex: 1;"><?= htmlspecialchars($job['title']) ?></h3>
                            <div style="text-align: right;">
                                <div
                                    style="font-size:10px; color:<?= $is_urgent ? '#ef4444' : '#10b981' ?>; font-weight:800; text-transform:uppercase; margin-bottom: 5px;">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    <?= $is_urgent ? 'Closing Soon' : 'Accepting Portfolios' ?>
                                </div>
                                <div style="font-size: 14px; font-weight: 700; color: #fff;">
                                    <?= $days ?>d <?= $hours ?>h <?= $mins ?>m
                                </div>
                            </div>
                        </div>
                        <div class="job-meta">
                            <span><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($job['category']) ?></span>
                            <span><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($job['type']) ?></span>
                            <span style="color: #888; font-size: 11px;"><i class="fa-solid fa-calendar"></i>
                                <?= date("M d, Y", strtotime($job['closing_date'])) ?></span>
                        </div>
                        <p class="job-desc"><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                        <button class="apply-btn"
                            onclick="openApplication(<?= $job['id'] ?>, '<?= htmlspecialchars($job['title']) ?>')">Apply
                            Now</button>
                    </div>
                    <?php
                endforeach;
            else:
                echo "<p style='text-align:center; width:100%; color:#888;'>No open positions at the moment. Check back later!</p>";
            endif;
            ?>
        </div>
    </section>

    <!-- Application Modal -->
    <div id="applicationModal" class="app-modal">
        <div class="app-modal-content">
            <span class="close-app" onclick="closeApplication()">&times;</span>
            <h2 id="appJobTitle" style="color:var(--primary); margin-bottom: 25px;">Apply for Job</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="apply_job" value="1">
                <input type="hidden" name="job_id" id="job_id_input">
                <div class="form-group" style="margin-bottom:15px;">
                    <input type="text" name="applicant_name" placeholder="Full Name" required
                        style="width:100%; padding:12px; background:#251d18; border:1px solid #444; color:#fff; border-radius:8px;">
                </div>
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <input type="email" name="email" placeholder="Email Address" required
                            style="width:100%; padding:12px; background:#251d18; border:1px solid #444; color:#fff; border-radius:8px;">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <input type="tel" name="phone" placeholder="Phone Number" required
                            style="width:100%; padding:12px; background:#251d18; border:1px solid #444; color:#fff; border-radius:8px;">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <input type="number" step="0.01" name="gpa" placeholder="GPA (e.g. 3.8)" required
                            style="width:100%; padding:12px; background:#251d18; border:1px solid #444; color:#fff; border-radius:8px;">
                    </div>
                </div>

                <div style="display: flex; gap: 20px; margin-bottom: 20px; align-items: flex-start;">
                    <div style="width: 120px;">
                        <label
                            style="font-size: 11px; color: var(--primary); display: block; margin-bottom: 5px; text-transform: uppercase;">Your
                            Photo</label>
                        <div style="width: 120px; height: 160px; background: #251d18; border: 2px dashed #444; border-radius: 8px; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; cursor: pointer;"
                            onclick="document.getElementById('photoInput').click()">
                            <input type="file" name="photo" id="photoInput" accept="image/*" required
                                style="display: none;"
                                onchange="let reader = new FileReader(); reader.onload = (e) => { document.getElementById('photoPreview').src = e.target.result; document.getElementById('photoPreview').style.display='block'; document.getElementById('photoIcon').style.display='none'; }; reader.readAsDataURL(this.files[0]);">
                            <div id="photoIcon" style="text-align: center; color: #666;">
                                <i class="fa-solid fa-camera" style="font-size: 24px; margin-bottom: 5px;"></i><br>
                                <span style="font-size: 10px;">3 x 4 Photo</span>
                            </div>
                            <img id="photoPreview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <div class="form-group" style="margin-bottom:15px;">
                            <label
                                style="font-size: 11px; color: #888; display: block; margin-bottom: 5px; text-transform: uppercase;">CV
                                / Resume</label>
                            <div
                                style="position: relative; background: #251d18; border: 1px solid #444; border-radius: 8px; padding: 12px; display: flex; align-items: center; gap: 10px;">
                                <i class="fa-solid fa-file-import" style="color: var(--primary);"></i>
                                <input type="file" name="resume" accept=".pdf,.doc,.docx,image/*" required
                                    style="font-size: 12px; color: #888;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label
                                style="font-size: 11px; color: #888; display: block; margin-bottom: 5px; text-transform: uppercase;">Why
                                should we hire you?</label>
                            <textarea name="message" placeholder="Brief summary of your experience..." rows="4"
                                style="width:100%; padding:12px; background:#251d18; border:1px solid #444; color:#fff; border-radius:8px; resize: none;"></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="submit-btn" style="width:100%;">Submit Application</button>
            </form>
        </div>
    </div>

    <script>
        function openApplication(id, title) {
            document.getElementById('job_id_input').value = id;
            document.getElementById('appJobTitle').innerText = 'Apply for ' + title;
            document.getElementById('applicationModal').style.display = 'flex';
        }
        function closeApplication() {
            document.getElementById('applicationModal').style.display = 'none';
        }
    </script>

    <section id="services" class="services-section">
        <h2 class="section-title">Our Services</h2>
        <div class="services-grid">
            <?php if (!empty($services_list)): ?>
                <?php foreach ($services_list as $srv): ?>
                    <div class="service-card" data-category="<?= htmlspecialchars($srv['category'] ?? 'Others') ?>">
                        <div class="service-media">
                            <?php if ($srv['video_url']): ?>
                                <video autoplay muted loop playsinline>
                                    <source src="<?= htmlspecialchars($srv['video_url']) ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($srv['image_url'] ?: 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?q=80&w=2070&auto=format&fit=crop') ?>"
                                    alt="<?= htmlspecialchars($srv['title']) ?>">
                            <?php endif; ?>
                        </div>
                        <div class="service-info">
                            <div class="service-icon"><i
                                    class="fa-solid <?= htmlspecialchars($srv['icon'] ?: 'fa-concierge-bell') ?>"></i></div>
                            <span
                                style="font-size: 11px; color: var(--primary); font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 5px; opacity: 0.8;"><?= htmlspecialchars($srv['category'] ?? 'Service') ?></span>
                            <h3><?= htmlspecialchars($srv['title']) ?></h3>
                            <p><?= nl2br(htmlspecialchars($srv['description'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default Services if DB is empty -->
                <div class="service-card" data-category="Food Delivery">
                    <div class="service-media">
                        <img src="https://images.unsplash.com/photo-1526367790999-015078648402?q=80&w=2070&auto=format&fit=crop"
                            alt="Food Delivery">
                    </div>
                    <div class="service-info">
                        <div class="service-icon"><i class="fa-solid fa-truck"></i></div>
                        <h3>Food Delivery</h3>
                        <p>Hot and fresh meals from our kitchen straight to your doorstep within 30 minutes.</p>
                    </div>
                </div>
                <div class="service-card" data-category="Catering Service">
                    <div class="service-media">
                        <img src="https://images.unsplash.com/photo-1555244162-803834f70033?q=80&w=2070&auto=format&fit=crop"
                            alt="Catering Service">
                    </div>
                    <div class="service-info">
                        <div class="service-icon"><i class="fa-solid fa-utensils"></i></div>
                        <h3>Catering Service</h3>
                        <p>Professional catering for your events, offering a diverse menu from African to European cuisines.
                        </p>
                    </div>
                </div>
                <div class="service-card" data-category="Wedding Events">
                    <div class="service-media">
                        <img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2069&auto=format&fit=crop"
                            alt="Wedding Events">
                    </div>
                    <div class="service-info">
                        <div class="service-icon"><i class="fa-solid fa-heart"></i></div>
                        <h3>Wedding Service</h3>
                        <p>Creating magical wedding experiences with exquisite décor, world-class dining, and impeccable
                            service.</p>
                    </div>
                </div>
                <div class="service-card" data-category="Birthday Parties">
                    <div class="service-media">
                        <img src="https://images.unsplash.com/photo-1464306208223-e0b4495a0100?q=80&w=2070&auto=format&fit=crop"
                            alt="Birthday Service">
                    </div>
                    <div class="service-info">
                        <div class="service-icon"><i class="fa-solid fa-cake-candles"></i></div>
                        <h3>Birthday Service</h3>
                        <p>Celebrate your special day with custom cakes, vibrant themes, and a memorable atmosphere for your
                            guests.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section id="gallery_section" style="padding-top: 50px;">
        <h2 class="section-title">Our Gallery</h2>

        <div class="gallery-filters">
            <button class="filter-btn active" onclick="filterGallery('all')">All</button>
            <?php foreach ($gallery_categories as $cat): ?>
                <button class="filter-btn" onclick="filterGallery('<?= htmlspecialchars($cat) ?>')">
                    <?= htmlspecialchars($cat) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="gallery">
            <div class="gallery-grid" id="galleryGrid">
                <?php foreach ($gallery_items as $item): ?>
                    <div class="gallery-card" data-category="<?= htmlspecialchars($item['category']) ?>">
                        <div class="gallery-card-media">
                            <img src="<?= htmlspecialchars($item['image_url']) ?>"
                                alt="<?= htmlspecialchars($item['title'] ?: 'Gallery Image') ?>" loading="lazy">
                        </div>
                        <div class="gallery-card-content">
                            <span
                                style="font-size: 11px; color: var(--primary); font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px; opacity: 0.8;"><?= htmlspecialchars($item['category']) ?></span>
                            <h4><?= htmlspecialchars($item['title'] ?: 'Signature Experience') ?></h4>
                            <p><?= nl2br(htmlspecialchars($item['description'] ?: 'Experience the perfect blend of tradition and modern culinary arts at Bloom Africa.')) ?>
                            </p>
                            <div class="gallery-card-footer">
                                <span class="date"><i class="fa-regular fa-calendar"></i>
                                    <?= date("M d, Y", strtotime($item['created_at'])) ?></span>
                                <a href="javascript:void(0)" class="read-more"
                                    onclick="openGalleryModal('<?= addslashes($item['image_url']) ?>', '<?= addslashes($item['category']) ?>', '<?= addslashes($item['title'] ?: 'Signature Experience') ?>', '<?= addslashes(str_replace(["\r", "\n"], ' ', $item['description'] ?: 'Experience the perfect blend of tradition and modern culinary arts at Bloom Africa.')) ?>', '<?= date("M d, Y", strtotime($item['created_at'])) ?>')">Read
                                    More <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($gallery_items)): ?>
                    <!-- Fallback Cards if DB is empty -->
                    <div class="gallery-card" data-category="Restaurant">
                        <div class="gallery-card-media">
                            <img src="https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?q=80&w=2070&auto=format&fit=crop"
                                alt="Ambience">
                        </div>
                        <div class="gallery-card-content">
                            <span
                                style="font-size: 11px; color: var(--primary); font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px; opacity: 0.8;">Restaurant</span>
                            <h4>The Elegant Ambience</h4>
                            <p>Our restaurant offers a warm and inviting atmosphere, perfect for family gatherings and
                                intimate dinners.</p>
                            <div class="gallery-card-footer">
                                <span class="date"><i class="fa-regular fa-calendar"></i> Dec 22, 2030</span>
                                <a href="javascript:void(0)" class="read-more"
                                    onclick="openGalleryModal('https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?q=80&w=2070&auto=format&fit=crop', 'Restaurant', 'The Elegant Ambience', 'Our restaurant offers a warm and inviting atmosphere, perfect for family gatherings and intimate dinners.', 'Dec 22, 2030')">Read
                                    More <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-card" data-category="Food">
                        <div class="gallery-card-media">
                            <img src="https://images.unsplash.com/photo-1544025162-8111d4e06223?q=80&w=1969&auto=format&fit=crop"
                                alt="Culinary">
                        </div>
                        <div class="gallery-card-content">
                            <span
                                style="font-size: 11px; color: var(--primary); font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px; opacity: 0.8;">Food</span>
                            <h4>Masterpiece Creations</h4>
                            <p>Every dish is a work of art, crafted with the freshest ingredients and traditional African
                                spices.</p>
                            <div class="gallery-card-footer">
                                <span class="date"><i class="fa-regular fa-calendar"></i> Dec 24, 2030</span>
                                <a href="javascript:void(0)" class="read-more"
                                    onclick="openGalleryModal('https://images.unsplash.com/photo-1544025162-8111d4e06223?q=80&w=1969&auto=format&fit=crop', 'Food', 'Masterpiece Creations', 'Every dish is a work of art, crafted with the freshest ingredients and traditional African spices.', 'Dec 24, 2030')">Read
                                    More <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-card" data-category="Ambience">
                        <div class="gallery-card-media">
                            <img src="https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=2070&auto=format&fit=crop"
                                alt="Mixology">
                        </div>
                        <div class="gallery-card-content">
                            <span
                                style="font-size: 11px; color: var(--primary); font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px; opacity: 0.8;">Ambience</span>
                            <h4>Evening Glow</h4>
                            <p>Experience the vibrant nightlife at Bloom Africa with our curated selection of signature
                                drinks.</p>
                            <div class="gallery-card-footer">
                                <span class="date"><i class="fa-regular fa-calendar"></i> Dec 28, 2030</span>
                                <a href="javascript:void(0)" class="read-more"
                                    onclick="openGalleryModal('https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=2070&auto=format&fit=crop', 'Ambience', 'Evening Glow', 'Experience the vibrant nightlife at Bloom Africa with our curated selection of signature drinks.', 'Dec 28, 2030')">Read
                                    More <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script>
        function filterGallery(category) {
            const items = document.querySelectorAll('.gallery-card');
            const buttons = document.querySelectorAll('.gallery-filters .filter-btn');

            // Update active button
            buttons.forEach(btn => {
                if (btn.innerText.toLowerCase() === category.toLowerCase() || (category === 'all' && btn.innerText === 'All')) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Filter items
            items.forEach(item => {
                if (category === 'all' || item.getAttribute('data-category').toLowerCase() === category.toLowerCase()) {
                    item.classList.remove('hidden');
                    item.style.display = 'flex';
                } else {
                    item.classList.add('hidden');
                    item.style.display = 'none';
                }
            });
        }

        function filterServices(category) {
            const items = document.querySelectorAll('.service-card');

            // Filter items
            items.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                if (category === 'all' || itemCategory === category) {
                    item.style.display = 'block';
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                        item.style.transition = 'all 0.5s ease';
                    }, 10);
                } else {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(20px)';
                    item.style.transition = 'all 0.5s ease';
                    setTimeout(() => {
                        if (item.style.opacity === '0') item.style.display = 'none';
                    }, 500);
                }
            });

            // Smooth scroll to services section
            const servicesSection = document.getElementById('services');
            if (servicesSection) {
                servicesSection.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function initiateChat(itemName, price, platform, url) {
            const formData = new FormData();
            formData.append('item_name', itemName);
            formData.append('price', price);
            formData.append('platform', platform);

            fetch('log_order.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                window.open(url, '_blank', 'width=600,height=800,scrollbars=yes');
            }).catch(err => {
                console.error('Logging failed, but opening chat anyway...', err);
                window.open(url, '_blank');
            });
        }

        function toggleFav(itemId, btn) {
            const isActive = btn.classList.contains('active');
            const icon = btn.querySelector('i');
            let email = localStorage.getItem('customer_email') || '';

            if (!isActive && !email) {
                // Show email prompt modal
                const modal = document.getElementById('favModal');
                modal.style.display = 'flex';
                document.getElementById('favItemId').value = itemId;
                document.getElementById('favBtnRef').value = btn; // This won't work well, I'll use a better way

                // Store reference to current item
                window.currentFavItem = { id: itemId, btn: btn };
                return;
            }

            processFavAction(itemId, btn, isActive, email);
        }

        function submitFavEmail() {
            const emailInput = document.getElementById('favEmailInput');
            const email = emailInput.value.trim();
            if (!email || !email.includes('@')) {
                alert('Please enter a valid email address.');
                return;
            }

            localStorage.setItem('customer_email', email);
            document.getElementById('favModal').style.display = 'none';

            if (window.currentFavItem) {
                processFavAction(window.currentFavItem.id, window.currentFavItem.btn, false, email);
            }
        }

        function processFavAction(itemId, btn, isActive, email) {
            const icon = btn.querySelector('i');

            // Visual update
            if (isActive) {
                btn.classList.remove('active');
                icon.className = 'fa-regular fa-heart';
            } else {
                btn.classList.add('active');
                icon.className = 'fa-solid fa-heart';
                // Add a little pop animation
                btn.style.transform = 'scale(1.4)';
                setTimeout(() => { btn.style.transform = ''; }, 200);
            }

            // Server update
            fetch('handle_favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `item_id=${itemId}&action=${isActive ? 'unlike' : 'like'}&email=${encodeURIComponent(email)}`
            });
        }

        function closeFavModal() {
            document.getElementById('favModal').style.display = 'none';
        }
    </script>
    </section>

    <section id="menu">
        <h2 class="section-title">Our Signature Dishes</h2>
        <div class="menu-grid">
            <?php if (empty($menu_items)): ?>
                <p style="text-align: center; grid-column: 1/-1;">Menu is currently being updated. Please check back later.
                </p>
            <?php endif; ?>

            <?php foreach ($menu_items as $item): ?>
                <div class="menu-item">
                    <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=500&q=80') ?>"
                        alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                    <div class="menu-content">
                        <div class="menu-header">
                            <h3>
                                <?= htmlspecialchars($item['name']) ?>
                            </h3>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                                <span class="price">$
                                    <?= number_format($item['price'], 2) ?>
                                </span>
                                <?php $is_fav = isset($_COOKIE['fav_' . $item['id']]); ?>
                                <button class="fav-btn <?= $is_fav ? 'active' : '' ?>"
                                    onclick="toggleFav(<?= $item['id'] ?>, this)">
                                    <i class="<?= $is_fav ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                                </button>
                            </div>
                        </div>
                        <p class="menu-desc">
                            <?= htmlspecialchars($item['description']) ?>
                        </p>
                        <div class="menu-order-btns">
                            <a href="javascript:void(0)"
                                onclick="initiateChat('<?= addslashes($item['name']) ?>', '<?= $item['price'] ?>', 'WhatsApp', 'https://wa.me/251918592028?text=Hello%21+I+would+like+to+order+<?= urlencode($item['name']) ?>')"
                                class="buy-btn">
                                <i class="fa-brands fa-whatsapp"></i> WhatsApp
                            </a>
                            <a href="javascript:void(0)"
                                onclick="initiateChat('<?= addslashes($item['name']) ?>', '<?= $item['price'] ?>', 'Telegram', 'https://t.me/+251918592028?text=Hello%21+I+would+like+to+order+<?= urlencode($item['name']) ?>')"
                                class="telegram-btn">
                                <i class="fa-brands fa-telegram"></i> Telegram
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="reservation">
        <div class="reservation">
            <div class="res-info">
                <h2>Reserve Your Spot</h2>
                <p>Join us for an unforgettable culinary journey. Secure your table effortlessly through our online
                    booking system.</p>
                <br>
                <div style="color: var(--primary);">
                    <p><i class="fa-solid fa-clock"></i> Open Mon-Sun: 10:00 AM - 11:00 PM</p>
                    <p><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($company['phone']) ?></p>
                    <p><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($company['email']) ?></p>
                </div>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="reserve_action" value="1">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Full Name" required>
                </div>
                <div class="form-group">
                    <input type="tel" name="phone" placeholder="Phone Number" required>
                </div>
                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <input type="date" id="res_date" name="date" required onchange="updateAvailableTables()">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <input type="time" id="res_time" name="time" required onchange="updateAvailableTables()">
                    </div>
                </div>
                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <select name="guests" required>
                            <option value="" disabled selected>Number of Guests</option>
                            <option value="1">1 Person</option>
                            <option value="2">2 People</option>
                            <option value="3">3 People</option>
                            <option value="4">4 People</option>
                            <option value="5+">5+ People</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <select name="table_number" id="table_select" required>
                            <option value="" disabled selected>Select Table</option>
                            <option value="">Choose Date & Time First</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="submit-btn">Confirm Reservation</button>
            </form>
            <script>
                function updateAvailableTables() {
                    const date = document.getElementById('res_date').value;
                    const time = document.getElementById('res_time').value;
                    const select = document.getElementById('table_select');

                    if (date && time) {
                        select.innerHTML = '<option value="" disabled selected>Checking availability...</option>';
                        fetch(`check_tables.php?date=${date}&time=${time}`)
                            .then(response => response.json())
                            .then(data => {
                                select.innerHTML = '<option value="" disabled selected>Select Table</option>';
                                data.forEach(table => {
                                    const option = document.createElement('option');
                                    option.value = table.table_number;
                                    option.textContent = `Table ${table.table_number} (${table.capacity} seats)`;
                                    if (!table.is_available) {
                                        option.disabled = true;
                                        option.textContent += ' - Already Reserved';
                                    }
                                    select.appendChild(option);
                                });
                            })
                            .catch(err => {
                                console.error('Error fetching tables:', err);
                                select.innerHTML = '<option value="" disabled selected>Error loading tables</option>';
                            });
                    }
                }
            </script>
        </div>
    </section>

    <section id="contact" class="map-section">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.5!2d38.74!3d9.03!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOcKwMDEnNDguMCJOIDM4wrA0NCcyNC4wIkU!5e0!3m2!1sen!2set!4v1620000000000!5m2!1sen!2set"
            allowfullscreen="" loading="lazy"></iframe>
    </section>

    <footer
        style="background: rgba(10, 8, 6, 0.95); backdrop-filter: blur(15px); padding: 80px 5% 40px; border-top: 1px solid rgba(223, 177, 128, 0.1); position: relative; z-index: 10;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 50px; text-align: left; margin-bottom: 60px;">
                <!-- Brand Section -->
                <div>
                    <h2
                        style="font-family:'Playfair Display', serif; color:var(--primary); font-size: 2.2rem; margin-bottom: 25px;">
                        <?= htmlspecialchars($company['company_name']) ?>
                    </h2>
                    <p style="color: rgba(255,255,255,0.7); line-height: 1.8; font-size: 1rem;">
                        Celebrate the rich flavors of Africa with a modern twist. Bloom Africa is more than just a
                        restaurant; it's a sanctuary for culinary excellence and hospitality.
                    </p>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 style="color: #fff; font-size: 1.3rem; margin-bottom: 25px; font-weight: 600;">Contact Us</h3>
                    <div style="display: flex; flex-direction: column; gap: 15px; color: rgba(255,255,255,0.8);">
                        <p><i class="fa-solid fa-location-dot"
                                style="color: var(--primary); margin-right: 12px; width: 20px;"></i>
                            <?= htmlspecialchars($company['address']) ?></p>
                        <p><i class="fa-solid fa-phone-volume"
                                style="color: var(--primary); margin-right: 12px; width: 20px;"></i>
                            <?= htmlspecialchars($company['phone']) ?></p>
                        <p><i class="fa-solid fa-envelope-open-text"
                                style="color: var(--primary); margin-right: 12px; width: 20px;"></i>
                            <?= htmlspecialchars($company['email']) ?></p>
                    </div>
                </div>

                <!-- Social Links -->
                <div>
                    <h3 style="color: #fff; font-size: 1.3rem; margin-bottom: 25px; font-weight: 600;">Follow the
                        Journey</h3>
                    <div class="footer-social-links" style="display: flex; gap: 18px; flex-wrap: wrap;">
                        <?php if ($company['facebook']): ?>
                            <a href="<?= htmlspecialchars($company['facebook']) ?>" target="_blank"
                                class="social-icon-btn"><i class="fa-brands fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if ($company['instagram']): ?>
                            <a href="<?= htmlspecialchars($company['instagram']) ?>" target="_blank"
                                class="social-icon-btn"><i class="fa-brands fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if ($company['twitter']): ?>
                            <a href="<?= htmlspecialchars($company['twitter']) ?>" target="_blank"
                                class="social-icon-btn"><i class="fa-brands fa-x-twitter"></i></a>
                        <?php endif; ?>
                        <?php if ($company['tiktok']): ?>
                            <a href="<?= htmlspecialchars($company['tiktok']) ?>" target="_blank" class="social-icon-btn"><i
                                    class="fa-brands fa-tiktok"></i></a>
                        <?php endif; ?>
                        <?php if ($company['linkedin']): ?>
                            <a href="<?= htmlspecialchars($company['linkedin']) ?>" target="_blank"
                                class="social-icon-btn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <?php endif; ?>
                        <?php if ($company['telegram']): ?>
                            <a href="<?= htmlspecialchars($company['telegram']) ?>" target="_blank"
                                class="social-icon-btn"><i class="fa-brands fa-telegram"></i></a>
                        <?php endif; ?>
                        <?php if ($company['whatsapp']): ?>
                            <a href="<?= htmlspecialchars($company['whatsapp']) ?>" target="_blank"
                                class="social-icon-btn"><i class="fa-brands fa-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Developer & Copyright Area -->
            <div
                style="border-top: 1px solid rgba(223, 177, 128, 0.1); padding-top: 40px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 30px;">
                <div class="developer-credit">
                    <div class="dev-img-container" style="position: relative;">
                        <img src="<?= htmlspecialchars($company['dev_photo'] ?: 'developer_photo.jpg') ?>"
                            alt="<?= htmlspecialchars($company['dev_name'] ?? 'The Developer') ?>" class="dev-photo"
                            onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($company['dev_name'] ?? 'Dev') ?>&background=dfb180&color=000'">
                        <div
                            style="position: absolute; bottom: -2px; right: -2px; width: 12px; height: 12px; background: #10b981; border: 2px solid #000; border-radius: 50%;">
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <span style="font-size: 0.9rem; color: rgba(255,255,255,0.6); margin-bottom: 2px;">Designed &
                            Developed by</span>
                        <span
                            style="font-size: 1.1rem; color: var(--primary); font-weight: 800; letter-spacing: 0.5px;"><?= htmlspecialchars($company['dev_name'] ?: 'Mequannent Gashaw') ?></span>
                        <div
                            style="display: flex; gap: 15px; margin-top: 8px; font-size: 0.85rem; color: rgba(255,255,255,0.5);">
                            <?php if (!empty($company['dev_email'])): ?>
                                <span><i class="fa-solid fa-envelope" style="margin-right: 5px;"></i>
                                    <?= htmlspecialchars($company['dev_email']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($company['dev_phone'])): ?>
                                <span><i class="fa-solid fa-phone" style="margin-right: 5px;"></i>
                                    <?= htmlspecialchars($company['dev_phone']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div style="text-align: right;">
                    <p style="font-size: 0.9rem; color: rgba(255,255,255,0.4);">
                        &copy; <?= date('Y') ?> <?= htmlspecialchars($company['company_name']) ?>.
                        <?= htmlspecialchars($company['copyright_text'] ?: 'All Rights Reserved.') ?>
                    </p>
                    <p
                        style="font-size: 0.75rem; color: rgba(223, 177, 128, 0.3); margin-top: 5px; text-transform: uppercase; letter-spacing: 2px;">
                        Bloom Africa Management System v2.0
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Chatbot Widget Window (Toggle moved to group below) -->
    <div class="chatbot-container">
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-robot"></i>
                    <strong style="font-weight: 700;">Bloom Online Chat</strong>
                </div>
                <i class="fa-solid fa-xmark" style="cursor: pointer;" onclick="toggleChat()"></i>
            </div>
            <div id="chatRegForm" style="padding: 20px; display: none; background: #251d18;">
                <p style="font-size: 13px; color: #888; margin-bottom: 15px;">Please introduce yourself to start
                    chatting with our team!</p>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <input type="text" id="regName" placeholder="Full Name"
                        style="width: 100%; padding: 10px; background: #1a1512; border: 1px solid #444; border-radius: 8px; color: #fff;">
                    <input type="email" id="regEmail" placeholder="Email Address"
                        style="width: 100%; padding: 10px; background: #1a1512; border: 1px solid #444; border-radius: 8px; color: #fff;">
                    <input type="tel" id="regPhone" placeholder="Phone Number"
                        style="width: 100%; padding: 10px; background: #1a1512; border: 1px solid #444; border-radius: 8px; color: #fff;">
                    <button onclick="registerChat()"
                        style="background: #dfb180; color: #000; padding: 12px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; margin-top: 10px;">Connect
                        Now</button>
                </div>
            </div>
            <div class="chat-messages" id="chatMessages" style="display: none;">
                <div class="chat-bubble bot">Hello! Welcome to Bloom Africa. How can I assist you today? 🌍🍖</div>
            </div>
            <div class="chat-input-area" id="chatInputArea" style="display: none;">
                <input type="text" id="chatInput" placeholder="Order text here..."
                    onkeypress="if(event.key === 'Enter') sendChatMessage()">
                <button class="chat-send-btn" onclick="sendChatMessage()"><i
                        class="fa-solid fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
    <script>
        let chatPollInterval = null;
        function registerChat() {
            const name = document.getElementById('regName').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const phone = document.getElementById('regPhone').value.trim();

            if (!name || !email || !phone) return alert('All fields required');
            fetch('chat_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ register: true, name, email, phone , department: 'Restaurant' })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showChatInterface();
                        loadChatHistory();
                    }
                });
        }

        function showChatInterface() {
            document.getElementById('chatRegForm').style.display = 'none';
            document.getElementById('chatMessages').style.display = 'flex';
            document.getElementById('chatInputArea').style.display = 'flex';
        }

        function toggleChat() {
            const win = document.getElementById('chatWindow');
            const isOpen = win.style.display === 'flex';
            win.style.display = isOpen ? 'none' : 'flex';

            if (win.style.display === 'flex') {
                checkChatReg();
                chatPollInterval = setInterval(loadChatHistory, 3000);
            } else {
                clearInterval(chatPollInterval);
            }
        }

        function checkChatReg() {
            fetch('chat_handler.php?check_reg=1') // Added query param to differentiate from message history fetch
                .then(res => res.json())
                .then(data => {
                    if (data.registered && data.customer.department === 'Restaurant') {
                        showChatInterface();
                        loadChatHistory();
                    } else {
                        document.getElementById('chatRegForm').style.display = 'block';
                        document.getElementById('chatMessages').style.display = 'none';
                        document.getElementById('chatInputArea').style.display = 'none';
                    }
                });
        }
        function sendChatMessage() {
            const input = document.getElementById('chatInput');
            const msg = input.value.trim();
            if (!msg) return;
            appendMessage('User', msg);
            input.value = '';
            fetch('chat_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: msg })
            })
                .then(res => res.json())
                .then(data => {
                    // Bots reply only if requested, but polling will catch admin replies
                    if (data.reply) appendMessage('Bot', data.reply, data.buttons);
                });
        }
        function appendMessage(sender, text, buttons = []) {
            const container = document.getElementById('chatMessages');

            // For User messages, we skip duplicates. For bot with buttons, we allow showing buttons on last msg.
            const exists = Array.from(container.children).some(c => c.dataset.text === text && c.dataset.sender === sender && !buttons.length);
            if (exists) return;
            const div = document.createElement('div');
            div.className = `chat-bubble ${sender.toLowerCase()}`;
            div.innerHTML = `<strong>${sender}:</strong> <br> ${text.replace(/\n/g, '<br>')}`;

            if (buttons && buttons.length > 0) {
                const optBox = document.createElement('div');
                optBox.className = 'chat-options';
                buttons.forEach(label => {
                    const btn = document.createElement('button');
                    btn.className = 'chat-option-btn';
                    btn.innerText = label;
                    btn.onclick = () => {
                        document.getElementById('chatInput').value = label;
                        sendChatMessage();
                    };
                    optBox.appendChild(btn);
                });
                div.appendChild(optBox);
            }
            div.dataset.text = text;
            div.dataset.sender = sender;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }
        function loadChatHistory() {
            fetch('chat_handler.php')
                .then(res => res.json())
                .then(data => {
                    if (data.messages && data.registered && data.customer.department === 'Restaurant') {
                        data.messages.forEach(m => appendMessage(m.sender, m.message));
                    }
                });
        }
    </script>
    <div class="float-btn-group" id="socialFloatGroup">
        <div class="chat-toggle" onclick="toggleChat()" id="chatToggle" style="margin-bottom: 5px;">
            <i class="fa-solid fa-robot"></i>
        </div>
        <?php if (!empty($company['telegram'])): ?>
                <a href="<?= htmlspecialchars($company['telegram']) ?>" class="telegram-float" target="_blank"
                    rel="noopener noreferrer" data-tooltip="Chat on Telegram">
                    <i class="fa-brands fa-telegram"></i>
                </a>
        <?php endif; ?>
        <?php if (!empty($company['whatsapp'])): ?>
                <a href="<?= htmlspecialchars($company['whatsapp']) ?>" class="whatsapp-float" target="_blank"
                    rel="noopener noreferrer" data-tooltip="Order on WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
        <?php endif; ?>
    </div>

    <!-- Favorite Email Prompt Modal -->
    <div id="favModal" class="fav-modal">
        <div class="fav-modal-content">
            <div style="text-align: right; margin-top: -20px; margin-right: -20px;">
                <i class="fa-solid fa-xmark" style="cursor: pointer; color: #888;" onclick="closeFavModal()"></i>
            </div>
            <i class="fa-solid fa-heart" style="color: var(--primary); font-size: 3rem; margin-bottom: 20px;"></i>
            <h2>Love this dish?</h2>
            <p>Please enter your email to save this to your favorites and stay updated with our latest offers!</p>
            <input type="hidden" id="favItemId">
            <input type="email" id="favEmailInput" class="fav-input" placeholder="your@email.com" required>
            <button class="fav-submit-btn" onclick="submitFavEmail()">Save as Favorite</button>
        </div>
    </div>

    <!-- Gallery Modal -->
    <div id="galleryModal" class="app-modal">
        <div class="app-modal-content"
            style="max-width: 800px; padding: 0; overflow: hidden; border-radius: 20px; background: #fff;">
            <div style="position: relative; height: 400px;">
                <img id="modalGalleryImg" src="" style="width: 100%; height: 100%; object-fit: cover;">
                <button onclick="closeGalleryModal()"
                    style="position: absolute; top: 20px; right: 20px; background: #fff; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(0,0,0,0.2);"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <div style="padding: 40px; text-align: left;">
                <span id="modalGalleryCat"
                    style="font-size: 12px; color: var(--primary); font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 10px;"></span>
                <h2 id="modalGalleryTitle"
                    style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: #1a1512; margin-bottom: 20px; font-weight: 800;">
                </h2>
                <p id="modalGalleryDesc" style="color: #555; font-size: 1.1rem; line-height: 1.8;"></p>
                <div
                    style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                    <span id="modalGalleryDate" style="color: #999; font-size: 0.95rem; font-weight: 500;"></span>
                    <button onclick="closeGalleryModal()" class="btn-primary"
                        style="padding: 12px 30px; border-radius: 30px; background: var(--primary); color: #000; border: none; font-weight: 700; cursor: pointer;">Close
                        Details</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openGalleryModal(img, cat, title, desc, date) {
            document.getElementById('modalGalleryImg').src = img;
            document.getElementById('modalGalleryCat').innerText = cat;
            document.getElementById('modalGalleryTitle').innerText = title;
            document.getElementById('modalGalleryDesc').innerText = desc;
            document.getElementById('modalGalleryDate').innerHTML = '<i class="fa-regular fa-calendar" style="margin-right: 8px;"></i>' + date;
            document.getElementById('galleryModal').style.display = 'flex';
        }

        function closeGalleryModal() {
            document.getElementById('galleryModal').style.display = 'none';
        }

        function openCEOModal(img, cat, title, desc, role) {
            document.getElementById('modalGalleryImg').src = img;
            document.getElementById('modalGalleryCat').innerText = cat;
            document.getElementById('modalGalleryTitle').innerText = title;
            document.getElementById('modalGalleryDesc').innerText = desc;
            document.getElementById('modalGalleryDate').innerHTML = '<i class="fa-solid fa-user-tie" style="margin-right: 8px;"></i>' + role;
            document.getElementById('galleryModal').style.display = 'flex';
        }

        window.addEventListener('load', function () {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                setTimeout(() => {
                    preloader.classList.add('fade-out');
                }, 600);
            }
        });
        // Page Transition Animation
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href && href !== '#' && !href.startsWith('http') && !href.includes('#') && !this.target) {
                    e.preventDefault();
                    document.getElementById('preloader').classList.remove('fade-out');
                    setTimeout(() => {
                        window.location.href = href;
                    }, 500);
                }
            });
        });
    </script>
</body>

</html>