<?php
$team = $pdo->query("SELECT * FROM team_members ORDER BY order_index ASC, id ASC")->fetchAll();
?>
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <span class="card-title">Team Management</span>
        <button class="btn btn-primary" onclick="openMemberModal()"><i class="fa-solid fa-plus"></i> Add New
            Member</button>
    </div>
    <div style="padding: 20px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($team as $m): ?>
                    <tr>
                        <td>
                            <img src="<?= htmlspecialchars($m['image_url'] ?: 'admin_logo.png') ?>"
                                style="width:50px; height:50px; border-radius:50%; object-fit:cover; border:2px solid #dfb180;">
                        </td>
                        <td style="font-weight:700;">
                            <?= htmlspecialchars($m['name']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($m['role']) ?>
                        </td>
                        <td><span class="badge" style="background: #f1f5f9; color: #475569;">
                                <?= $m['order_index'] ?>
                            </span></td>
                        <td>
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                    <button class="btn btn-sm btn-outline-primary"
                                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid #3b82f6; color: #3b82f6; background: transparent; cursor: pointer;"
                                        onclick='editMember(<?= json_encode($m) ?>)' title="Edit">
                                        <i class="fa-solid fa-pen" style="font-size: 12px;"></i>
                                    </button>
                                    <span
                                        style="font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase;">Edit</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                    <form method="POST" onsubmit="return confirm('Remove this team member?')">
                                        <input type="hidden" name="delete_team_member" value="1">
                                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid #ef4444; color: #ef4444; background: transparent; cursor: pointer;"
                                            title="Delete">
                                            <i class="fa-solid fa-trash-can" style="font-size: 12px;"></i>
                                        </button>
                                    </form>
                                    <span
                                        style="font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase;">Delete</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($team)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px; color:#888;">No team members found. Click
                            Add above to start.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Member Modal -->
<div id="memberModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close" onclick="closeMemberModal()">&times;</span>
        <h2 id="modalTitle">Add Team Member</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_team_member" value="1">
            <input type="hidden" name="id" id="member_id">

            <div style="text-align: center; margin-bottom: 20px;">
                <div
                    style="width: 120px; height: 120px; border-radius: 50%; border: 3px solid #dfb180; overflow: hidden; margin: 0 auto 10px; background: #f8f9fa;">
                    <img id="tmPreview" src="admin_logo.png" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <input type="file" name="member_photo" onchange="previewImage(this, 'tmPreview')">
                <input type="hidden" name="existing_image" id="member_existing_image">
            </div>

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" id="member_name" required placeholder="e.g. Abebe Bikila">
            </div>
            <div class="form-group">
                <label>Role / Position</label>
                <input type="text" name="role" id="member_role" required placeholder="e.g. Executive Chef">
            </div>
            <div class="form-group">
                <label>Display Order (Lower numbers appear first)</label>
                <input type="number" name="order_index" id="member_order" value="0">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:20px;">Save Team Member</button>
        </form>
    </div>
</div>
<script>
    function openMemberModal() {
        document.getElementById('modalTitle').innerText = 'Add Team Member';
        document.getElementById('member_id').value = '';
        document.getElementById('member_name').value = '';
        document.getElementById('member_role').value = '';
        document.getElementById('member_order').value = '0';
        document.getElementById('member_existing_image').value = '';
        document.getElementById('tmPreview').src = 'admin_logo.png';
        document.getElementById('memberModal').style.display = 'flex';
    }
    function closeMemberModal() {
        document.getElementById('memberModal').style.display = 'none';
    }
    function editMember(m) {
        document.getElementById('modalTitle').innerText = 'Edit Team Member';
        document.getElementById('member_id').value = m.id;
        document.getElementById('member_name').value = m.name;
        document.getElementById('member_role').value = m.role;
        document.getElementById('member_order').value = m.order_index;
        document.getElementById('member_existing_image').value = m.image_url;
        document.getElementById('tmPreview').src = m.image_url || 'admin_logo.png';
        document.getElementById('memberModal').style.display = 'flex';
    }
</script>