<?php
session_start();
require_once 'db.php';

// Auth logic
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Auth Logic (Updated for dynamic users)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_pic'] = $user['profile_pic'];
            $_SESSION['login_time'] = date('Y-m-d H:i:s');
            header("Location: admin.php");
            exit;
        } else {
            $auth_error = "Invalid email or password.";
        }
    } elseif (isset($_POST['signup'])) {
        $name = $_POST['full_name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $password]);
            $auth_success = "Account created! You can now login.";
        } catch (PDOException $e) {
            $auth_error = "Email already registered.";
        }
    } elseif (isset($_POST['reset'])) {
        // Simple Reset Mock - In reality, would send an email. 
        // For this demo, we'll just show a success message.
        $auth_success = "Password reset link sent to your email!";
    }
}

if (!isset($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bloom Africa | Admin Login</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --primary-gold: #dfb180;
                --bg-dark: #0a0a0a;
                --card-bg: rgba(18, 18, 18, 0.75);
                --input-bg: rgba(255, 255, 255, 0.04);
            }

            body {
                background-color: var(--bg-dark);
                background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), url('login_bg.png');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                color: #fdfbf7;
                font-family: 'Inter', sans-serif;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
                overflow: hidden;
            }

            .login-box {
                background: var(--card-bg);
                padding: 50px 40px;
                border-radius: 24px;
                border: 1px solid rgba(255, 255, 255, 0.1);
                width: 100%;
                max-width: 420px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(20px);
                position: relative;
                z-index: 10;
            }

            .login-box h2 {
                color: var(--primary-gold);
                text-align: center;
                margin-bottom: 35px;
                font-size: 32px;
                font-weight: 700;
            }

            .form-group {
                margin-bottom: 25px;
            }

            .form-group label {
                display: block;
                margin-bottom: 8px;
                color: rgba(255, 255, 255, 0.6);
                font-size: 14px;
            }

            .form-group input {
                width: 100%;
                padding: 14px 18px;
                background: var(--input-bg);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                color: #fff;
                outline: none;
                box-sizing: border-box;
                font-size: 16px;
            }

            .btn-login {
                width: 100%;
                padding: 16px;
                background: var(--primary-gold);
                color: #0d0d0d;
                border: none;
                border-radius: 12px;
                font-weight: 700;
                cursor: pointer;
                transition: 0.4s;
                font-size: 17px;
                margin-top: 10px;
            }

            .btn-login:hover {
                background: #fff;
                transform: translateY(-3px);
            }

            .error {
                background: rgba(255, 107, 107, 0.1);
                border: 1px solid rgba(255, 107, 107, 0.2);
                color: #ff6b6b;
                padding: 12px;
                border-radius: 10px;
                text-align: center;
                margin-bottom: 25px;
            }
        </style>
    </head>

    <body>
        <div class="login-box">
            <h2>Bloom Admin</h2>

            <?php if (isset($auth_error)): ?>
                <div class="error"><?= $auth_error ?></div>
            <?php endif; ?>

            <?php if (isset($auth_success)): ?>
                <div class="success"
                    style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 12px; border-radius: 10px; text-align: center; margin-bottom: 25px;">
                    <?= $auth_success ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="loginForm" method="POST">
                <input type="hidden" name="login" value="1">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="admin@bloomafrica.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login">Login Access</button>
                <div style="margin-top: 20px; text-align: center; font-size: 14px; color: rgba(255,255,255,0.5);">
                    <a href="javascript:void(0)" onclick="toggleAuth('signup')"
                        style="color: var(--primary-gold); text-decoration: none;">Create Account</a>
                    <span style="margin: 0 10px;">|</span>
                    <a href="javascript:void(0)" onclick="toggleAuth('forgot')"
                        style="color: var(--primary-gold); text-decoration: none;">Forgot Password?</a>
                </div>
            </form>

            <!-- Signup Form (Hidden) -->
            <form id="signupForm" method="POST" style="display: none;">
                <input type="hidden" name="signup" value="1">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="email@example.com" required>
                </div>
                <div class="form-group">
                    <label>Create Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login" style="background: #10b981;">Sign Up Now</button>
                <div style="margin-top: 20px; text-align: center; font-size: 14px; color: rgba(255,255,255,0.5);">
                    Already have an account? <a href="javascript:void(0)" onclick="toggleAuth('login')"
                        style="color: var(--primary-gold); text-decoration: none;">Back to Login</a>
                </div>
            </form>

            <!-- Forgot Password Form (Hidden) -->
            <form id="forgotForm" method="POST" style="display: none;">
                <input type="hidden" name="reset" value="1">
                <p style="font-size: 13px; color: rgba(255,255,255,0.6); text-align: center; margin-bottom: 25px;">Enter
                    your email to receive a password reset link.</p>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="email@example.com" required>
                </div>
                <button type="submit" class="btn-login">Send Reset Link</button>
                <div style="margin-top: 20px; text-align: center; font-size: 14px; color: rgba(255,255,255,0.5);">
                    <a href="javascript:void(0)" onclick="toggleAuth('login')"
                        style="color: var(--primary-gold); text-decoration: none;">Back to Login</a>
                </div>
            </form>
        </div>

        <script>         function toggleAuth(type) {
                const forms = ['loginForm', 'signupForm', 'forgotForm']; forms.forEach(f => document.getElementById(f).style.display = 'none'); document.getElementById(type + 'Form').style.display = 'block';
                const titles = { login: 'Bloom Admin', signup: 'Sign Up', forgot: 'Reset Password' }; document.querySelector('.login-box h2').innerText = titles[type];
            }
        </script>
    </body>

    </html>
    <?php
    exit;
}

