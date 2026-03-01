<?php
// Handle additions & deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = "Action completed successfully!";
    function logActivity($pdo, $action)
    {
        $admin = $_SESSION['admin_name'] ?? 'System';
        $stmt = $pdo->prepare("INSERT INTO activity_logs (action, admin_name) VALUES (?, ?)");
        $stmt->execute([$action, $admin]);
    }

    function moveToRecycleBin($pdo, $table, $id, $reason = "Manual Deletion")
    {
        $admin = $_SESSION['admin_name'] ?? 'System';
        // Fetch record first
        $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
        $stmt->execute([$id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
            $json_data = json_encode($record);
            $ins = $pdo->prepare("INSERT INTO recycle_bin (table_name, record_data, deleted_by, deletion_reason) VALUES (?, ?, ?, ?)");
            $ins->execute([$table, $json_data, $admin, $reason]);

            // Now delete from original
            $pdo->prepare("DELETE FROM `$table` WHERE id = ?")->execute([$id]);
            return true;
        }
        return false;
    }

    if (isset($_POST['add_menu'])) {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $image_url = $_POST['image_url'];

        if (isset($_FILES['dish_photo']) && $_FILES['dish_photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['dish_photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $upload_dir = __DIR__ . "/../uploads/menu/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $new_filename = "dish_" . time() . "." . $ext;
                $target = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['dish_photo']['tmp_name'], $target)) {
                    $image_url = "uploads/menu/" . $new_filename;
                }
            }
        }

        $stmt = $pdo->prepare("INSERT INTO menu_items (name, category, description, price, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $description, $price, $image_url]);
        logActivity($pdo, "Added new dish to menu: " . $name);
        $msg = "Food item '$name' successfully added!";
    } elseif (isset($_POST['import_excel'])) {
        // Handle CSV/Excel import
        $imported = 0;
        if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) {
            $file = $_FILES['excel_file']['tmp_name'];
            $filename = $_FILES['excel_file']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if ($ext === 'csv') {
                // Parse CSV file
                $handle = fopen($file, 'r');
                if ($handle) {
                    $row_num = 0;
                    $stmt = $pdo->prepare("INSERT INTO menu_items (name, category, description, price, image_url) VALUES (?, ?, ?, ?, '')");
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $row_num++;
                        // Skip header row (if first row contains 'name' or 'Name' etc.)
                        if ($row_num === 1) {
                            $first_cell = strtolower(trim($row[0] ?? ''));
                            if (in_array($first_cell, ['name', 'item', 'dish', 'food', 'menu'])) {
                                continue; // skip header
                            }
                        }
                        // Expect: name, category, price, description
                        $item_name = trim($row[0] ?? '');
                        $item_category = trim($row[1] ?? 'Main');
                        $item_price = floatval($row[2] ?? 0);
                        $item_desc = trim($row[3] ?? '');

                        if (!empty($item_name) && $item_price > 0) {
                            $stmt->execute([$item_name, $item_category, $item_desc, $item_price]);
                            $imported++;
                        }
                    }
                    fclose($handle);
                }
            }
        }
        logActivity($pdo, "Imported $imported menu items from Excel/CSV");
        $msg = "$imported menu items successfully imported from file!";
    } elseif (isset($_POST['delete_menu'])) {
        $reason = $_POST['deletion_reason'] ?? "Cleaned up from menu";
        moveToRecycleBin($pdo, 'menu_items', $_POST['id'], $reason);
        logActivity($pdo, "Moved menu item ID: " . $_POST['id'] . " to Recycle Bin");
        $msg = "Food item moved to Recycle Bin!";
    } elseif (isset($_POST['update_reservation'])) {
        $pdo->prepare("UPDATE reservations SET status=? WHERE id=?")->execute([$_POST['status'], $_POST['id']]);
        logActivity($pdo, "Updated reservation ID " . $_POST['id'] . " to " . $_POST['status']);
        $msg = "Reservation status updated to " . $_POST['status'];
    } elseif (isset($_POST['register_employee'])) {
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $last_name = $_POST['last_name'];
        $full_name = trim($first_name . " " . $middle_name . " " . $last_name);

        $photo_url = "";
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $upload_dir = __DIR__ . "/../uploads/staff/";
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);
                $new_filename = "emp_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $new_filename)) {
                    $photo_url = "uploads/staff/" . $new_filename;
                }
            }
        }

        try {
            $title = $_POST['title'] ?? '';
            $stmt = $pdo->prepare("INSERT INTO employees (title, name, first_name, middle_name, last_name, role, salary_type, email, phone, address, emergency_contact_name, emergency_contact_phone, date_of_birth, gender, salary, join_date, hire_date, bio, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $title,
                $full_name,
                $first_name,
                $middle_name,
                $last_name,
                $_POST['role'],
                $_POST['salary_type'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['address'],
                $_POST['emergency_name'],
                $_POST['emergency_phone'],
                $_POST['dob'],
                $_POST['gender'],
                $_POST['salary'],
                $_POST['join_date'],
                $_POST['join_date'],
                $_POST['bio'],
                $photo_url
            ]);

            $emp_id = $pdo->lastInsertId();
            $id_number = 'BA-' . str_pad($emp_id, 3, '0', STR_PAD_LEFT);
            $pdo->prepare("UPDATE employees SET id_number = ? WHERE id = ?")->execute([$id_number, $emp_id]);

            logActivity($pdo, "Registered new employee: $full_name ($id_number)");
            $msg = "Employee '$full_name' registered with ID: $id_number";

            echo "<script>window.onload = function() { showIDCard(" . json_encode([
                'id' => $emp_id,
                'id_number' => $id_number,
                'title' => $title,
                'name' => $full_name,
                'role' => $_POST['role'],
                'photo' => $photo_url
            ]) . "); }</script>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "<script>alert('Error: Data overlap detected (duplicate email or ID).'); window.location.href='admin.php?tab=staff';</script>";
                exit;
            } else
                throw $e;
        }
    } elseif (isset($_POST['update_employee_status'])) {
        $pdo->prepare("UPDATE employees SET status=? WHERE id=?")->execute([$_POST['status'], $_POST['id']]);
        logActivity($pdo, "Updated employee ID " . $_POST['id'] . " status to " . $_POST['status']);
        $msg = "Employee status updated to " . $_POST['status'];
    } elseif (isset($_POST['mark_check_in'])) {
        $emp_id = $_POST['employee_id'];
        $date = date('Y-m-d');
        $time = date('Y-m-d H:i:s');

        // Prevent double check-in
        $check = $pdo->prepare("SELECT id FROM attendance WHERE employee_id=? AND attendance_date=?");
        $check->execute([$emp_id, $date]);
        if (!$check->fetch()) {
            // Determine if late (Standard shift: 08:00, Grace: 10 mins)
            $shift_start = strtotime($date . ' 08:00:00');
            $grace_period = 10 * 60; // 10 minutes
            $current_timestamp = strtotime($time);

            $status = 'Present';
            $late_minutes = 0;
            if ($current_timestamp > ($shift_start + $grace_period)) {
                $status = 'Late';
                $late_minutes = round(($current_timestamp - $shift_start) / 60);
            }

            $stmt = $pdo->prepare("INSERT INTO attendance (employee_id, attendance_date, check_in, status, late_minutes) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$emp_id, $date, $time, $status, $late_minutes]);
            logActivity($pdo, "Employee ID $emp_id checked in at " . date('H:i'));
            $msg = "Check-in successful at " . date('H:i');
        } else {
            $msg = "Error: Employee already checked in for today!";
        }
    } elseif (isset($_POST['mark_check_out'])) {
        $emp_id = $_POST['employee_id'];
        $date = date('Y-m-d');
        $time = date('Y-m-d H:i:s');

        $rec = $pdo->prepare("SELECT * FROM attendance WHERE employee_id=? AND attendance_date=?");
        $rec->execute([$emp_id, $date]);
        $row = $rec->fetch();

        if ($row) {
            if (!$row['check_out']) {
                $check_in = strtotime($row['check_in']);
                $check_out = strtotime($time);

                $work_seconds = $check_out - $check_in;
                $work_hours = $work_seconds / 3600;

                $overtime = 0;
                if ($work_hours > 8) {
                    $overtime = $work_hours - 8;
                }

                // Half day logic (less than 4 hours)
                $status = $row['status'];
                if ($work_hours < 4) {
                    $status = 'Half Day';
                }

                $stmt = $pdo->prepare("UPDATE attendance SET check_out=?, work_hours=?, overtime_hours=?, status=? WHERE id=?");
                $stmt->execute([$time, $work_hours, $overtime, $status, $row['id']]);
                logActivity($pdo, "Employee ID $emp_id checked out at " . date('H:i'));
                $msg = "Check-out successful at " . date('H:i');
            } else {
                $msg = "Error: Employee already checked out for today!";
            }
        } else {
            $msg = "Error: This employee has not checked in today!";
        }
    } elseif (isset($_POST['add_attendance'])) {
        // Manual marking
        $emp_id = $_POST['employee_id'];
        $date = $_POST['attendance_date'];

        $check = $pdo->prepare("SELECT id FROM attendance WHERE employee_id=? AND attendance_date=?");
        $check->execute([$emp_id, $date]);
        if (!$check->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO attendance (employee_id, attendance_date, status, notes, overtime_hours) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $emp_id,
                $date,
                $_POST['status'],
                $_POST['notes'] ?? '',
                $_POST['overtime_hours'] ?? 0
            ]);
            logActivity($pdo, "Manually marked attendance for employee ID $emp_id on $date");
        }
    } elseif (isset($_POST['add_salary_advance'])) {
        $stmt = $pdo->prepare("INSERT INTO salary_advances (employee_id, amount, advance_date, reason) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['employee_id'], $_POST['amount'], $_POST['advance_date'], $_POST['reason']]);
        logActivity($pdo, "Recorded salary advance for employee ID " . $_POST['employee_id']);
    } elseif (isset($_POST['add_payroll'])) {
        $emp_id = $_POST['employee_id'];
        $month_str = $_POST['salary_month'];
        list($year, $month) = explode('-', $month_str);

        $emp_stmt = $pdo->prepare("SELECT * FROM employees WHERE id=?");
        $emp_stmt->execute([$emp_id]);
        $emp = $emp_stmt->fetch();

        if (!$emp) {
            echo "<script>alert('Error: Employee not found.'); window.history.back();</script>";
            exit;
        }

        $att_stmt = $pdo->prepare("SELECT status, SUM(overtime_hours) as ot, COUNT(*) as sessions FROM attendance WHERE employee_id=? AND DATE_FORMAT(attendance_date, '%Y-%m') = ? GROUP BY status");
        $att_stmt->execute([$emp_id, $month_str]);
        $attendance_data = $att_stmt->fetchAll(PDO::FETCH_ASSOC);

        $present_days = 0;
        $absent_days = 0;
        $late_count = 0;
        $total_ot_hours = 0;
        foreach ($attendance_data as $row) {
            if ($row['status'] == 'Present')
                $present_days += $row['sessions'];
            if ($row['status'] == 'Absent')
                $absent_days += $row['sessions'];
            if ($row['status'] == 'Late') {
                $present_days += $row['sessions'];
                $late_count += $row['sessions'];
            }
            if ($row['status'] == 'Half Day') {
                $present_days += ($row['sessions'] * 0.5);
                $absent_days += ($row['sessions'] * 0.5);
            }
            $total_ot_hours += $row['ot'];
        }

        $ot_stmt = $pdo->prepare("SELECT SUM(overtime_hours) FROM attendance WHERE employee_id=? AND DATE_FORMAT(attendance_date, '%Y-%m') = ?");
        $ot_stmt->execute([$emp_id, $month_str]);
        $total_ot_hours = $ot_stmt->fetchColumn() ?: 0;

        $base_rate = (float) ($emp['salary'] ?? 0);
        $working_days_standard = 26;
        $daily_rate = $base_rate / $working_days_standard;
        $hourly_rate = $daily_rate / 8;

        $calc_base_salary = $base_rate;
        if ($emp['salary_type'] == 'Daily') {
            $calc_base_salary = $daily_rate * $present_days;
        } elseif ($emp['salary_type'] == 'Hourly') {
            $calc_base_salary = $hourly_rate * ($present_days * 8);
        }

        $overtime_amount = $total_ot_hours * $hourly_rate * 1.5;
        $late_penalty = $late_count * 50;
        $absent_deduction = $absent_days * $daily_rate;

        $adv_stmt = $pdo->prepare("SELECT SUM(amount) FROM salary_advances WHERE employee_id=? AND DATE_FORMAT(advance_date, '%Y-%m') = ?");
        $adv_stmt->execute([$emp_id, $month_str]);
        $advance_deduction = (float) ($adv_stmt->fetchColumn() ?: 0);

        $bonus = (float) ($_POST['bonus'] ?? 0);
        $other_deductions = (float) ($_POST['deductions'] ?? 0);

        $net_salary = ($calc_base_salary + $overtime_amount + $bonus) - ($absent_deduction + $late_penalty + $advance_deduction + $other_deductions);
        $stmt = $pdo->prepare("INSERT INTO payroll (employee_id, salary_month, year, base_salary, bonus, deductions, net_salary, working_days, present_days, absent_days, late_count, total_overtime_hours, overtime_amount, advance_deduction, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $emp_id,
            $month_str,
            $year,
            $calc_base_salary,
            $bonus,
            ($absent_deduction + $late_penalty + $other_deductions),
            $net_salary,
            $working_days_standard,
            $present_days,
            $absent_days,
            $late_count,
            $total_ot_hours,
            $overtime_amount,
            $advance_deduction,
            $_POST['status']
        ]);
        logActivity($pdo, "Generated automated payroll for " . $emp['name'] . " (" . $month_str . ")");
        $msg = "Payroll for " . $emp['name'] . " generated successfully!";
    } elseif (isset($_POST['update_payroll_status'])) {
        $pdo->prepare("UPDATE payroll SET status=? WHERE id=?")->execute([$_POST['status'], $_POST['id']]);
        logActivity($pdo, "Updated payroll ID " . $_POST['id'] . " status to " . $_POST['status']);
        $msg = "Payroll status updated to " . $_POST['status'];
    } elseif (isset($_POST['add_job'])) {
        $stmt = $pdo->prepare("INSERT INTO jobs (title, category, type, location, description, closing_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['title'], $_POST['category'], $_POST['type'], $_POST['location'], $_POST['description'], $_POST['closing_date']]);
        logActivity($pdo, "Posted new job: " . $_POST['title']);
        $msg = "New job listing '" . $_POST['title'] . "' successfully posted!";
    } elseif (isset($_POST['delete_job'])) {
        $reason = $_POST['deletion_reason'] ?? "Position closed / Expired";
        moveToRecycleBin($pdo, 'jobs', $_POST['id'], $reason);
        logActivity($pdo, "Moved job listing ID: " . $_POST['id'] . " to Recycle Bin");
        $msg = "Job listing moved to Recycle Bin!";
    } elseif (isset($_POST['update_job_status'])) {
        $pdo->prepare("UPDATE jobs SET status=? WHERE id=?")->execute([$_POST['status'], $_POST['id']]);
        logActivity($pdo, "Updated job ID " . $_POST['id'] . " status to " . $_POST['status']);
    } elseif (isset($_POST['update_menu'])) {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $image_url = $_POST['image_url'];
        $id = $_POST['id'];
        if (isset($_FILES['dish_photo']) && $_FILES['dish_photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['dish_photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $upload_dir = __DIR__ . "/../uploads/menu/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $new_filename = "dish_" . time() . "." . $ext;
                $target = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['dish_photo']['tmp_name'], $target)) {
                    $image_url = "uploads/menu/" . $new_filename;
                }
            }
        }
        $stmt = $pdo->prepare("UPDATE menu_items SET name=?, category=?, description=?, price=?, image_url=? WHERE id=?");
        $stmt->execute([$name, $category, $description, $price, $image_url, $id]);
        logActivity($pdo, "Updated menu item: " . $name);
        $msg = "Menu item '$name' successfully updated!";
    } elseif (isset($_POST['update_profile'])) {
        $name = $_POST['full_name'];
        $email = $_POST['email'];
        $user_id = $_SESSION['admin_id'];

        $profile_pic = $_SESSION['admin_pic'] ?? '';
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['profile_pic']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $upload_dir = __DIR__ . "/../uploads/admin/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $new_filename = "admin_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_dir . $new_filename)) {
                    $profile_pic = "uploads/admin/" . $new_filename;
                }
            }
        }

        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, profile_pic = ? WHERE id = ?");
        $stmt->execute([$name, $email, $profile_pic, $user_id]);
        $_SESSION['admin_name'] = $name;
        $_SESSION['admin_email'] = $email;
        $_SESSION['admin_pic'] = $profile_pic;
        logActivity($pdo, "Updated admin profile details & picture");
        $msg = "Profile updated successfully!";
    } elseif (isset($_POST['change_password'])) {
        $old_pass = $_POST['old_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];
        $user_id = $_SESSION['admin_id'];

        $user = $pdo->query("SELECT password FROM users WHERE id = $user_id")->fetch();

        if (password_verify($old_pass, $user['password'])) {
            if ($new_pass === $confirm_pass) {
                $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hashed, $user_id]);
                logActivity($pdo, "Changed admin password");
                $msg = "Password changed successfully!";
            } else {
                $error_msg = "Passwords do not match.";
                header("Location: admin.php?tab=profile&err=" . urlencode($error_msg));
                exit;
            }
        } else {
            $error_msg = "Incorrect old password.";
            header("Location: admin.php?tab=profile&err=" . urlencode($error_msg));
            exit;
        }
    } elseif (isset($_POST['update_employee'])) {
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $last_name = $_POST['last_name'];
        $full_name = trim($first_name . " " . $middle_name . " " . $last_name);
        $id = $_POST['id'];

        $update_photo = "";
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $upload_dir = __DIR__ . "/../uploads/staff/";
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);
                $new_filename = "emp_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $new_filename)) {
                    $update_photo = ", photo='uploads/staff/$new_filename'";
                }
            }
        }
        $stmt = $pdo->prepare("UPDATE employees SET title=?, name=?, first_name=?, middle_name=?, last_name=?, role=?, email=?, phone=?, salary=?, salary_type=?, join_date=?, date_of_birth=?, gender=?, address=?, emergency_contact_name=?, emergency_contact_phone=?, bio=? $update_photo WHERE id=?");
        $stmt->execute([
            $_POST['title'] ?? '',
            $full_name,
            $first_name,
            $middle_name,
            $last_name,
            $_POST['role'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['salary'],
            $_POST['salary_type'],
            $_POST['join_date'],
            $_POST['dob'],
            $_POST['gender'],
            $_POST['address'],
            $_POST['emergency_name'],
            $_POST['emergency_phone'],
            $_POST['bio'] ?? '',
            $id
        ]);

        logActivity($pdo, "Updated detailed employee profile for: $full_name");
        $msg = "Employee profile for $full_name successfully updated!";
    } elseif (isset($_POST['delete_employee'])) {
        $reason = $_POST['deletion_reason'] ?? "Resigned / Terminated";
        moveToRecycleBin($pdo, 'employees', $_POST['id'], $reason);
        logActivity($pdo, "Moved employee ID: " . $_POST['id'] . " to Recycle Bin");
        $msg = "Employee record moved to Recycle Bin!";
    } elseif (isset($_POST['update_job'])) {
        $stmt = $pdo->prepare("UPDATE jobs SET title=?, category=?, type=?, location=?, description=?, closing_date=? WHERE id=?");
        $stmt->execute([$_POST['title'], $_POST['category'], $_POST['type'], $_POST['location'], $_POST['description'], $_POST['closing_date'], $_POST['id']]);
        logActivity($pdo, "Updated job listing: " . $_POST['title']);
    } elseif (isset($_POST['update_company'])) {
        $stmt = $pdo->prepare("UPDATE company_info SET company_name=?, email=?, phone=?, address=?, about_text=?, facebook=?, instagram=?, twitter=?, tiktok=?, linkedin=?, telegram=?, whatsapp=?, ceo_name=?, ceo_title=?, ceo_message=?, ceo_image=? WHERE id=1");
        $stmt->execute([$_POST['company_name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['about_text'], $_POST['facebook'], $_POST['instagram'], $_POST['twitter'], $_POST['tiktok'], $_POST['linkedin'], $_POST['telegram'], $_POST['whatsapp'], $_POST['ceo_name'], $_POST['ceo_title'], $_POST['ceo_message'], $_POST['ceo_image']]);
        logActivity($pdo, "Updated company and CEO information");
        $msg = "Company information updated successfully!";
    } elseif (isset($_POST['update_application_status'])) {
        $status = $_POST['status'];
        $id = $_POST['id'];
        $pdo->prepare("UPDATE job_applications SET status=? WHERE id=?")->execute([$status, $id]);

        if ($status === 'Accepted') {
            // Check if already in staff to avoid duplicates
            $check = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE email = (SELECT email FROM job_applications WHERE id = ?)");
            $check->execute([$id]);
            if ($check->fetchColumn() == 0) {
                // Insert into employees
                $stmt = $pdo->prepare("INSERT INTO employees (name, role, email, phone, photo, bio, join_date, hire_date, status) 
                    SELECT a.applicant_name, j.title, a.email, a.phone, a.photo_url, a.cover_letter, CURDATE(), CURDATE(), 'Active' 
                    FROM job_applications a 
                    JOIN jobs j ON a.job_id = j.id 
                    WHERE a.id = ?");
                $stmt->execute([$id]);

                $emp_id = $pdo->lastInsertId();
                $id_number = 'BA-' . str_pad($emp_id, 3, '0', STR_PAD_LEFT);
                $pdo->prepare("UPDATE employees SET id_number = ? WHERE id = ?")->execute([$id_number, $emp_id]);

                logActivity($pdo, "Auto-added accepted applicant ID " . $id . " to staff directory ($id_number)");
            }
        }

        logActivity($pdo, "Updated application ID " . $id . " to " . $status);
    } elseif (isset($_POST['delete_application'])) {
        $reason = $_POST['deletion_reason'] ?? "Rejected after review";
        moveToRecycleBin($pdo, 'job_applications', $_POST['id'], $reason);
        logActivity($pdo, "Moved job application ID: " . $_POST['id'] . " to Recycle Bin");
        $msg = "Application moved to Recycle Bin!";
    } elseif (isset($_POST['bulk_status'])) {
        $ids = explode(',', $_POST['bulk_ids']);
        $status = $_POST['bulk_status'];
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("UPDATE job_applications SET status=? WHERE id IN ($placeholders)");
        $stmt->execute(array_merge([$status], $ids));

        if ($status === 'Accepted') {
            foreach ($ids as $id) {
                $check = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE email = (SELECT email FROM job_applications WHERE id = ?)");
                $check->execute([$id]);
                if ($check->fetchColumn() == 0) {
                    $stmt = $pdo->prepare("INSERT INTO employees (name, role, email, phone, photo, bio, join_date, hire_date, status) 
                        SELECT a.applicant_name, j.title, a.email, a.phone, a.photo_url, a.cover_letter, CURDATE(), CURDATE(), 'Active' 
                        FROM job_applications a 
                        JOIN jobs j ON a.job_id = j.id 
                        WHERE a.id = ?");
                    $stmt->execute([$id]);

                    $emp_id = $pdo->lastInsertId();
                    $id_number = 'BA-' . str_pad($emp_id, 3, '0', STR_PAD_LEFT);
                    $pdo->prepare("UPDATE employees SET id_number = ? WHERE id = ?")->execute([$id_number, $emp_id]);
                }
            }
            logActivity($pdo, "Auto-added bulk accepted applicants to staff directory");
        }

        logActivity($pdo, "Bulk updated " . count($ids) . " applications to $status");
        $msg = count($ids) . " applications updated to $status!";
    } elseif (isset($_POST['bulk_delete'])) {
        $ids = explode(',', $_POST['bulk_ids']);
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM job_applications WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        logActivity($pdo, "Bulk deleted " . count($ids) . " job applications");
        $msg = count($ids) . " applications permanently removed!";
    } elseif (isset($_POST['add_gallery'])) {
        $category = $_POST['category'];
        $title = $_POST['title'];
        $image_url = "";

        if (isset($_FILES['gallery_photo']) && $_FILES['gallery_photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['gallery_photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $upload_dir = __DIR__ . "/../uploads/gallery/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $new_filename = "gallery_" . time() . "." . $ext;
                $target = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['gallery_photo']['tmp_name'], $target)) {
                    $image_url = "uploads/gallery/" . $new_filename;
                }
            }
        }

        $description = $_POST['description'] ?? '';
        if ($image_url) {
            $stmt = $pdo->prepare("INSERT INTO gallery (image_url, category, title, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$image_url, $category, $title, $description]);
            logActivity($pdo, "Added new image to gallery: " . ($title ?: $category));
        }
    } elseif (isset($_POST['delete_gallery'])) {
        $reason = $_POST['deletion_reason'] ?? "Cleaned up gallery";
        moveToRecycleBin($pdo, 'gallery', $_POST['id'], $reason);
        logActivity($pdo, "Moved gallery image ID: " . $_POST['id'] . " to Recycle Bin");
        $msg = "Gallery image moved to Recycle Bin!";
    } elseif (isset($_POST['add_service'])) {
        $title = $_POST['title'];
        $icon = $_POST['icon'] ?? 'fa-concierge-bell';
        $video_url = $_POST['video_url'] ?? '';
        $description = $_POST['description'] ?? '';
        $image_url = "";

        if (isset($_FILES['service_photo']) && $_FILES['service_photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['service_photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $upload_dir = __DIR__ . "/../uploads/services/";
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);
                $new_filename = "service_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES['service_photo']['tmp_name'], $upload_dir . $new_filename)) {
                    $image_url = "uploads/services/" . $new_filename;
                }
            }
        }

        $category = $_POST['category'] ?? 'Others';

        $stmt = $pdo->prepare("INSERT INTO services (title, icon, video_url, description, image_url, category) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $icon, $video_url, $description, $image_url, $category]);
        logActivity($pdo, "Added new service: " . $title);
        $msg = "Service '$title' added successfully!";
    } elseif (isset($_POST['delete_service'])) {
        $reason = $_POST['deletion_reason'] ?? "Service discontinued";
        moveToRecycleBin($pdo, 'services', $_POST['id'], $reason);
        logActivity($pdo, "Moved service ID: " . $_POST['id'] . " to Recycle Bin");
        $msg = "Service moved to Recycle Bin!";
    } elseif (isset($_POST['update_service'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $icon = $_POST['icon'] ?? 'fa-concierge-bell';
        $category = $_POST['category'] ?? 'Others';
        $status = $_POST['status'] ?? 'Active';
        $video_url = $_POST['video_url'] ?? '';
        $description = $_POST['description'] ?? '';
        $image_url = $_POST['existing_image'] ?? ''; // keep current by default

        // Replace image only if a new file was uploaded
        if (isset($_FILES['service_photo']) && $_FILES['service_photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['service_photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $upload_dir = __DIR__ . "/../uploads/services/";
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);
                $new_filename = "service_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES['service_photo']['tmp_name'], $upload_dir . $new_filename)) {
                    $image_url = "uploads/services/" . $new_filename;
                }
            }
        }

        $stmt = $pdo->prepare("UPDATE services SET title=?, icon=?, category=?, status=?, video_url=?, description=?, image_url=? WHERE id=?");
        $stmt->execute([$title, $icon, $category, $status, $video_url, $description, $image_url, $id]);
        logActivity($pdo, "Updated service: " . $title);
        $msg = "Service '$title' updated successfully!";
    } elseif (isset($_POST['send_chat_reply'])) {
        $sid = $_POST['session_id'];
        $reply = $_POST['reply'];
        $stmt = $pdo->prepare("INSERT INTO chat_messages (session_id, sender, message) VALUES (?, 'Admin', ?)");
        $stmt->execute([$sid, $reply]);
        $msg = "Reply sent to customer!";
    } elseif (isset($_POST['restore_item'])) {
        $trash_id = $_POST['trash_id'];
        $trash = $pdo->query("SELECT * FROM recycle_bin WHERE id = $trash_id")->fetch(PDO::FETCH_ASSOC);
        if ($trash) {
            $table = $trash['table_name'];
            $record = json_decode($trash['record_data'], true);

            $cols = array_keys($record);
            $cols_str = "`" . implode("`,`", $cols) . "`";
            $placeholders = str_repeat('?,', count($cols) - 1) . '?';

            $stmt = $pdo->prepare("INSERT INTO `$table` ($cols_str) VALUES ($placeholders)");
            $stmt->execute(array_values($record));

            $pdo->prepare("DELETE FROM recycle_bin WHERE id = ?")->execute([$trash_id]);
            logActivity($pdo, "Restored archived item from " . $table);
            $msg = "Data successfully restored to " . $table . "!";
        }
    } elseif (isset($_POST['purge_item'])) {
        $trash_id = $_POST['trash_id'];
        $pdo->prepare("DELETE FROM recycle_bin WHERE id = ?")->execute([$trash_id]);
        logActivity($pdo, "Permanently purged item from Recycle Bin");
        $msg = "Item permanently removed!";
    }

    header("Location: admin.php?tab=" . ($_GET['tab'] ?? 'dashboard') . "&msg=" . urlencode($msg));
    exit;
}
?>