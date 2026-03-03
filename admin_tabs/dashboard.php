<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-value">
                <?= $pending_res_count ?>
            </div>
            <div class="stat-label">Pending Res.</div>
        </div>
        <div class="stat-icon" style="background:#e0e8ff; color:#4361ee;"><i class="fa-solid fa-calendar"></i>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">
                <?= $order_count ?>
            </div>
            <div class="stat-label">New Orders</div>
        </div>
        <div class="stat-icon" style="background:#d4edda; color:#155724;"><i class="fa-solid fa-bell"></i></div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">
                <?= $menu_count ?>
            </div>
            <div class="stat-label">Menu Items</div>
        </div>
        <div class="stat-icon" style="background:#fff3cd; color:#856404;"><i class="fa-solid fa-burger"></i>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">
                <?= $emp_present_today ?>
            </div>
            <div class="stat-label">Present Today</div>
        </div>
        <div class="stat-icon" style="background:#d4edda; color:#155724;"><i class="fa-solid fa-user-check"></i>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">
                <?= $pending_salary_count ?>
            </div>
            <div class="stat-label">Pending Salaries</div>
        </div>
        <div class="stat-icon" style="background:#f8d7da; color:#721c24;"><i
                class="fa-solid fa-hand-holding-dollar"></i>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">
                <?= $fav_count ?>
            </div>
            <div class="stat-label">Total Favorites</div>
        </div>
        <div class="stat-icon" style="background:#fce4ec; color:#d81b60;"><i class="fa-solid fa-heart"></i>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <span class="card-title">Daily Performance (%)</span>
        </div>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <div class="card-header" style="background: transparent;">
            <span class="card-title">Services & Menu Distribution</span>
        </div>
        <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center;">
            <canvas id="servicesPieChart"></canvas>
        </div>
    </div>
</div>

<div
    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-bottom: 30px;">
    <div class="card" style="margin-bottom: 0; background: #fafafa; border: 1px dashed var(--accent);">
        <div class="card-header" style="background: transparent;">
            <span class="card-title" style="color: var(--blue);"><i class="fa-solid fa-comments"></i> Recent Chat
                Orders</span>
            <a href="?tab=communication"
                style="font-size: 11px; text-decoration: none; color: var(--accent); font-weight: 700;">View Hub</a>
        </div>
        <table style="font-size: 13px;">
            <tr>
                <th>Item</th>
                <th>Platform</th>
                <th>Time</th>
            </tr>
            <?php
            $chat_orders = $pdo->query("SELECT * FROM orders WHERE platform IN ('WhatsApp', 'Telegram') ORDER BY created_at DESC LIMIT 5")->fetchAll();
            foreach ($chat_orders as $co): ?>
                <tr>
                    <td><strong style="color: #333;"><?= htmlspecialchars($co['order_details']) ?></strong></td>
                    <td>
                        <span
                            style="font-size: 11px; font-weight: 600; color: <?= $co['platform'] == 'WhatsApp' ? '#25d366' : '#0088cc' ?>;">
                            <i class="fa-brands fa-<?= strtolower($co['platform']) ?>"></i> <?= $co['platform'] ?>
                        </span>
                    </td>
                    <td style="color: #888; font-size: 11px;"><?= date('H:i', strtotime($co['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($chat_orders)): ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: #999; padding: 20px;">No chat orders yet.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <span class="card-title">Recent Reservations</span>
        </div>
        <table>
            <tr>
                <th>Customer Name</th>
                <th>Date & Time</th>
                <th>Guests</th>
                <th>Status</th>
            </tr>
            <?php
            $recent_res = $pdo->query("SELECT * FROM reservations ORDER BY created_at DESC LIMIT 5")->fetchAll();
            foreach ($recent_res as $r):
                ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($r['customer_name']) ?> <br><small>
                            <?= htmlspecialchars($r['phone']) ?>
                        </small>
                    </td>
                    <td>
                        <?= $r['reservation_date'] ?> at
                        <?= $r['reservation_time'] ?>
                    </td>
                    <td>
                        <?= $r['guests'] ?>
                    </td>
                    <td><span class="badge <?= strtolower($r['status']) ?>">
                            <?= $r['status'] ?>
                        </span></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="card" style="margin-bottom: 0; background: #fffafb; border: 1px solid #f8bbd0;">
        <div class="card-header" style="background: transparent;">
            <span class="card-title" style="color: #d81b60;"><i class="fa-solid fa-heart"></i> Recent Favorites</span>
            <span style="font-size: 11px; color: #888;">Customer Interests</span>
        </div>
        <table style="font-size: 13px;">
            <tr>
                <th>Customer Email</th>
                <th>Dish Name</th>
                <th>Time</th>
            </tr>
            <?php foreach ($recent_favorites as $fav): ?>
                <tr>
                    <td><strong style="color: #333;"><?= htmlspecialchars($fav['customer_email']) ?></strong></td>
                    <td>
                        <span class="badge" style="background: rgba(216, 27, 96, 0.1); color: #d81b60; border: none;">
                            <?= htmlspecialchars($fav['dish_name']) ?>
                        </span>
                    </td>
                    <td style="color: #888; font-size: 11px;"><?= date('M d, H:i', strtotime($fav['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($recent_favorites)): ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: #999; padding: 20px;">No favorites yet.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>


<div class="card" style="margin-bottom: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #e1e5ee;">
    <div class="card-header"
        style="background-color: #e6ebf2; border-bottom: 1px solid #d0d7e5; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
        <span class="card-title"
            style="color: #2a4158; font-size: 15px; font-weight: 600; text-transform: capitalize;">System
            Activity Log</span>
        <i class="fa-solid fa-chevron-down" style="color: #2a4158; cursor: pointer;"></i>
    </div>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="background-color: #ffffff; border-bottom: 1px solid #f0f0f0;">
                <th
                    style="padding: 12px 20px; font-size: 12px; font-weight: 600; color: #1f3041; text-align: left; text-transform:none;">
                    TimeStamp</th>
                <th
                    style="padding: 12px 20px; font-size: 12px; font-weight: 600; color: #1f3041; text-align: left; text-transform:none;">
                    Action Performed</th>
            </tr>
            <?php
            $recent_logs = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 15")->fetchAll();
            if (count($recent_logs) > 0) {
                foreach ($recent_logs as $log):
                    ?>
                    <tr style="background-color: #fbfbfd; border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 12px 20px; color: #435b71; font-size: 13px; width: 220px;">
                            <?= $log['created_at'] ?>
                        </td>
                        <td style="padding: 12px 20px; color: #1f3041; font-size: 13px;">
                            <?= htmlspecialchars($log['action']) ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
            } else {
                echo "<tr><td colspan='2' style='text-align: center; color: #888; padding: 25px; font-size: 14px;'>No recent activities logged inside the system.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>