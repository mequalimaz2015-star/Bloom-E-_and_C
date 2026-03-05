<?php
$users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();

// Detailed Role Templates (Recommended defaults)
$role_defaults = [
    'Admin' => ['menu', 'reservations', 'gallery', 'staff', 'attendance', 'payroll', 'jobs', 'applications', 'services', 'company', 'chatbot', 'communication'],
    'Manager' => ['menu', 'reservations', 'gallery', 'staff', 'attendance', 'payroll', 'services', 'communication'],
    'Supervisor' => ['menu', 'reservations', 'attendance', 'services', 'chatbot'],
    'Waiter' => ['menu', 'services']
];

$permission_groups = [
    'Restaurant' => [
        'menu' => ['label' => 'Menu Mgmt', 'icon' => 'fa-list-check'],
        'reservations' => ['label' => 'Reservations', 'icon' => 'fa-calendar-check'],
        'gallery' => ['label' => 'Gallery', 'icon' => 'fa-images'],
    ],
    'Human Resources' => [
        'staff' => ['label' => 'Staff Directory', 'icon' => 'fa-users'],
        'attendance' => ['label' => 'Attendance', 'icon' => 'fa-user-clock'],
        'payroll' => ['label' => 'Payroll Dept', 'icon' => 'fa-money-bill-transfer'],
    ],
    'Recruitment' => [
        'jobs' => ['label' => 'Job Listings', 'icon' => 'fa-briefcase'],
        'applications' => ['label' => 'Applications', 'icon' => 'fa-file-signature'],
    ],
    'Construction' => [
        'const_info' => ['label' => 'Company Info', 'icon' => 'fa-building'],
        'const_projects' => ['label' => 'Projects', 'icon' => 'fa-building'],
        'const_equipment' => ['label' => 'Equipment', 'icon' => 'fa-truck-pickup'],
        'const_info' => ['label' => 'Construction Info', 'icon' => 'fa-info-circle'],
    ],
    'Access & Comms' => [
        'services' => ['label' => 'Our Services', 'icon' => 'fa-concierge-bell'],
        'company' => ['label' => 'Portal Info', 'icon' => 'fa-info-circle'],
        'chatbot' => ['label' => 'Chatbot Room', 'icon' => 'fa-robot'],
        'communication' => ['label' => 'Comm. Hub', 'icon' => 'fa-comments']
    ]
];
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-size: 28px; font-weight: 800; color: #1e293b;">User & Access Control</h2>
    <div style="display: flex; gap: 10px;">
        <button onclick="toggleModal('addUserModal')" class="btn" style="background: #10b981; color: white;">
            <i class="fa-solid fa-user-plus"></i> Add New Admin
        </button>
        <div style="background: #f1f5f9; padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; color: #64748b;">
            <i class="fa-solid fa-user-shield" style="margin-right: 8px; color: var(--blue);"></i>
            Security Protocol Active
        </div>
    </div>
</div>