// ------------------------------------------------------------------
// Main Dashboard Logic (Keep it clean by including sub-files)
// ------------------------------------------------------------------

// Include Handlers (CRUD)
require_once 'admin_tabs/handlers.php';

// Data fetching for Dashboard
$active_tab = $_GET['tab'] ?? 'dashboard';
$res_count = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$order_count = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='Pending'")->fetchColumn();
$menu_count = $pdo->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();
$staff_count = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
$emp_present_today = $pdo->query("SELECT COUNT(*) FROM attendance WHERE attendance_date=CURDATE() AND (status='Present' OR status='Late')")->fetchColumn();
$pending_salary = $pdo->query("SELECT COUNT(*) FROM payroll WHERE status='Unpaid'")->fetchColumn();

// Chart Data
$cat_distribution = $pdo->query("SELECT category, COUNT(*) as count FROM menu_items GROUP BY category")->fetchAll();
$chart_labels = json_encode(array_column($cat_distribution, 'category'));
$chart_data = json_encode(array_column($cat_distribution, 'count'));

// Weekly Performance Data (Last 7 Days)
$perf_data = [];
$perf_labels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dayName = date('l', strtotime($date));
    $count = $pdo->prepare("SELECT COUNT(*) FROM activity_logs WHERE DATE(created_at) = ?");
    $count->execute([$date]);
    $perf_data[] = (int) $count->fetchColumn();
    $perf_labels[] = $dayName;
}

// Convert numbers to percentages for "Performance %"
$max_val = max($perf_data) ?: 1;
$perf_percent = array_map(function ($v) use ($max_val) {
    return round(($v / $max_val) * 100);
}, $perf_data);

$perf_labels_json = json_encode($perf_labels);
$perf_data_json = json_encode($perf_percent);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloom Africa Admin | Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php include 'admin_tabs/styles.php'; ?>
</head>

