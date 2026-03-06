<?php
$projects = $pdo->query("SELECT * FROM construction_projects ORDER BY created_at DESC")->fetchAll();
?>

<div class="card" style="border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: none; overflow: hidden;">
    <div class="card-header"
        style="background: #fff; border-bottom: 1px solid #f1f5f9; padding: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <span class="card-title"
            style="margin: 0; display: flex; align-items: center; gap: 12px; font-weight: 700; color: #1e293b;">
            <i class="fa-solid fa-building" style="color: #10b981;"></i> Construction Projects
        </span>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <div id="cproj_bulk_actions" style="display: none; align-items: center; gap: 10px;">
                <span id="cproj_selected_count" style="font-size: 13px; font-weight: 700; color: #2563eb;">0
                    selected</span>
                <form method="POST" style="display: flex; gap: 5px;">
                    <input type="hidden" name="cproj_bulk_ids" id="cproj_bulk_ids_input">
                    <button type="submit" name="bulk_delete_const_projects" class="btn"
                        onclick="return confirm('Are you sure you want to delete the selected projects?')"
                        style="background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; padding: 7px 16px; font-size: 12px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(239,68,68,0.3);">
                        <i class="fa-solid fa-trash-can"></i> Delete Selected
                    </button>
                </form>
            </div>
            <button class="btn btn-primary btn-sm" onclick="showModal('addProjectModal')"
                style="border-radius: 8px; padding: 8px 16px; font-weight: 600; display: flex; align-items: center; gap: 8px; background: #10b981; border: none;">
                <i class="fa-solid fa-plus"></i> Add New Project
            </button>
        </div>
    </div>
    <div style="padding: 25px;">
        <div style="overflow-x: auto;">
            <table class="data-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="padding: 15px; border-bottom: 2px solid #f1f5f9; width: 40px; text-align: center;">
                            <input type="checkbox" id="select_all_cproj" onchange="toggleSelectAllCProj(this)"
                                style="width: 16px; height: 16px; cursor: pointer; accent-color: #2563eb;">
                        </th>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: left; font-weight: 600; color: #64748b; font-size: 13px;">
                            Project Info</th>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: left; font-weight: 600; color: #64748b; font-size: 13px;">
                            Status</th>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: left; font-weight: 600; color: #64748b; font-size: 13px;">
                            Timeline</th>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: right; font-weight: 600; color: #64748b; font-size: 13px;">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                                    <i class="fa-solid fa-folder-open" style="font-size: 40px; color: #e2e8f0;"></i>
                                    <span>No projects registered yet. Start by adding your first project!</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($projects as $proj): ?>
                            <tr>
                                <td style="padding: 20px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                                    <input type="checkbox" class="cproj-checkbox" value="<?= $proj['id'] ?>"
                                        onchange="updateCProjBulkUI()"
                                        style="width: 16px; height: 16px; cursor: pointer; accent-color: #2563eb;">
                                </td>
                                <td style="padding: 20px; border-bottom: 1px solid #f1f5f9;">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div
                                            style="width: 50px; height: 50px; border-radius: 10px; overflow: hidden; background: #f1f5f9; border: 1px solid #e2e8f0;">
                                            <img src="<?= htmlspecialchars($proj['image_url'] ?: 'https://images.unsplash.com/photo-1541888941257-236b281f021e?q=80&w=200') ?>"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #1e293b;">
                                                <?= htmlspecialchars($proj['title'] ?? $proj['name'] ?? 'Untitled') ?>
                                            </div>
                                            <div style="font-size: 12px; color: #64748b;">
                                                <?= htmlspecialchars(substr($proj['description'] ?? '', 0, 50)) ?>...
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 20px; border-bottom: 1px solid #f1f5f9;">
                                    <?php
                                    $status_colors = [
                                        'Planning' => ['bg' => '#e0f2fe', 'text' => '#0369a1'],
                                        'Ongoing' => ['bg' => '#fef9c3', 'text' => '#a16207'],
                                        'Completed' => ['bg' => '#dcfce7', 'text' => '#15803d'],
                                        'On Hold' => ['bg' => '#fee2e2', 'text' => '#b91c1c']
                                    ];
                                    $color = $status_colors[$proj['status']] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                                    ?>
                                    <span
                                        style="background: <?= $color['bg'] ?>; color: <?= $color['text'] ?>; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                        <?= $proj['status'] ?>
                                    </span>
                                </td>
                                <td style="padding: 20px; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 13px;">
                                    <div><i class="fa-solid fa-calendar-day" style="width: 20px;"></i> Start:
                                        <?= !empty($proj['start_date']) ? $proj['start_date'] : 'N/A' ?>
                                    </div>
                                    <div style="margin-top: 4px;"><i class="fa-solid fa-flag-checkered"
                                            style="width: 20px;"></i> End:
                                        <?= !empty($proj['completion_date']) ? $proj['completion_date'] : 'N/A' ?>
                                    </div>
                                </td>
                                <td style="padding: 20px; border-bottom: 1px solid #f1f5f9; text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <button class="btn-icon"
                                            onclick="editProject(<?= htmlspecialchars(json_encode($proj)) ?>)"
                                            style="background: #f1f5f9; color: #475569; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.3s;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form method="POST" onsubmit="return confirm('Archive this project?')">
                                            <input type="hidden" name="id" value="<?= $proj['id'] ?>">
                                            <button type="submit" name="delete_const_project" class="btn-icon"
                                                style="background: #fee2e2; color: #ef4444; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.3s;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
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