<div class="card" style="padding: 0; overflow: visible;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead style="background: #f8fafc;">
                <tr>
                    <th style="padding: 20px; text-align: left; width: 40px;"><input type="checkbox" onclick="toggleAllUsers(this)" style="cursor:pointer;"></th>
                    <th style="padding: 20px; text-align: left;">Personnel Details</th>
                    <th style="padding: 20px; text-align: left; width: 180px;">Designated Role</th>
                    <th style="padding: 20px; text-align: left;">Tab-Level Access Permissions</th>
                    <th style="padding: 20px; text-align: right; width: 140px;">Operations</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user):
                    $user_perms = json_decode($user['permissions'] ?? '[]', true);
                    $is_self = $user['id'] == ($_SESSION['admin_id'] ?? 0);
                    $is_root = $user['id'] == 1;
                    ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;" class="user-row" id="row_<?= $user['id'] ?>">
                            <td style="padding: 20px;"><input type="checkbox" class="user-select" style="cursor:pointer;"></td>
                            <td style="padding: 20px;">
                                <div style="display:flex; align-items:center; gap:15px;">
                                    <div style="width:50px; height:50px; border-radius:12px; background:#e2e8f0; display:flex; align-items:center; justify-content:center; overflow:hidden; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                                        <img src="<?= !empty($user['profile_pic']) ? $user['profile_pic'] : 'admin_logo.png' ?>" style="width:100%; height:100%; object-fit:cover;">
                                    </div>
                                    <div>
                                        <strong style="color: #1e293b; font-size: 15px;"><?= htmlspecialchars($user['full_name']) ?></strong>
                                        <?php if ($is_self): ?><span class="badge-blue">Current User</span><?php endif; ?>
                                        <br>
                                        <span style="color: #64748b; font-size: 12px;"><?= htmlspecialchars($user['email']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <form method="POST">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <td style="padding: 20px;">
                                    <div style="position: relative;">
                                        <select name="role" class="detailed-select" onchange="applyRoleTemplate(this, <?= $user['id'] ?>)" <?= ($is_root) ? 'disabled' : '' ?>>
                                            <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Administrator</option>
                                            <option value="Manager" <?= $user['role'] === 'Manager' ? 'selected' : '' ?>>System Manager</option>
                                            <option value="Supervisor" <?= $user['role'] === 'Supervisor' ? 'selected' : '' ?>>Team Supervisor</option>
                                            <option value="Waiter" <?= $user['role'] === 'Waiter' ? 'selected' : '' ?>>Staff Waiter</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down" style="position:absolute; right:15px; top:15px; font-size:10px; color:#94a3b8; pointer-events:none;"></i>
                                    </div>
                                </td>
                                <td style="padding: 20px;">
                                    <?php if ($user['role'] === 'Admin'): ?>
                                            <div class="full-access-banner">
                                                <i class="fa-solid fa-lock-open"></i> UNRESTRICTED SYSTEM ACCESS GRANTED
                                            </div>
                                    <?php else: ?>
                                            <div style="display: flex; flex-wrap: wrap; gap: 25px;">
                                                <?php $group_idx = 0;
                                                foreach ($permission_groups as $group_name => $group_tabs):
                                                    $group_idx++; ?>
                                                        <div style="flex: 1; min-width: 160px;">
                                                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                                                                <span style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;"><?= $group_name ?></span>
                                                                <label style="font-size: 10px; color: #3b82f6; cursor: pointer; font-weight: 700;">
                                                                    <input type="checkbox" onclick="toggleGroup(this, 'grp_<?= $user['id'] ?>_<?= $group_idx ?>')" style="transform:scale(0.8); margin-right:2px; vertical-align:middle;"> ALL
                                                                </label>
                                                            </div>
                                                            <div style="display: flex; flex-direction: column; gap: 6px;" class="grp_<?= $user['id'] ?>_<?= $group_idx ?>">
                                                                <?php foreach ($group_tabs as $key => $tab): ?>
                                                                        <div class="perm-chip-row">
                                                                            <input type="checkbox" id="p_<?= $user['id'] ?>_<?= $key ?>" name="perms[]" value="<?= $key ?>" class="perm-check-hidden" data-role-keys="<?= $key ?>" <?= in_array($key, $user_perms) ? 'checked' : '' ?>>
                                                                            <label for="p_<?= $user['id'] ?>_<?= $key ?>" class="perm-custom-chip">
                                                                                <i class="fa-solid <?= $tab['icon'] ?>"></i>
                                                                                <span><?= $tab['label'] ?></span>
                                                                                <div class="selection-box"></div>
                                                                            </label>
                                                                        </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                <?php endforeach; ?>
                                            </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 20px; text-align:right;">
                                    <div style="display:flex; justify-content:flex-end; gap:12px;">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                                            <button type="submit" name="update_user_permissions" class="btn-action primary" title="Save Permissions">
                                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                            </button>
                                            <span style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Update</span>
                                        </div>
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                                            <button type="button" onclick="promptPassword(<?= $user['id'] ?>)" class="btn-action warning" title="Reset Credentials">
                                                <i class="fa-solid fa-shield-keyhole"></i>
                                            </button>
                                            <span style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Reset</span>
                                        </div>
                                        <?php if (!$is_root && !$is_self): ?>
                                            <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                                                <button type="submit" name="delete_admin_user" class="btn-action danger" onclick="return confirm('🚨 WARN: This will wipe this admin account. Proceed?')" title="Delete Account">
                                                    <i class="fa-solid fa-trash-xmark"></i>
                                                </button>
                                                <span style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Delete</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </form>
                        </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const roleTemplates = <?= json_encode($role_defaults) ?>;

function applyRoleTemplate(select, userId) {
    const role = select.value;
    if (role === 'Admin') {
        if(confirm("Setting to Admin will grant Full Unrestricted Access. Page will refresh to update UI.")) {
            select.closest('form').submit();
        }
        return;
    }
    
    const allowed = roleTemplates[role] || [];
    const checkboxes = document.querySelectorAll(`#row_${userId} .perm-check-hidden`);
    
    checkboxes.forEach(cb => {
        cb.checked = allowed.includes(cb.value);
    });
}

function toggleGroup(master, groupClass) {
    const container = document.querySelector('.' + groupClass);
    const checks = container.querySelectorAll('input[type="checkbox"]');
    checks.forEach(c => c.checked = master.checked);
}

function toggleAllUsers(master) {
    document.querySelectorAll('.user-select').forEach(c => c.checked = master.checked);
}

function toggleModal(id) {
    const m = document.getElementById(id);
    if(m) m.style.display = m.style.display === 'none' ? 'block' : 'none';
}

function promptPassword(uid) {
    const newPass = prompt("Enter secure new password for this personnel:");
    if (newPass && newPass.length >= 6) {
        const f = document.createElement('form');
        f.method = 'POST';
        f.innerHTML = `<input type="hidden" name="user_id" value="${uid}"><input type="hidden" name="new_password" value="${newPass}"><input type="hidden" name="reset_admin_password" value="1">`;
        document.body.appendChild(f);
        f.submit();
    } else if (newPass) alert("Error: Use at least 6 characters.");
}
</script>

<style>
    /* Premium Detailed Styles */
    .detailed-select {
        width: 100%;
        padding: 12px 15px;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        appearance: none;
        cursor: pointer;
        transition: 0.2s;
    }
    .detailed-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
    .detailed-select:disabled { background: #f8fafc; cursor: not-allowed; opacity: 0.8; }

    .full-access-banner {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        padding: 15px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
        text-align: center;
        width: 100%;
        letter-spacing: 0.5px;
    }

    .perm-chip-row { position: relative; }
    .perm-check-hidden { position: absolute; opacity: 0; pointer-events: none; }
    
    .perm-custom-chip {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: #fff;
        border: 1.5px solid #f1f5f9;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .selection-box {
        width: 18px;
        height: 18px;
        border-radius: 5px;
        border: 2px solid #cbd5e1;
        margin-left: auto;
        position: relative;
        transition: 0.2s;
        background: #fff;
    }

    .selection-box::after {
        content: '\f00c';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 10px;
        color: #fff;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        transition: 0.2s;
    }

    .perm-check-hidden:checked + .perm-custom-chip {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #1d4ed8;
    }
    
    .perm-check-hidden:checked + .perm-custom-chip .selection-box {
        background: #3b82f6;
        border-color: #3b82f6;
    }
    
    .perm-check-hidden:checked + .perm-custom-chip .selection-box::after {
        transform: translate(-50%, -50%) scale(1);
    }

    .btn-action {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.3s;
        font-size: 16px;
    }
    .btn-action.primary { background: #2563eb; color: #fff; }
    .btn-action.warning { background: #f59e0b; color: #fff; }
    .btn-action.danger { background: #ef4444; color: #fff; }
    .btn-action:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); filter: brightness(1.1); }

    .badge-blue { background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; margin-left: 8px; vertical-align: middle; }
    
    .user-row:hover { background: #fafafa; }
</style>