<?php
// Support both local XAMPP and cloud hosting (Render/Railway)
// 1. Get Environment Variables with robust detection
$host = getenv('BLOOM_DB_HOST');
$dbname = getenv('BLOOM_DB_NAME') ?: 'bloom_africa';
$username = getenv('BLOOM_DB_USER') ?: 'root';
$password = getenv('BLOOM_DB_PASS'); // Can be empty
$port = getenv('BLOOM_DB_PORT') ?: '3306';

// 2. Default to internal connection (All-in-One Stack)
if (!$host || $host === 'bloom-db' || $host === 'bloom-mysql' || $host === 'mysql') {
    $host = '127.0.0.1';
}

// Ensure we NEVER use 'localhost' (which triggers socket files on Linux)
if ($host === 'localhost') {
    $host = '127.0.0.1';
}

// 4. Connection Loop with Retries
$max_retries = 5;
$retry_delay = 5; // seconds
$pdo = null;

for ($i = 0; $i < $max_retries; $i++) {
    try {
        // Attempt 1: Connect directly to the database
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password ?: '');
        break; // Success!
    } catch (PDOException $e) {
        // Attempt 2: Connect to host only and create DB (if it doesn't exist)
        try {
            $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password ?: '');
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$dbname`");
            break; // Success!
        } catch (PDOException $e2) {
            if ($i === $max_retries - 1) {
                die("Critical: Database connection failed after $max_retries attempts. Target Host: $host:$port. Error: " . $e2->getMessage());
            }
            sleep($retry_delay);
        }
    }
}

// 5. Final Connection Check
if (!$pdo) {
    die("Critical: Could not establish a database connection.");
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

    CREATE TABLE IF NOT EXISTS favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        menu_item_id INT NOT NULL,
        customer_email VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS recycle_bin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        table_name VARCHAR(100) NOT NULL,
        record_id INT NOT NULL,
        record_data JSON NOT NULL,
        deleted_by VARCHAR(255),
        deletion_reason TEXT,
        deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
        title VARCHAR(20),
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
        admin_name VARCHAR(255),
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
        hero_video VARCHAR(255),
        hero_audio VARCHAR(255),
        hero2_title VARCHAR(255),
        hero2_subtitle TEXT,
        hero2_button_text VARCHAR(100),
        hero2_image VARCHAR(255),
        hero3_title VARCHAR(255),
        hero3_subtitle TEXT,
        hero3_button_text VARCHAR(100),
        hero3_image VARCHAR(255),
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
        profile_pic VARCHAR(255),
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
        image_path VARCHAR(255) DEFAULT NULL,
        location_lat DECIMAL(10, 8) DEFAULT NULL,
        location_lng DECIMAL(11, 8) DEFAULT NULL,
        is_read BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS chat_sessions (
        session_id VARCHAR(255) PRIMARY KEY,
        customer_name VARCHAR(255) NOT NULL,
        customer_email VARCHAR(255) NOT NULL,
        customer_phone VARCHAR(50) NOT NULL,
        department VARCHAR(50) DEFAULT 'Restaurant',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS construction_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(255),
        hero_title VARCHAR(255),
        hero_subtitle TEXT,
        hero_description TEXT,
        hero_image VARCHAR(255),
        hero_video VARCHAR(255),
        why_choose_us_title VARCHAR(255),
        why_choose_us_subtitle TEXT,
        services_title VARCHAR(255),
        services_subtitle TEXT,
        projects_title VARCHAR(255),
        projects_subtitle TEXT,
        reviews_title VARCHAR(255),
        reviews_subtitle TEXT,
        quote_title VARCHAR(255),
        quote_subtitle TEXT,
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
        title VARCHAR(255) NOT NULL,
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

    // --- ROBUST SCHEMA SYNC (Add missing columns to existing tables) ---
    $sync_tasks = [
        'activity_logs' => ['admin_name' => "VARCHAR(255) AFTER action"],
        'menu_items' => ['likes' => "INT DEFAULT 0 AFTER price"],
        'construction_projects' => [
            'start_date' => "DATE AFTER image_url",
            'completion_date' => "DATE AFTER start_date"
        ],
        'construction_equipment' => ['serial_number' => "VARCHAR(100) UNIQUE AFTER name"],
        'construction_info' => [
            'hero_title' => "VARCHAR(255)",
            'hero_subtitle' => "TEXT",
            'hero_description' => "TEXT",
            'hero_image' => "VARCHAR(255)",
            'hero_video' => "VARCHAR(255)",
            'why_choose_us_title' => "VARCHAR(255)",
            'why_choose_us_subtitle' => "TEXT",
            'services_title' => "VARCHAR(255)",
            'services_subtitle' => "TEXT",
            'projects_title' => "VARCHAR(255)",
            'projects_subtitle' => "TEXT",
            'reviews_title' => "VARCHAR(255)",
            'reviews_subtitle' => "TEXT",
            'quote_title' => "VARCHAR(255)",
            'quote_subtitle' => "TEXT"
        ],
        'company_info' => [
            'hero_button_text' => "VARCHAR(100)",
            'hero_video' => "VARCHAR(255)",
            'hero_audio' => "VARCHAR(255)",
            'hero2_title' => "VARCHAR(255)",
            'hero2_subtitle' => "TEXT",
            'hero2_button_text' => "VARCHAR(100)",
            'hero2_image' => "VARCHAR(255)",
            'hero3_title' => "VARCHAR(255)",
            'hero3_subtitle' => "TEXT",
            'hero3_button_text' => "VARCHAR(100)",
            'hero3_image' => "VARCHAR(255)"
        ],
        'chat_sessions' => ['department' => "VARCHAR(50) DEFAULT 'Restaurant' AFTER customer_phone"],
        'chat_messages' => ['is_read' => "BOOLEAN DEFAULT 0 AFTER location_lng"],
        'employees' => [
            'id_number' => "VARCHAR(50) UNIQUE AFTER id",
            'title' => "VARCHAR(20) AFTER id_number",
            'first_name' => "VARCHAR(100) AFTER name",
            'middle_name' => "VARCHAR(100) AFTER first_name",
            'last_name' => "VARCHAR(100) AFTER middle_name",
            'salary_type' => "ENUM('Monthly', 'Daily', 'Hourly') DEFAULT 'Monthly' AFTER role",
            'salary' => "DECIMAL(10, 2) AFTER gender",
            'hire_date' => "DATE AFTER join_date",
            'bio' => "TEXT AFTER status",
            'photo' => "VARCHAR(255) AFTER bio"
        ],
        'users' => [
            'profile_pic' => "VARCHAR(255) AFTER role",
            'permissions' => "TEXT AFTER profile_pic"
        ],
        'gallery' => [
            'title' => "VARCHAR(255) AFTER image_url",
            'description' => "TEXT AFTER title"
        ],
        'services' => [
            'icon' => "VARCHAR(100) AFTER description",
            'video_url' => "VARCHAR(255) AFTER icon",
            'category' => "ENUM('Food Delivery', 'Catering Service', 'Wedding Events', 'Birthday Parties', 'Corporate Events', 'Others') DEFAULT 'Others' AFTER video_url",
            'status' => "ENUM('Active', 'Inactive') DEFAULT 'Active' AFTER category"
        ]
    ];

    foreach ($sync_tasks as $table => $columns) {
        try {
            $stmt = $pdo->query("DESCRIBE `$table`");
            $existing_cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $existing_cols = array_map('strtolower', $existing_cols);

            foreach ($columns as $col => $definition) {
                if (!in_array(strtolower($col), $existing_cols)) {
                    try {
                        $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$col` $definition");
                    } catch (Exception $e2) {
                        $clean_def = preg_replace('/AFTER \w+/i', '', $definition);
                        $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$col` $clean_def");
                    }
                }
            }
        } catch (Exception $e) { /* Table might not exist yet */
        }
    }

    // Special case: Ensure construction_projects uses 'title' not 'name'
    try {
        $cols = $pdo->query("DESCRIBE construction_projects")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('name', $cols) && !in_array('title', $cols)) {
            $pdo->exec("ALTER TABLE construction_projects CHANGE COLUMN `name` `title` VARCHAR(255) NOT NULL");
        }
    } catch (Exception $e) {
    }



    // 7. Data Seeding
    // Seed company_info if it's empty
    $check_company = $pdo->query("SELECT COUNT(*) FROM company_info")->fetchColumn();
    if ($check_company == 0) {
        $pdo->exec("INSERT INTO company_info (company_name, email, phone, address, about_text, dev_name, dev_email, dev_phone) VALUES ('Bloom Africa Restaurant', 'info@bloomafrica.com', '+251 900 123 456', 'Addis Ababa, Ethiopia', 'Experience authentic African cuisine.', 'Mequannent Gashaw', 'meqalimaz2015@gmail.com', '+251 918 592 028')");
    }

    // Seed construction_info if it's empty
    $check_const = $pdo->query("SELECT COUNT(*) FROM construction_info")->fetchColumn();
    if ($check_const == 0) {
        $pdo->exec("INSERT INTO construction_info (company_name, hero_title, hero_image, email, phone, address, why_choose_us_msg, services_desc, review_text, review_image) VALUES 
            ('Bloom Construction', 'WELCOME TO OUR COMPANY', 'uploads/const/hero_1772692960.jpg', 'info@bloomconstruction.et', '+251 911 222 333', 'Addis Ababa, Ethiopia', 'Quality and Excellence in every build.', 'Leading construction services in Ethiopia.', 'The team delivered our project ahead of schedule with exceptional attention to detail. Highly recommend for any major construction work in Addis.', 'uploads/const/review_1772692350.png')");
    } else {
        // Force update user's specific image and messages back to local defaults for a perfect sync
        $pdo->exec("UPDATE construction_info SET 
            hero_image = 'uploads/const/hero_1772692960.jpg', 
            hero_title = 'WELCOME TO OUR COMPANY',
            company_name = 'Bloom Construction',
            why_choose_us_msg = 'Quality and Excellence in every build.',
            services_desc = 'Leading construction services in Ethiopia.',
            review_text = 'The team delivered our project ahead of schedule with exceptional attention to detail. Highly recommend for any major construction work in Addis.',
            review_image = 'uploads/const/review_1772692350.png'
            WHERE id = 1");
    }

    // Ensure uploads directory exists for chatbot images
    if (!file_exists('uploads/chat')) {
        @mkdir('uploads/chat', 0777, true);
    }


    // Check if we need to seed the base structure (if services are missing)
    $check_seed = $pdo->query("SELECT COUNT(*) FROM construction_services")->fetchColumn();
    if ($check_seed < 3) {
        // Seed construction_services (Using user's local images)
        $pdo->exec("INSERT IGNORE INTO construction_services (title, description, image_url, button_text, button_url) VALUES 
            ('Construction Management', 'Full lifecycle management of your construction project, ensuring site safety, resource efficiency, and regulatory compliance from start to finish.', 'Construction/Images/card1.jpg', 'Learn More', '#'), 
            ('Renovation', 'Modernizing existing structures with the latest materials and designs, breathing new life into your residential or commercial space.', 'Construction/Images/card2.jpg', 'Learn More', '#'), 
            ('Interior Design', 'Creating functional and aesthetically pleasing interior spaces tailored to your personal style and operational needs.', 'Construction/Images/card3.jpg', 'Learn More', '#')");

        $pdo->exec("UPDATE construction_services SET image_url = 'Construction/Images/card1.jpg' WHERE title='Construction Management'");
        $pdo->exec("UPDATE construction_services SET image_url = 'Construction/Images/card2.jpg' WHERE title='Renovation'");
        $pdo->exec("UPDATE construction_services SET image_url = 'Construction/Images/card3.jpg' WHERE title='Interior Design'");

        // Seed construction_projects
        $pdo->exec("INSERT IGNORE INTO construction_projects (title, description, status, image_url) VALUES 
            ('Skyline Tower', 'A modern 40-story commercial skyscraper featuring sustainable materials and state-of-the-art energy systems.', 'Ongoing', 'Construction/Images/gallery1.jpg'),
            ('Oceanview Residences', 'Luxury residential complex with panoramic ocean views, infinity pools, and high-end finishes throughout.', 'Ongoing', 'Construction/Images/gallery2.jpg'),
            ('City Center Mall', 'Massive retail and entertainment complex in the heart of the city, bringing over 200 premium brands together.', 'Completed', 'Construction/Images/gallery3.jpg')");

        $pdo->exec("UPDATE construction_projects SET image_url = 'Construction/Images/gallery1.jpg' WHERE title='Skyline Tower'");
        $pdo->exec("UPDATE construction_projects SET image_url = 'Construction/Images/gallery2.jpg' WHERE title='Oceanview Residences'");
        $pdo->exec("UPDATE construction_projects SET image_url = 'Construction/Images/gallery3.jpg' WHERE title='City Center Mall'");

        // Seed menu items
        $pdo->exec("INSERT IGNORE INTO menu_items (name, category, price, description, image_url) VALUES 
            ('Doro Wat', 'Main Dish', 450.00, 'Spicy chicken stew slow-cooked with berbere, onions, and garlic, served with a hard-boiled egg.', 'https://images.unsplash.com/photo-1627308595229-7830a5c91f9f?q=80&w=800'),
            ('Kitfo Traditional', 'Main Dish', 550.00, 'Minced raw beef marinated in mitmita (spicy chili powder) and niter kibbeh (clarified butter).', 'https://images.unsplash.com/photo-1541014741259-df549fa3bb68?q=80&w=800'),
            ('Injera Special Wrap', 'Fast Food', 320.00, 'Authentic sourdough flatbread wrapped with various fresh vegetables and lean proteins.', 'https://images.unsplash.com/photo-1589182373726-e4f658ab50f0?q=80&w=800')");

        $pdo->exec("UPDATE menu_items SET image_url = 'https://images.unsplash.com/photo-1627308595229-7830a5c91f9f?q=80&w=800' WHERE name='Doro Wat'");
        $pdo->exec("UPDATE menu_items SET image_url = 'https://images.unsplash.com/photo-1541014741259-df549fa3bb68?q=80&w=800' WHERE name='Kitfo Traditional'");
        $pdo->exec("UPDATE menu_items SET image_url = 'https://images.unsplash.com/photo-1589182373726-e4f658ab50f0?q=80&w=800' WHERE name='Injera Special Wrap'");

        // Seed default admin if empty
        $check_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($check_users == 0) {
            $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->exec("INSERT INTO users (full_name, email, password, role) VALUES ('System Admin', 'admin@bloomafrica.com', '$admin_pass', 'Admin')");
        }

        // Seed tables if empty
        $check_tables = $pdo->query("SELECT COUNT(*) FROM tables")->fetchColumn();
        if ($check_tables == 0) {
            $tables_to_seed = [[1, 2], [2, 2], [3, 2], [4, 2], [5, 4], [6, 4], [7, 4], [8, 6], [9, 8], [10, 12]];
            $stmt_seed = $pdo->prepare("INSERT INTO tables (table_number, capacity) VALUES (?, ?)");
            foreach ($tables_to_seed as $t) {
                $stmt_seed->execute($t);
            }
        }

        // Seed construction_equipment and force update images
        $pdo->exec("INSERT IGNORE INTO construction_equipment (name, serial_number, description, status, image_url) VALUES 
            ('Heavy Duty Excavator', 'EQ-EX-001', 'High-performance hydraulic excavator for major earthmoving and trenching operations.', 'Available', 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?q=80&w=800'),
            ('Tower Crane - 50M', 'EQ-CR-042', 'Reliable vertical transport for high-rise construction projects with precision control systems.', 'In Use', 'https://images.unsplash.com/photo-1541888946425-d81bb19480c5?q=80&w=800'),
            ('Industrial Cement Mixer', 'EQ-MX-099', 'Efficient concrete mixing and delivery for structural foundations and large-scale flooring.', 'Available', 'https://images.unsplash.com/photo-1533160600052-a5676735237c?q=80&w=800')");

        $pdo->exec("UPDATE construction_equipment SET image_url = 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?q=80&w=800' WHERE serial_number='EQ-EX-001'");
        $pdo->exec("UPDATE construction_equipment SET image_url = 'https://images.unsplash.com/photo-1541888946425-d81bb19480c5?q=80&w=800' WHERE serial_number='EQ-CR-042'");
        $pdo->exec("UPDATE construction_equipment SET image_url = 'https://images.unsplash.com/photo-1533160600052-a5676735237c?q=80&w=800' WHERE serial_number='EQ-MX-099'");

        // Seed construction_testimonials
        $pdo->exec("INSERT IGNORE INTO construction_testimonials (client_name, client_role, message, rating, status, image_url) VALUES 
            ('Samuel Teketo', 'Project Manager, UrbanDev', 'Bloom Construction delivered our high-rise project ahead of schedule with exceptional quality. Their engineering team is second to none.', 5, 'Active', 'https://i.pravatar.cc/150?u=sam'),
            ('Lydia Gashaw', 'CEO, Rift Valley Estates', 'The attention to detail in our renovation project was amazing. They turned our vision into reality while maintaining strict safety standards.', 5, 'Active', 'https://i.pravatar.cc/150?u=lydia'),
            ('Dr. Mequannent G.', 'University Coordinator', 'Professionalism and integrity are the core of Bloom. They handled our lab extension with the utmost care and precision.', 5, 'Active', 'https://i.pravatar.cc/150?u=meq')");

        $pdo->exec("UPDATE construction_testimonials SET message = 'Bloom Construction delivered our high-rise project ahead of schedule with exceptional quality. Their engineering team is second to none.' WHERE client_name='Samuel Teketo'");
        $pdo->exec("UPDATE construction_testimonials SET message = 'The attention to detail in our renovation project was amazing. They turned our vision into reality while maintaining strict safety standards.' WHERE client_name='Lydia Gashaw'");
        $pdo->exec("UPDATE construction_testimonials SET message = 'Professionalism and integrity are the core of Bloom. They handled our lab extension with the utmost care and precision.' WHERE client_name='Dr. Mequannent G.'");

        // Seed careers (Job Listings)
        $pdo->exec("INSERT IGNORE INTO jobs (title, category, type, location, description, closing_date, status) VALUES 
            ('Executive Chef', 'Kitchen', 'Full-time', 'Addis Ababa', 'We are looking for a creative and experienced Executive Chef to lead our kitchen team, design menus, and maintain high standards of culinary excellence.', '2026-04-30', 'Active'),
            ('Restaurant Manager', 'Management', 'Full-time', 'Addis Ababa', 'Seeking a dynamic Restaurant Manager to oversee daily operations, ensure exceptional guest service, and manage staff to achieve business goals.', '2026-04-15', 'Active'),
            ('Senior Civil Engineer', 'Construction', 'Full-time', 'Project Site', 'Responsible for overseeing large-scale infrastructure projects, ensuring technical accuracy, and managing onsite engineering teams for Bloom Construction.', '2026-05-20', 'Active')");

        $pdo->exec("UPDATE jobs SET category = 'Kitchen', type = 'Full-time', location = 'Addis Ababa' WHERE title='Executive Chef'");
        $pdo->exec("UPDATE jobs SET category = 'Management', type = 'Full-time', location = 'Addis Ababa' WHERE title='Restaurant Manager'");
        $pdo->exec("UPDATE jobs SET category = 'Construction', type = 'Full-time', location = 'Project Site' WHERE title='Senior Civil Engineer'");

        // --- NEW: Force Exactly 3 for Restaurant Services ---
        $pdo->exec("INSERT IGNORE INTO services (title, description, image_url, category, icon) VALUES 
            ('Catering Service', 'Professional catering for your events, offering a diverse menu from African to European cuisines.', 'https://images.unsplash.com/photo-1555244162-803834f70033?q=80&w=800', 'Catering Service', 'fa-utensils'),
            ('Food Delivery', 'Hot and fresh meals from our kitchen straight to your doorstep within 30 minutes.', 'https://images.unsplash.com/photo-1526367790999-015078648402?q=80&w=800', 'Food Delivery', 'fa-truck'),
            ('Wedding Events', 'Creating magical wedding experiences with exquisite décor, world-class dining, and impeccable service.', 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=800', 'Wedding Events', 'fa-heart')");

        // --- NEW: Force Exactly 3 for Team Members ---
        $pdo->exec("INSERT IGNORE INTO team_members (name, role, image_url) VALUES 
            ('Mequannent Gashaw', 'Executive Chef', 'https://images.unsplash.com/photo-1583394838336-acd977730f90?q=80&w=800'),
            ('Samuel Teketo', 'Operations Manager', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=800'),
            ('Lydia Gashaw', 'Guest Experience Lead', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=800')");

        // --- NEW: Force Exactly 3 for Gallery ---
        $pdo->exec("INSERT IGNORE INTO gallery (title, category, image_url, description) VALUES 
            ('Elegant Dining', 'Ambience', 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=800', 'Experience our premium seating arrangement.'),
            ('Culinary Art', 'Food', 'https://images.unsplash.com/photo-1627308595229-7830a5c91f9f?q=80&w=800', 'Dishes prepared with passion and skill.'),
            ('Modern Ambience', 'Interior', 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=800', 'Contemporary design for a cozy feel.')");

        // --- NEW: Force Exactly 3 for Construction Highlight Features ---
        $pdo->exec("INSERT IGNORE INTO construction_features (title, description, icon_class) VALUES 
            ('Expert Engineering', 'Top-tier structural engineering solutions for any scale.', 'fa-hard-hat'),
            ('Modern Technology', 'Using the latest BIM and drone inspection tools.', 'fa-microchip'),
            ('Sustainable Building', 'Eco-friendly materials and energy-efficient designs.', 'fa-leaf')");
    }

} catch (PDOException $e) {
    die("Database Setup Error: " . $e->getMessage());
}
?>