<script>
    function toggleSelectAllCProj(source) {
        document.querySelectorAll('.cproj-checkbox').forEach(cb => cb.checked = source.checked);
        updateCProjBulkUI();
    }
    function updateCProjBulkUI() {
        const checked = document.querySelectorAll('.cproj-checkbox:checked');
        const all = document.querySelectorAll('.cproj-checkbox');
        const bulk = document.getElementById('cproj_bulk_actions');
        const count = document.getElementById('cproj_selected_count');
        const ids = document.getElementById('cproj_bulk_ids_input');
        const selAll = document.getElementById('select_all_cproj');
        if (checked.length > 0) {
            bulk.style.display = 'flex';
            count.innerText = checked.length + ' selected';
            ids.value = Array.from(checked).map(cb => cb.value).join(',');
        } else { bulk.style.display = 'none'; }
        if (all.length > 0 && checked.length === all.length) { selAll.checked = true; selAll.indeterminate = false; }
        else if (checked.length > 0) { selAll.checked = false; selAll.indeterminate = true; }
        else { selAll.checked = false; selAll.indeterminate = false; }
    }
</script>

<!-- Add/Edit Project Modal -->
<div id="addProjectModal" class="modal"
    style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div
        style="background:#fff; width:90%; max-width:600px; margin: 40px auto; border-radius:24px; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);">
        <div
            style="background: #10b981; padding: 25px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle" style="margin:0; font-size: 20px; font-weight: 700;">Add New Project</h3>
            <span onclick="closeModal('addProjectModal')" style="cursor:pointer; font-size:24px;">&times;</span>
        </div>
        <form method="POST" enctype="multipart/form-data" style="padding:30px;">
            <input type="hidden" name="save_const_project" value="1">
            <input type="hidden" name="id" id="projId">
            <input type="hidden" name="existing_image" id="projExistingImage">

            <label
                style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px; text-transform:uppercase;">Project
                Title</label>
            <input type="text" name="title" id="projTitle" required
                style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:12px; outline:none; transition:0.3s;">
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div>
            <label
                style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px; text-transform:uppercase;">Status</label>
            <select name="status" id="projStatus"
                style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:12px;">
                <option value="Planning">Planning</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
                <option value="On Hold">On Hold</option>
            </select>
        </div>
        <div>
            <label
                style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px; text-transform:uppercase;">Project
                Photo</label>
            <input type="file" name="project_photo" accept="image/*" style="font-size: 11px;">
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div>
            <label
                style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px; text-transform:uppercase;">Start
                Date</label>
            <input type="date" name="start_date" id="projStart"
                style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:12px;">
        </div>
        <div>
            <label
                style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px; text-transform:uppercase;">Commpletion
                Date (Est.)</label>
            <input type="date" name="completion_date" id="projEnd"
                style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:12px;">
        </div>
    </div>

    <div style="margin-bottom: 30px;">
        <label
            style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:8px; text-transform:uppercase;">Project
            Description</label>
        <textarea name="description" id="projDesc" rows="4"
            style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:12px; resize:none; outline:none;"></textarea>
    </div>

    <div style="display:flex; gap:15px; justify-content:flex-end;">
        <button type="button" onclick="closeModal('addProjectModal')"
            style="padding:12px 25px; border-radius:12px; border:1px solid #e2e8f0; background:#fff; color:#64748b; font-weight:600; cursor:pointer;">Cancel</button>
        <button type="submit"
            style="padding:12px 35px; border-radius:12px; border:none; background:#10b981; color:#fff; font-weight:700; cursor:pointer; box-shadow:0 4px 14px rgba(16,185,129,0.39);">Save
            Project</button>
    </div>
    </form>
</div>
</div>

<script>
    function showModal(id) {
        document.getElementById(id).style.display = 'block';
        document.getElementById('modalTitle').innerText = 'Add New Project';
        document.getElementById('projId').value = '';
        document.getElementById('projTitle').value = '';
        document.getElementById('projDesc').value = '';
        document.getElementById('projStatus').value = 'Planning';
        document.getElementById('projStart').value = '';
        document.getElementById('projEnd').value = '';
        document.getElementById('projExistingImage').value = '';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function editProject(proj) {
        document.getElementById('addProjectModal').style.display = 'block';
        document.getElementById('modalTitle').innerText = 'Edit Project';
        document.getElementById('projId').value = proj.id;
        document.getElementById('projTitle').value = proj.title || proj.name;
        document.getElementById('projDesc').value = proj.description;
        document.getElementById('projStatus').value = proj.status;
        document.getElementById('projStart').value = proj.start_date;
        document.getElementById('projEnd').value = proj.completion_date;
        document.getElementById('projExistingImage').value = proj.image_url;
    }

    // Close modal when clicking outside
    window.onclick = function (event) {
        const modal = document.getElementById('addProjectModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>