<?php
$date_filter = $_GET['date'] ?? date('Y-m-d');
$emp_present_today = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE attendance_date=? AND (status='Present' OR status='Late')");
$emp_present_today->execute([$date_filter]);
$present_count = $emp_present_today->fetchColumn();

$emp_late_today = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE attendance_date=? AND status='Late'");
$emp_late_today->execute([$date_filter]);
$late_count = $emp_late_today->fetchColumn();

$total_employees = $pdo->query("SELECT COUNT(*) FROM employees WHERE status='Active'")->fetchColumn();
$absent_count = $total_employees - $present_count;
?>

<div class="stats-grid" style="margin-bottom: 25px;">
    <div class="stat-card" style="border-left: 4px solid #28a745;">
        <div>
            <div class="stat-value"><?= $present_count ?></div>
            <div class="stat-label">Present Today</div>
        </div>
        <div class="stat-icon" style="background:#d4edda; color:#28a745;"><i class="fa-solid fa-user-check"></i></div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #ffc107;">
        <div>
            <div class="stat-value"><?= $late_count ?></div>
            <div class="stat-label">Late Today</div>
        </div>
        <div class="stat-icon" style="background:#fff3cd; color:#ffc107;"><i class="fa-solid fa-clock"></i></div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #dc3545;">
        <div>
            <div class="stat-value"><?= max(0, $absent_count) ?></div>
            <div class="stat-label">Absent Today</div>
        </div>
        <div class="stat-icon" style="background:#f8d7da; color:#dc3545;"><i class="fa-solid fa-user-xmark"></i></div>
    </div>
</div>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; gap: 20px; flex-wrap: wrap;">
    <div style="display: flex; gap: 15px; align-items: flex-end;">
        <div class="card" style="margin-bottom: 0; padding: 15px; flex: 1; min-width: 300px;">
            <div class="card-header" style="margin-bottom: 10px; padding: 0;"><span class="card-title" style="font-size: 14px;">Quick Check-In / Check-Out</span></div>
            <form method="POST" style="display: flex; gap: 10px;">
                <select name="employee_id" required style="flex: 2;">
                    <option value="" disabled selected>Select Employee</option>
                    <?php
                    $staff_list = $pdo->query("SELECT id, name, role FROM employees WHERE status='Active' ORDER BY name ASC")->fetchAll();
                    foreach ($staff_list as $emp):
                    ?>
                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name']) ?> (<?= $emp['role'] ?>)</option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="mark_check_in" class="btn" style="background: #28a745; color: #fff; flex: 1;">IN</button>
                <button type="submit" name="mark_check_out" class="btn" style="background: #007bff; color: #fff; flex: 1;">OUT</button>
            </form>
        </div>
        <form method="GET" style="display: flex; gap: 10px; align-items: flex-end;">
            <input type="hidden" name="tab" value="attendance">
            <div>
                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">Filter by Date</label>
                <input type="date" name="date" value="<?= $date_filter ?>" onchange="this.form.submit()" style="padding: 8px 12px;">
            </div>
        </form>
    </div>
    <button onclick="document.getElementById('manualAttModal').style.display='flex';" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Manual Marking
    </button>
</div>

<!-- Manual Attendance Modal -->
<div class="modal-overlay" id="manualAttModal" style="display: none;">
    <div class="modal-content">
        <div class="card-header">
            <span class="card-title">Manual Attendance Record</span>
            <button type="button" onclick="document.getElementById('manualAttModal').style.display='none';" class="btn" style="background:none; border:none; font-size:20px;">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="add_attendance" value="1">
            <div class="form-row">
                <select name="employee_id" required>
                    <option value="" disabled selected>Select Employee</option>
                    <?php foreach ($staff_list as $emp): ?>
                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="attendance_date" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-row">
                <select name="status">
                    <option value="Present">Present</option>
                    <option value="Late">Late</option>
                    <option value="Absent">Absent</option>
                    <option value="Half Day">Half Day</option>
                </select>
                <input type="number" step="0.1" name="overtime_hours" placeholder="Overtime (Optional)" value="0">
            </div>
            <textarea name="notes" placeholder="Notes (Optional)" rows="3" style="width: 100%; margin-bottom: 15px;"></textarea>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Save Record</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">Attendance Log (<?= date('M d, Y', strtotime($date_filter)) ?>)</span></div>
    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Work Hours</th>
                <th>Overtime</th>
                <th>Late (Min)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->prepare("SELECT a.*, e.name, e.role FROM attendance a JOIN employees e ON a.employee_id = e.id WHERE a.attendance_date=? ORDER BY e.name ASC");
            $stmt->execute([$date_filter]);
            $logs = $stmt->fetchAll();
            
            if (count($logs) > 0):
                foreach ($logs as $log):
                    $badge_color = '#28a745';
                    if ($log['status'] == 'Late') $badge_color = '#ffc107';
                    if ($log['status'] == 'Absent') $badge_color = '#dc3545';
                    if ($log['status'] == 'Half Day') $badge_color = '#17a2b8';
            ?>
                <tr>
                    <td><strong><?= htmlspecialchars($log['name']) ?></strong><br><small><?= $log['role'] ?></small></td>
                    <td><?= $log['check_in'] ? date('H:i', strtotime($log['check_in'])) : '---' ?></td>
                    <td><?= $log['check_out'] ? date('H:i', strtotime($log['check_out'])) : '---' ?></td>
                    <td><?= $log['work_hours'] > 0 ? number_format($log['work_hours'], 2) . 'h' : '---' ?></td>
                    <td><?= $log['overtime_hours'] > 0 ? '<span style="color:#28a745; font-weight:700;">+' . number_format($log['overtime_hours'], 1) . 'h</span>' : '---' ?></td>
                    <td><?= $log['late_minutes'] > 0 ? '<span style="color:#dc3545;">' . $log['late_minutes'] . 'm</span>' : '---' ?></td>
                    <td><span class="badge" style="background:<?= $badge_color ?>; color:#fff;"><?= $log['status'] ?></span></td>
                </tr>
            <?php 
                endforeach;
            else:
            ?>
                <tr><td colspan="7" style="text-align: center; color: #888; padding: 40px;">No attendance records found for this date.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>