<div class="card">
    <div class="card-header"><span class="card-title">System Activity Log</span></div>
    <table>
        <tr>
            <th>Date & Time</th>
            <th>Action Performed</th>
            <th>Admin User</th>
        </tr>
        <?php
        // Fetch logs securely
        $logs = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 50")->fetchAll();
        if (count($logs) > 0) {
            foreach ($logs as $log):
                ?>
                <tr>
                    <td style="color: #125b9bff;"><i class="fa-regular fa-clock" style="margin-right: 5px;"></i>
                        <?= $log['created_at'] ?>
                    </td>
                    <td><strong style="color: var(--text); font-weight: 500;">
                            <?= htmlspecialchars($log['action']) ?>
                        </strong>
                    </td>
                    <td>
                        <span style="font-size: 13px; color: #64748b; font-weight: 600;">
                            <i class="fa-solid fa-user-shield" style="margin-right: 5px; color: #0054a6;"></i>
                            <?= htmlspecialchars($log['admin_name'] ?? 'System') ?>
                        </span>
                    </td>
                </tr>
                <?php
            endforeach;
        } else {
            echo "<tr><td colspan='3' style='text-align: center; color: #888;'>No recent activities logged.</td></tr>";
        }
        ?>
    </table>
</div>