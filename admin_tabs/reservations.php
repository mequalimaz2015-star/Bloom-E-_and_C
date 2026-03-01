<div class="card">
    <div class="card-header"><span class="card-title">Manage Reservations</span></div>
    <table>
        <tr>
            <th>Customer</th>
            <th>Date/Time</th>
            <th>Guests</th>
            <th>Table</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php
        $res = $pdo->query("SELECT * FROM reservations ORDER BY reservation_date DESC")->fetchAll();
        foreach ($res as $r):
            ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($r['customer_name']) ?></strong><br>
                    <a href="tel:<?= $r['phone'] ?>" style="color: #64748b; text-decoration: none; font-size: 13px;">
                        <i class="fa-solid fa-phone" style="font-size: 11px;"></i> <?= htmlspecialchars($r['phone']) ?>
                    </a>
                </td>
                <td>
                    <?= $r['reservation_date'] ?><br><small>
                        <?= date("g:i A", strtotime($r['reservation_time'])) ?>
                    </small>
                </td>
                <td>
                    <?= $r['guests'] ?>
                </td>
                <td><span class="badge confirmed" style="background:#f1f5f9; color:#475569;">#
                        <?= $r['table_number'] ?: 'N/A' ?>
                    </span></td>
                <td><span class="badge <?= strtolower($r['status']) ?>">
                        <?= $r['status'] ?>
                    </span></td>
                <td>
                    <form method="POST" class="flex-actions">
                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <select name="status" onchange="this.form.submit()" style="width:120px; padding:5px;">
                            <option value="Pending" <?= $r['status'] == 'Pending' ? 'selected' : '' ?>>Pending
                            </option>
                            <option value="Confirmed" <?= $r['status'] == 'Confirmed' ? 'selected' : '' ?>>
                                Confirmed
                            </option>
                            <option value="Rejected" <?= $r['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected
                            </option>
                        </select>
                        <input type="hidden" name="update_reservation" value="1">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>