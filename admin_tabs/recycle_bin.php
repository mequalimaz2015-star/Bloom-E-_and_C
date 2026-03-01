<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-size: 28px; font-weight: 800; color: #1e293b;">Recycle Bin</h2>
    <div
        style="background: #f1f5f9; padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; color: #64748b;">
        <i class="fa-solid fa-trash-can" style="margin-right: 8px; color: #ef4444;"></i>
        Stored Deleted Items
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Recently Deleted Items</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Item Details</th>
                <th>Deleted By</th>
                <th>Reason</th>
                <th>Deleted At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $trash = $pdo->query("SELECT * FROM recycle_bin ORDER BY deleted_at DESC")->fetchAll();
            if (empty($trash)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                        <i class="fa-solid fa-cloud-sun"
                            style="font-size: 40px; display: block; margin-bottom: 15px; opacity: 0.3;"></i>
                        No items in recycle bin. Your trash is empty!
                    </td>
                </tr>
            <?php else:
                foreach ($trash as $item):
                    $data = json_decode($item['record_data'], true);
                    $name = $data['name'] ?? $data['applicant_name'] ?? $data['title'] ?? $data['id'] ?? 'Unknown';
                    $type = ucfirst(str_replace('_', ' ', str_replace('menu_items', 'Menu Dish', str_replace('job_applications', 'Application', $item['table_name']))));
                    ?>
                    <tr>
                        <td>
                            <span class="badge" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">
                                <?= $type ?>
                            </span>
                        </td>
                        <td>
                            <strong style="color: #1e293b;">
                                <?= htmlspecialchars($name) ?>
                            </strong><br>
                            <small style="color: #64748b;">Original ID: #
                                <?= $data['id'] ?? 'N/A' ?>
                            </small>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #2563eb;">
                                <?= htmlspecialchars($item['deleted_by']) ?>
                            </span>
                        </td>
                        <td>
                            <div style="max-width: 200px; font-size: 13px; font-style: italic; color: #64748b;">
                                "
                                <?= htmlspecialchars($item['deletion_reason']) ?>"
                            </div>
                        </td>
                        <td>
                            <small style="color: #94a3b8; font-weight: 500;">
                                <?= date('M d, Y', strtotime($item['deleted_at'])) ?><br>
                                <?= date('H:i', strtotime($item['deleted_at'])) ?>
                            </small>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="trash_id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="restore_item" class="btn"
                                        style="background: #10b981; color: #fff; padding: 6px 12px; border-radius: 6px; font-size: 12px; border: none; font-weight: 600; cursor: pointer;">
                                        <i class="fa-solid fa-rotate-left"></i> Restore
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;"
                                    onsubmit="return confirm('WARNING: This will permanently delete the data. This action cannot be undone. Proceed?');">
                                    <input type="hidden" name="trash_id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="purge_item" class="btn"
                                        style="background: #ef4444; color: #fff; padding: 6px 12px; border-radius: 6px; font-size: 12px; border: none; font-weight: 600; cursor: pointer;">
                                        <i class="fa-solid fa-fire"></i> Purge
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<style>
    .badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
</style>