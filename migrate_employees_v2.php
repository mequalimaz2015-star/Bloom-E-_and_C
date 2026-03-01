<?php
require_once 'db.php';
try {
    $columns = [
        "id_number VARCHAR(50) UNIQUE AFTER id",
        "first_name VARCHAR(100) AFTER name",
        "middle_name VARCHAR(100) AFTER first_name",
        "last_name VARCHAR(100) AFTER middle_name",
        "salary_type ENUM('Monthly', 'Daily', 'Hourly') DEFAULT 'Monthly' AFTER role",
        "address TEXT AFTER phone",
        "emergency_contact_name VARCHAR(255) AFTER address",
        "emergency_contact_phone VARCHAR(50) AFTER emergency_contact_name",
        "date_of_birth DATE AFTER emergency_contact_phone",
        "gender ENUM('Male', 'Female', 'Other') AFTER date_of_birth",
        "hire_date DATE AFTER join_date",
        "bio TEXT AFTER status",
        "photo VARCHAR(255) AFTER bio"
    ];

    foreach ($columns as $column) {
        $col_name = explode(" ", $column)[0];
        $check = $pdo->query("SHOW COLUMNS FROM employees LIKE '$col_name'");
        if ($check->rowCount() == 0) {
            $pdo->exec("ALTER TABLE employees ADD COLUMN $column");
            echo "Added column: $col_name\n";
        }
    }
    echo "Employee table migration completed successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>