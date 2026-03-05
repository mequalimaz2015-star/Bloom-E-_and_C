<?php
$equipment = $pdo->query("SELECT * FROM construction_equipment ORDER BY created_at DESC")->fetchAll();
?>

<div class="card" style="border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: none; overflow: hidden;">
    <div class="card-header"
        style="background: #fff; border-bottom: 1px solid #f1f5f9; padding: 25px; display: flex; justify-content: space-between; align-items: center;">
        <span class="card-title"
            style="margin: 0; display: flex; align-items: center; gap: 12px; font-weight: 700; color: #1e293b;">
            <i class="fa-solid fa-truck-pickup" style="color: #6366f1;"></i> Equipment Inventory
        </span>
        <button class="btn btn-primary btn-sm" onclick="showEquipModal('addEquipModal')"
            style="border-radius: 8px; padding: 8px 16px; font-weight: 600; display: flex; align-items: center; gap: 8px; background: #6366f1; border: none;">
            <i class="fa-solid fa-plus"></i> Add Equipment
        </button>
    </div>
    <div style="padding: 25px;">
        <div style="overflow-x: auto;">
            <table class="data-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: left; font-weight: 600; color: #64748b; font-size: 13px;">
                            Equipment Info</th>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: left; font-weight: 600; color: #64748b; font-size: 13px;">
                            Serial / ID</th>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: left; font-weight: 600; color: #64748b; font-size: 13px;">
                            Status</th>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: right; font-weight: 600; color: #64748b; font-size: 13px;">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($equipment)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                                    <i class="fa-solid fa-tools" style="font-size: 40px; color: #e2e8f0;"></i>
                                    <span>No equipment registered yet.</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($equipment as $equip): ?>
                            <tr>
                                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9;">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div
                                            style="width: 50px; height: 50px; border-radius: 10px; overflow: hidden; background: #f1f5f9; border: 1px solid #e2e8f0;">
                                            <img src="<?= htmlspecialchars($equip['image_url'] ?: 'https://images.unsplash.com/photo-1579349454199-4c12517e467d?q=80&w=200') ?>"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #1e293b;">
                                                <?= htmlspecialchars($equip['name']) ?>
                                            </div>
                                            <div style="font-size: 11px; color: #64748b;">
                                                <?= htmlspecialchars(substr($equip['description'] ?? '', 0, 40)) ?>...
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-weight: 600;">
                                    <?= htmlspecialchars($equip['serial_number'] ?: 'N/A') ?>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9;">
                                    <?php
                                    $equip_colors = [
                                        'Available' => ['bg' => '#dcfce7', 'text' => '#15803d'],
                                        'In Use' => ['bg' => '#fef9c3', 'text' => '#a16207'],
                                        'Maintenance' => ['bg' => '#fee2e2', 'text' => '#b91c1c'],
                                        'Retired' => ['bg' => '#f1f5f9', 'text' => '#475569']
                                    ];
                                    $ecol = $equip_colors[$equip['status']] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                                    ?>
                                    <span
                                        style="background: <?= $ecol['bg'] ?>; color: <?= $ecol['text'] ?>; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                        <?= $equip['status'] ?>
                                    </span>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <button class="btn-icon"
                                            onclick="editEquip(<?= htmlspecialchars(json_encode($equip)) ?>)"
                                            style="background: #f1f5f9; color: #475569; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i
                                                class="fa-solid fa-pen-to-square"></i></button>
                                        <form method="POST" onsubmit="return confirm('Remove this equipment from inventory?')">
                                            <input type="hidden" name="id" value="<?= $equip['id'] ?>">
                                            <button type="submit" name="delete_const_equipment" class="btn-icon"
                                                style="background: #fee2e2; color: #ef4444; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i
                                                    class="fa-solid fa-trash-can"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="addEquipModal" class="modal"
    style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div style="background:#fff; width:90%; max-width:500px; margin: 60px auto; border-radius:24px; overflow:hidden;">
        <div
            style="background: #6366f1; padding: 25px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="equipModalTitle" style="margin:0;">Add Equipment</h3>
            <span onclick="closeEquipModal()" style="cursor:pointer; font-size:24px;">&times;</span>
        </div>
        <form method="POST" enctype="multipart/form-data" style="padding:30px;">
            <input type="hidden" name="save_const_equipment" value="1">
            <input type="hidden" name="id" id="equipId">
            <input type="hidden" name="existing_image" id="equipExistingImage">

            <div style="margin-bottom: 20px;">
                <label
                    style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px;">EQUIPMENT
                    NAME</label>
                <input type="text" name="name" id="equipName" required
                    style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:12px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label
                        style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px;">SERIAL
                        / ID</label>
                    <input type="text" name="serial_number" id="equipSerial"
                        style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:12px;">
                </div>
                <div>
                    <label
                        style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px;">STATUS</label>
                    <select name="status" id="equipStatus"
                        style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:12px;">
                        <option value="Available">Available</option>
                        <option value="In Use">In Use</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Retired">Retired</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label
                    style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px;">PHOTO</label>
                <input type="file" name="equipment_photo" accept="image/*">
            </div>

            <div style="margin-bottom: 30px;">
                <label
                    style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px;">DESCRIPTION</label>
                <textarea name="description" id="equipDesc" rows="3"
                    style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:12px; resize:none;"></textarea>
            </div>

            <div style="display:flex; gap:15px; justify-content:flex-end;">
                <button type="button" onclick="closeEquipModal()"
                    style="padding:12px 25px; border-radius:12px; border:1px solid #e2e8f0; background:#fff; color:#64748b;">Cancel</button>
                <button type="submit"
                    style="padding:12px 35px; border-radius:12px; border:none; background:#6366f1; color:#fff; font-weight:700;">Save
                    Details</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showEquipModal(id) {
        document.getElementById(id).style.display = 'block';
        document.getElementById('equipModalTitle').innerText = 'Add Equipment';
        document.getElementById('equipId').value = '';
        document.getElementById('equipName').value = '';
        document.getElementById('equipSerial').value = '';
        document.getElementById('equipDesc').value = '';
        document.getElementById('equipStatus').value = 'Available';
        document.getElementById('equipExistingImage').value = '';
    }

    function closeEquipModal() {
        document.getElementById('addEquipModal').style.display = 'none';
    }

    function editEquip(equip) {
        document.getElementById('addEquipModal').style.display = 'block';
        document.getElementById('equipModalTitle').innerText = 'Edit Equipment';
        document.getElementById('equipId').value = equip.id;
        document.getElementById('equipName').value = equip.name;
        document.getElementById('equipSerial').value = equip.serial_number;
        document.getElementById('equipDesc').value = equip.description;
        document.getElementById('equipStatus').value = equip.status;
        document.getElementById('equipExistingImage').value = equip.image_url;
    }
</script>