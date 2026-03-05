<?php
// Support both local XAMPP and cloud hosting (Render/Railway)
// 1. Get Environment Variables with robust detection and fallbacks
$raw_host = getenv('BLOOM_DB_HOST') ?: getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: getenv('DATABASE_HOST');

// 2. Render-Specific Fix: Ignore auto-injected Postgres hosts (starting with dpg-)
if (strpos($raw_host, 'dpg-') !== false) {
    $host = 'mysql'; // Force fallback to our private MySQL service name
} else {
    $host = $raw_host;
}

$dbname = getenv('BLOOM_DB_NAME') ?: getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'bloom_africa';
$username = getenv('BLOOM_DB_USER') ?: getenv('DB_USER') ?: getenv('MYSQLUSER') ?: 'root';
$password = getenv('BLOOM_DB_PASS') ?: getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: '';
$port = getenv('BLOOM_DB_PORT') ?: getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306';

// 3. Environment Logic
if (empty($host) || $host === 'localhost') {
    if (getenv('RENDER')) {
        $host = 'mysql';
    } else {
        $host = '127.0.0.1';
    }
}

// Ensure we NEVER use 'localhost' (which triggers socket files on Linux)
if ($host === 'localhost') {
    $host = '127.0.0.1';
}

// 3. Connection Loop with Retries
$max_retries = 5;
$retry_delay = 3;
$pdo = null;

for ($i = 0; $i < $max_retries; $i++) {
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 3, // Short timeout per attempt
        ];
        $pdo = new PDO($dsn, $username, $password ?: '', $options);
        break;
    } catch (PDOException $e) {
        $err = $e->getMessage();

        // Fast-fail if host is unreachable/unknown - No point in waiting 100s
        if (strpos($err, 'getaddrinfo failed') !== false || strpos($err, 'Name or service not known') !== false) {
            if ($i === 0 && $host === 'mysql') {
                // On some Render setups, '127.0.0.1' works better if linked via Docker
                $host = '127.0.0.1';
                continue;
            }
        }

        if ($i === $max_retries - 1) {
            $diag = "\n\n[System Info]\nHost: $host\nEnv: " . (getenv('RENDER') ? 'Cloud' : 'Local');
            die("Database Connection Error. The server is still starting up or the configuration is incorrect." . $diag);
        }

        sleep($retry_delay);
    }
}