<body>

    <div class="sidebar">
        <a href="index.php" class="brand">
            <div class="brand-icon"
                style="padding: 0; overflow: hidden; border: 2px solid rgba(255,255,255,0.2); width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                <img src="admin_logo.png" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            Bloom Admin
        </a>
        <div class="nav-items" style="margin-top: 10px;">
            <a href="?tab=dashboard" class="nav-item <?= $active_tab == 'dashboard' ? 'active' : '' ?>"><i
                    class="fa-solid fa-chart-pie"></i> Dashboard</a>
            <a href="?tab=profile" class="nav-item <?= $active_tab == 'profile' ? 'active' : '' ?>"><i
                    class="fa-solid fa-user-gear"></i> My Profile</a>

            <!-- Restaurant Management Dropdown -->
            <div class="nav-dropdown <?= in_array($active_tab, ['menu', 'reservations', 'gallery']) ? 'open' : '' ?>">
                <div class="nav-dropdown-toggle" onclick="this.parentElement.classList.toggle('open')">
                    <span><i class="fa-solid fa-utensils"></i> Restaurant Mgmt</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="submenu">
                    <a href="?tab=menu" class="nav-item <?= $active_tab == 'menu' ? 'active' : '' ?>"><i
                            class="fa-solid fa-list-check"></i> Menu Mgmt</a>
                    <a href="?tab=reservations" class="nav-item <?= $active_tab == 'reservations' ? 'active' : '' ?>"><i
                            class="fa-solid fa-calendar-check"></i> Reservations</a>
                    <a href="?tab=gallery" class="nav-item <?= $active_tab == 'gallery' ? 'active' : '' ?>"><i
                            class="fa-solid fa-images"></i> Gallery</a>
                </div>
            </div>

            <!-- HR & Payroll Dropdown -->
            <div class="nav-dropdown <?= in_array($active_tab, ['staff', 'attendance', 'payroll']) ? 'open' : '' ?>">
                <div class="nav-dropdown-toggle" onclick="this.parentElement.classList.toggle('open')">
                    <span><i class="fa-solid fa-users-gear"></i> HR & Payroll</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="submenu">
                    <a href="?tab=staff" class="nav-item <?= $active_tab == 'staff' ? 'active' : '' ?>"><i
                            class="fa-solid fa-users"></i> Staff Directory</a>
                    <a href="?tab=attendance" class="nav-item <?= $active_tab == 'attendance' ? 'active' : '' ?>"><i
                            class="fa-solid fa-user-clock"></i> Attendance</a>
                    <a href="?tab=payroll" class="nav-item <?= $active_tab == 'payroll' ? 'active' : '' ?>"><i
                            class="fa-solid fa-money-bill-transfer"></i> Payroll Dept</a>
                </div>
            </div>

            <!-- Recruitment Dropdown -->
            <div class="nav-dropdown <?= in_array($active_tab, ['jobs', 'applications']) ? 'open' : '' ?>">
                <div class="nav-dropdown-toggle" onclick="this.parentElement.classList.toggle('open')">
                    <span><i class="fa-solid fa-briefcase"></i> Recruitment</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="submenu">
                    <a href="?tab=jobs" class="nav-item <?= $active_tab == 'jobs' ? 'active' : '' ?>"><i
                            class="fa-solid fa-clipboard-list"></i> Job Listings</a>
                    <a href="?tab=applications" class="nav-item <?= $active_tab == 'applications' ? 'active' : '' ?>"><i
                            class="fa-solid fa-file-signature"></i> Applications</a>
                </div>
            </div>

            <a href="?tab=services" class="nav-item <?= $active_tab == 'services' ? 'active' : '' ?>"><i
                    class="fa-solid fa-concierge-bell"></i> Our Services</a>
            <a href="?tab=company" class="nav-item <?= $active_tab == 'company' ? 'active' : '' ?>"><i
                    class="fa-solid fa-info-circle"></i> Portal Info</a>
            <a href="?tab=recycle_bin" class="nav-item <?= $active_tab == 'recycle_bin' ? 'active' : '' ?>"><i
                    class="fa-solid fa-trash-can"></i> Recycle Bin</a>
            <a href="?tab=activity" class="nav-item <?= $active_tab == 'activity' ? 'active' : '' ?>"><i
                    class="fa-solid fa-history"></i> Activity Logs</a>
            <a href="?tab=chatbot" class="nav-item <?= $active_tab == 'chatbot' ? 'active' : '' ?>"><i
                    class="fa-solid fa-robot"></i> Chatbot Room</a>
            <a href="?tab=communication" class="nav-item <?= $active_tab == 'communication' ? 'active' : '' ?>"><i
                    class="fa-solid fa-comments"></i> Communication Hub</a>
        </div>
    </div>

    <div class="main-content">
        <!-- Floating Notifications -->
        <?php if (isset($_GET['msg'])): ?>
            <div id="statusToast" class="stat-toast"
                style="position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 15px 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); z-index: 9999; display: flex; align-items: center; gap: 12px; font-weight: 600; animation: slideInRight 0.5s ease;">
                <i class="fa-solid fa-circle-check"></i>
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
            <script>             setTimeout(() => { const toast = document.getElementById('statusToast'); if (toast) { toast.style.animation = 'fadeOut 0.5s ease forwards'; setTimeout(() => toast.remove(), 500); } }, 4000);
            </script>
        <?php endif; ?>

        <header>
            <h1><?= ucfirst($active_tab) ?></h1>
            <div class="profile-container">
                <div class="profile-clickarea" onclick="toggleLogout()">
                    <div style="text-align: right;">
                        <div
                            style="font-weight: 700; color: var(--blue); font-size: 15px; display: flex; align-items: center; gap: 10px; justify-content: flex-end;">
                            <span><?= $_SESSION['admin_name'] ?? 'Admin' ?></span>
                            <div
                                style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden; border: 2px solid var(--accent); background: #f8fafc;">
                                <?php
                                $header_pic = !empty($_SESSION['admin_pic']) ? $_SESSION['admin_pic'] : 'admin_logo.png';
                                ?>
                                <img src="<?= $header_pic ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Logged in at:
                            <?= date('H:i A', strtotime($_SESSION['login_time'] ?? 'now')) ?>
                        </div>
                    </div>
                </div>
                <div class="logout-dropdown" id="logoutDropdown">
                    <a href="?tab=profile" class="nav-item"><i class="fa-solid fa-user-gear"></i> My Profile</a>
                    <a href="?logout=1" class="nav-item" style="color: #ef4444 !important;"><i
                            class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </div>
            </div>
        </header>

        <div class="tab-content">
            <?php
            // Direct tab loader
            $tab_file = "admin_tabs/{$active_tab}.php";
            if (file_exists($tab_file)) {
                include $tab_file;
            } else {
                echo "<div class='card'><h2>Tab '{$active_tab}' not found.</h2></div>";
            }
            ?>
        </div>
    </div>

    <?php include 'admin_tabs/scripts.php'; ?>
</body>

</html>