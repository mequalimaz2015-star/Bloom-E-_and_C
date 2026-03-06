<div class="card">
    <div class="card-header"
        style="display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
        <span class="card-title">System Activity Log</span>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <!-- Bulk Actions (shown when items are selected) -->
            <div id="activity_bulk_actions" style="display: none; align-items: center; gap: 10px;">
                <span id="activity_selected_count" style="font-size: 13px; font-weight: 700; color: #2563eb;">0
                    selected</span>
                <form method="POST" id="activity_bulk_form" style="display: flex; gap: 5px;">
                    <input type="hidden" name="activity_bulk_ids" id="activity_bulk_ids_input">
                    <button type="submit" name="bulk_delete_activities" class="btn"
                        onclick="return confirm('Are you sure you want to delete the selected activity logs?')"
                        style="background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; padding: 7px 16px; font-size: 12px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(239,68,68,0.3); transition: all 0.2s;">
                        <i class="fa-solid fa-trash-can"></i> Delete Selected
                    </button>
                </form>
            </div>

            <!-- Clear All Button -->
            <form method="POST"
                onsubmit="return confirm('⚠️ WARNING: This will permanently delete ALL activity logs. This action cannot be undone. Continue?');"
                style="margin: 0;">
                <button type="submit" name="clear_all_activities" class="btn"
                    style="background: linear-gradient(135deg, #64748b, #475569); color: #fff; padding: 7px 16px; font-size: 12px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(100,116,139,0.3); transition: all 0.2s;">
                    <i class="fa-solid fa-broom"></i> Clear All Logs
                </button>
            </form>
        </div>
    </div>
    <table>
        <tr>
            <th style="width: 40px; text-align: center;">
                <input type="checkbox" id="select_all_activities" onchange="toggleSelectAllActivities(this)"
                    style="width: 16px; height: 16px; cursor: pointer; accent-color: #2563eb;">
            </th>
            <th>Date & Time</th>
            <th>Action Performed</th>
            <th>Admin User</th>
            <th style="width: 70px; text-align: center;">Remove</th>
        </tr>
        <?php
        // Fetch logs securely
        $logs = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 200")->fetchAll();
        if (count($logs) > 0) {
            foreach ($logs as $log):
                ?>
                <tr id="activity-row-<?= $log['id'] ?>">
                    <td style="text-align: center;">
                        <input type="checkbox" class="activity-checkbox" value="<?= $log['id'] ?>"
                            onchange="updateActivityBulkUI()"
                            style="width: 16px; height: 16px; cursor: pointer; accent-color: #2563eb;">
                    </td>
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
                    <td style="text-align: center;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                            <button type="button" class="btn-icon btn-delete"
                                onclick="modernDelete('delete_single_activity', '<?= $log['id'] ?>', '<?= htmlspecialchars(substr($log['action'], 0, 40), ENT_QUOTES) ?>', 'Activity Log')"
                                title="Remove Log Entry"
                                style="background: #fee2e2; color: #ef4444; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <span
                                style="font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase;">Delete</span>
                        </div>
                    </td>
                </tr>
                <?php
            endforeach;
        } else {
            echo "<tr><td colspan='5' style='text-align: center; color: #888; padding: 40px 20px;'>
                    <i class='fa-solid fa-clipboard-check' style='font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 10px;'></i>
                    No recent activities logged.
                  </td></tr>";
        }
        ?>
    </table>
    <?php if (count($logs) > 0): ?>
        <div
            style="padding: 12px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; border-radius: 0 0 12px 12px;">
            <span style="font-size: 12px; color: #94a3b8; font-weight: 600;">
                <i class="fa-solid fa-list" style="margin-right: 4px;"></i>
                Showing <?= count($logs) ?> log entries
            </span>
            <span style="font-size: 11px; color: #cbd5e1;">
                Select items using checkboxes to perform bulk actions
            </span>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleSelectAllActivities(source) {
        const checkboxes = document.querySelectorAll('.activity-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
        updateActivityBulkUI();
    }

    function updateActivityBulkUI() {
        const checkboxes = document.querySelectorAll('.activity-checkbox:checked');
        const allCheckboxes = document.querySelectorAll('.activity-checkbox');
        const bulkDiv = document.getElementById('activity_bulk_actions');
        const selectedCount = document.getElementById('activity_selected_count');
        const bulkIdsInput = document.getElementById('activity_bulk_ids_input');
        const selectAllBox = document.getElementById('select_all_activities');

        if (checkboxes.length > 0) {
            bulkDiv.style.display = 'flex';
            selectedCount.innerText = checkboxes.length + ' selected';
            const ids = Array.from(checkboxes).map(cb => cb.value);
            bulkIdsInput.value = ids.join(',');
        } else {
            bulkDiv.style.display = 'none';
        }

        // Update "select all" checkbox state
        if (allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length) {
            selectAllBox.checked = true;
            selectAllBox.indeterminate = false;
        } else if (checkboxes.length > 0) {
            selectAllBox.checked = false;
            selectAllBox.indeterminate = true;
        } else {
            selectAllBox.checked = false;
            selectAllBox.indeterminate = false;
        }
    }
</script>