$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 6. Table Creation and Seeding (All wrapped in one block)
try {
    $setup_queries = "
    CREATE TABLE IF NOT EXISTS menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        image_url VARCHAR(255),
        available BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(255) DEFAULT 'Guest',
        customer_phone VARCHAR(50) DEFAULT NULL,
        order_details TEXT NOT NULL,
        total_amount DECIMAL(10, 2) DEFAULT 0.00,
        platform ENUM('Website', 'WhatsApp', 'Telegram') DEFAULT 'Website',
        status ENUM('Pending', 'Chat Initiated', 'Preparing', 'Ready', 'Delivered', 'Cancelled') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS tables (
        id INT AUTO_INCREMENT PRIMARY KEY,
        table_number INT NOT NULL UNIQUE,
        capacity INT NOT NULL,
        status ENUM('Available', 'Occupied', 'Reserved') DEFAULT 'Available'
    );

    CREATE TABLE IF NOT EXISTS reservations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        reservation_date DATE NOT NULL,
        reservation_time TIME NOT NULL,
        table_number INT,
        guests INT NOT NULL,
        status ENUM('Pending', 'Confirmed', 'Rejected') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS employees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_number VARCHAR(50) UNIQUE,
        name VARCHAR(255) NOT NULL,
        first_name VARCHAR(100),
        middle_name VARCHAR(100),
        last_name VARCHAR(100),
        role VARCHAR(100) NOT NULL,
        salary_type ENUM('Monthly', 'Daily', 'Hourly') DEFAULT 'Monthly',
        email VARCHAR(255) UNIQUE,
        phone VARCHAR(50),
        address TEXT,
        emergency_contact_name VARCHAR(255),
        emergency_contact_phone VARCHAR(50),
        date_of_birth DATE,
        gender ENUM('Male', 'Female', 'Other'),
        salary DECIMAL(10, 2),
        join_date DATE,
        hire_date DATE,
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        bio TEXT,
        photo VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        attendance_date DATE NOT NULL,
        check_in DATETIME DEFAULT NULL,
        check_out DATETIME DEFAULT NULL,
        work_hours DECIMAL(10, 2) DEFAULT 0,
        overtime_hours DECIMAL(10, 2) DEFAULT 0,
        late_minutes INT DEFAULT 0,
        status ENUM('Present', 'Absent', 'Late', 'Half Day') DEFAULT 'Present',
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS salary_advances (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        advance_date DATE NOT NULL,
        reason TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS payroll (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        salary_month VARCHAR(20) NOT NULL,
        year INT,
        base_salary DECIMAL(10, 2) NOT NULL,
        bonus DECIMAL(10, 2) DEFAULT 0,
        deductions DECIMAL(10, 2) DEFAULT 0,
        net_salary DECIMAL(10, 2) NOT NULL,
        working_days INT DEFAULT 26,
        present_days INT DEFAULT 0,
        absent_days INT DEFAULT 0,
        late_count INT DEFAULT 0,
        total_overtime_hours DECIMAL(10, 2) DEFAULT 0,
        overtime_amount DECIMAL(10, 2) DEFAULT 0,
        advance_deduction DECIMAL(10, 2) DEFAULT 0,
        status ENUM('Paid', 'Unpaid') DEFAULT 'Unpaid',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        action VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS jobs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        type VARCHAR(50) NOT NULL,
        location VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        closing_date DATE,
        status ENUM('Active', 'Closed') DEFAULT 'Active',
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS job_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        job_id INT NOT NULL,
        applicant_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        resume_url VARCHAR(255),
        photo_url VARCHAR(255),
        cover_letter TEXT,
        status ENUM('Pending', 'Reviewed', 'Accepted', 'Rejected') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS company_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        address VARCHAR(255) NOT NULL,
        about_text TEXT,
        facebook VARCHAR(255),
        instagram VARCHAR(255),
        twitter VARCHAR(255),
        tiktok VARCHAR(255),
        linkedin VARCHAR(255),
        telegram VARCHAR(255),
        whatsapp VARCHAR(255),
        ceo_name VARCHAR(255),
        ceo_title VARCHAR(255),
        ceo_message TEXT,
        ceo_image VARCHAR(255),
        hero_title VARCHAR(255),
        hero_subtitle TEXT,
        hero_button_text VARCHAR(100),
        hero_image VARCHAR(255),
        about_subtitle TEXT,
        about_image_main VARCHAR(255),
        about_image_sub1 VARCHAR(255),
        about_image_sub2 VARCHAR(255),
        history_title VARCHAR(255),
        history_text1 TEXT,
        history_text2 TEXT,
        dev_name VARCHAR(255),
        dev_photo VARCHAR(255),
        dev_email VARCHAR(255),
        dev_phone VARCHAR(50),
        copyright_text VARCHAR(255)
    );

    CREATE TABLE IF NOT EXISTS team_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        role VARCHAR(100) NOT NULL,
        image_url VARCHAR(255),
        order_index INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_url VARCHAR(255) NOT NULL,
        category VARCHAR(100) DEFAULT 'General',
        title VARCHAR(255),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('Admin', 'Manager', 'Supervisor', 'Waiter') DEFAULT 'Admin',
        permissions TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        image_url VARCHAR(255),
        video_url VARCHAR(255),
        icon VARCHAR(100),
        category ENUM('Food Delivery', 'Catering Service', 'Wedding Events', 'Birthday Parties', 'Corporate Events', 'Others') DEFAULT 'Others',
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS chat_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(255) NOT NULL,
        sender ENUM('User', 'Bot', 'Admin') NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS chat_sessions (
        session_id VARCHAR(255) PRIMARY KEY,
        customer_name VARCHAR(255) NOT NULL,
        customer_email VARCHAR(255) NOT NULL,
        customer_phone VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS construction_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(50),
        address VARCHAR(255),
        ome_page_url VARCHAR(255),
        blog_url VARCHAR(255),
        portfolio_url VARCHAR(255),
        why_choose_us_msg TEXT,
        services_desc TEXT,
        review_image VARCHAR(255),
        review_text TEXT,
        facebook VARCHAR(255),
        twitter VARCHAR(255),
        linkedin VARCHAR(255),
        google_plus VARCHAR(255),
        youtube VARCHAR(255),
        instagram VARCHAR(255),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS construction_projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        status ENUM('Planning', 'Ongoing', 'Completed', 'On Hold') DEFAULT 'Planning',
        image_url VARCHAR(255),
        start_date DATE,
        completion_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS construction_equipment (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        serial_number VARCHAR(100),
        description TEXT,
        status ENUM('Available', 'In Use', 'Maintenance', 'Retired') DEFAULT 'Available',
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS construction_features (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        description TEXT,
        icon_class VARCHAR(100),
        icon_image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS construction_services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        image_url VARCHAR(255),
        button_text VARCHAR(100),
        button_url VARCHAR(255),
        icon VARCHAR(100),
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS construction_testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_name VARCHAR(255) NOT NULL,
        client_role VARCHAR(255),
        message TEXT,
        image_url VARCHAR(255),
        rating INT DEFAULT 5,
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS construction_quotes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_name VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        phone VARCHAR(50),
        project_type VARCHAR(100),
        budget ENUM('Budget-friendly', 'Standard', 'Premium', 'Custom', 'Not Specified', 'Economy', 'Luxury') DEFAULT 'Standard',
        message TEXT,
        admin_reply TEXT,
        status ENUM('Pending', 'Contacted', 'Quoted', 'Rejected') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";

    $pdo->exec($setup_queries);

    // 7. Data Seeding
    // Seed company_info if it's empty
    $check_company = $pdo->query("SELECT COUNT(*) FROM company_info")->fetchColumn();
    if ($check_company == 0) {
        $pdo->exec("INSERT INTO company_info (company_name, email, phone, address, about_text, dev_name, dev_email, dev_phone) VALUES ('Bloom Africa Restaurant', 'info@bloomafrica.com', '+251 900 123 456', 'Addis Ababa, Ethiopia', 'Experience authentic African cuisine.', 'Mequannent Gashaw', 'meqalimaz2015@gmail.com', '+251 918 592 028')");
    }

    // Seed construction_info if it's empty
    $check_const = $pdo->query("SELECT COUNT(*) FROM construction_info")->fetchColumn();
    if ($check_const == 0) {
        $pdo->exec("INSERT INTO construction_info (company_name, email, phone, address, why_choose_us_msg, services_desc) VALUES ('Bloom Construction', 'info@bloomconstruction.et', '+251 911 222 333', 'Addis Ababa, Ethiopia', 'Quality and Excellence in every build.', 'Leading construction services in Ethiopia.')");
    }

    // Seed construction_services if empty
    $check_const_services = $pdo->query("SELECT COUNT(*) FROM construction_services")->fetchColumn();
    if ($check_const_services == 0) {
        $pdo->exec("INSERT INTO construction_services (title, description, image_url, button_text, button_url) VALUES 
            ('Construction Management', 'Full lifecycle management of your construction project, ensuring site safety, resource efficiency, and regulatory compliance from start to finish.', 'Construction/Images/card1.jpg', 'Learn More', '#'), 
            ('Renovation', 'Modernizing existing structures with the latest materials and designs, breathing new life into your residential or commercial space.', 'Construction/Images/card2.jpg', 'Learn More', '#'), 
            ('Interior Design', 'Creating functional and aesthetically pleasing interior spaces tailored to your personal style and operational needs.', 'Construction/Images/card3.jpg', 'Learn More', '#')");
    }

    // Seed construction_projects if empty
    $check_const_projects = $pdo->query("SELECT COUNT(*) FROM construction_projects")->fetchColumn();
    if ($check_const_projects == 0) {
        $pdo->exec("INSERT INTO construction_projects (title, description, status, image_url, category) VALUES 
            ('Skyline Tower', 'A modern 40-story commercial skyscraper featuring sustainable materials and state-of-the-art energy systems.', 'Active', 'Construction/Images/gallery1.jpg', 'Commercial'),
            ('Oceanview Residences', 'Luxury residential complex with panoramic ocean views, infinity pools, and high-end finishes throughout.', 'Active', 'Construction/Images/gallery2.jpg', 'Residential'),
            ('City Center Mall', 'Massive retail and entertainment complex in the heart of the city, bringing over 200 premium brands together.', 'Active', 'Construction/Images/gallery3.jpg', 'Commercial')");
    }

    // Seed default admin if empty
    $check_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($check_users == 0) {
        $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (full_name, email, password, role) VALUES ('System Admin', 'admin@bloomafrica.com', '$admin_pass', 'Admin')");
    }

    // Seed tables if empty
    $check_tables = $pdo->query("SELECT COUNT(*) FROM tables")->fetchColumn();
    if ($check_tables == 0) {
        $tables_to_seed = [
            [1, 2],
            [2, 2],
            [3, 2],
            [4, 2],
            [5, 4],
            [6, 4],
            [7, 4],
            [8, 6],
            [9, 8],
            [10, 12]
        ];
        $stmt_seed = $pdo->prepare("INSERT INTO tables (table_number, capacity) VALUES (?, ?)");
        foreach ($tables_to_seed as $t) {
            $stmt_seed->execute($t);
        }
    }

} catch (PDOException $e) {
    die("Database Setup Error: " . $e->getMessage());
}
?>