<?php
$features = $pdo->query("SELECT * FROM construction_features ORDER BY id DESC")->fetchAll();
?>

<div style="padding: 20px 0 60px;">
    <div
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="margin: 0; color: #333; font-size: 24px;">Why Choose Us - Highlights</h2>
            <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Manage the 4 main advantages displayed on your
                homepage.</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <div id="cfeatures_bulk_actions" style="display: none; align-items: center; gap: 10px;">
                <span id="cfeatures_selected_count" style="font-size: 13px; font-weight: 700; color: #2563eb;">0
                    selected</span>
                <form method="POST" style="display: flex; gap: 5px;">
                    <input type="hidden" name="cfeatures_bulk_ids" id="cfeatures_bulk_ids_input">
                    <button type="submit" name="bulk_delete_const_features" class="btn"
                        onclick="return confirm('Are you sure you want to delete the selected highlights?')"
                        style="background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; padding: 7px 16px; font-size: 12px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(239,68,68,0.3);">
                        <i class="fa-solid fa-trash-can"></i> Delete Selected
                    </button>
                </form>
            </div>
            <label
                style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 600; color: #64748b; background: #f8fafc; padding: 6px 14px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <input type="checkbox" id="select_all_cfeatures" onchange="toggleSelectAllCFeatures(this)"
                    style="width: 16px; height: 16px; cursor: pointer; accent-color: #2563eb;"> Select All
            </label>
            <button onclick="openModal('featureModal')" class="btn"
                style="background: #c0991cff; color: #fff; padding: 12px 25px; border-radius: 10px; font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);">
                <i class="fa-solid fa-plus"></i> Add Highlight Item
            </button>
        </div>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php foreach ($features as $f): ?>
            <div class="card"
                style="border-radius: 16px; overflow: hidden; border: 1px solid #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.03); background: #fff; transition: transform 0.3s ease; position: relative;">
                <div style="position: absolute; top: 10px; right: 10px; z-index: 5;">
                    <input type="checkbox" class="cfeatures-checkbox" value="<?= $f['id'] ?>"
                        onchange="updateCFeaturesBulkUI()"
                        style="width: 18px; height: 18px; cursor: pointer; accent-color: #2563eb;">
                </div>
                <div style="padding: 25px;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div
                            style="width: 50px; height: 50px; border-radius: 50%; background: #fdf5e6; color: #f39c12; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="<?= htmlspecialchars($f['icon_class'] ?? 'fa-solid fa-check') ?>"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 18px; color: #333;">
                            <?= htmlspecialchars($f['title']) ?>
                        </h3>
                    </div>
                    <p
                        style="color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 20px; height: 70px; overflow: hidden;">
                        <?= htmlspecialchars($f['description']) ?>
                    </p>
                    <div style="display: flex; gap: 10px; padding-top: 15px; border-top: 1px solid #f9f9f9;">
                        <button onclick='editFeature(<?= json_encode($f) ?>)' class="btn"
                            style="flex: 1; padding: 8px; font-size: 13px; border: 1px solid #eee; background: #fff; border-radius: 6px; cursor: pointer;">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>
                        <form method="POST" style="flex: 1;" onsubmit="return confirm('Remove this highlight?');">
                            <input type="hidden" name="delete_const_feature" value="1">
                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                            <button type="submit"
                                style="width: 100%; padding: 8px; font-size: 13px; border: 1px solid #ffebeb; background: #fff5f5; color: #e74c3c; border-radius: 6px; cursor: pointer;">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (empty($features)): ?>
        <div
            style="text-align: center; padding: 100px 20px; background: #fafafa; border-radius: 20px; border: 2px dashed #eee;">
            <i class="fa-solid fa-lightbulb" style="font-size: 40px; color: #ccc; margin-bottom: 15px;"></i>
            <h3 style="color: #999;">No highlights added yet</h3>
            <p style="color: #bbb;">Add items like "Quality Work", "On Time", etc.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleSelectAllCFeatures(source) {
        document.querySelectorAll('.cfeatures-checkbox').forEach(cb => cb.checked = source.checked);
        updateCFeaturesBulkUI();
    }
    function updateCFeaturesBulkUI() {
        const checked = document.querySelectorAll('.cfeatures-checkbox:checked');
        const all = document.querySelectorAll('.cfeatures-checkbox');
        const bulk = document.getElementById('cfeatures_bulk_actions');
        const count = document.getElementById('cfeatures_selected_count');
        const ids = document.getElementById('cfeatures_bulk_ids_input');
        const selAll = document.getElementById('select_all_cfeatures');
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
<!-- Modal -->
<div id="featureModal" class="modal"
    style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(5px);">
    <div
        style="background:#fff; margin:10% auto; padding:30px; border-radius:20px; width:100%; max-width:500px; box-shadow:0 15px 50px rgba(0,0,0,0.2);">
        <h2 id="modalTitle" style="margin-top:0; color:#333;">Add Highlight</h2>
        <form method="POST">
            <input type="hidden" name="save_const_feature" value="1">
            <input type="hidden" name="id" id="featureId">

            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#555;">Title</label>
                <input type="text" name="title" id="featureTitle" required placeholder="e.g. WE DELIVER QUALITY"
                    style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#555;">Short Description</label>
                <textarea name="description" id="featureDescription" rows="3" required
                    placeholder="Briefly explain this advantage..."
                    style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;"></textarea>
            </div>
            <div style="margin-bottom:25px;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#555;">Icon Class
                    (FontAwesome)</label>
                <div style="display:flex; gap:10px;">
                    <input type="text" name="icon_class" id="featureIcon" placeholder="fa-solid fa-leaf"
                        style="flex:1; padding:12px; border:1px solid #ddd; border-radius:8px;">
                    <div
                        style="width:45px; height:45px; background:#f5f5f5; border-radius:8px; display:flex; align-items:center; justify-content:center; border:1px solid #ddd;">
                        <i id="iconPreview" class="fa-solid fa-leaf" style="color:#f39c12;"></i>
                    </div>
                </div>
                <p style="font-size:11px; color:#999; margin-top:5px;">Use classes from FontAwesome (e.g., fa-solid
                    fa-clock)</p>
            </div>
            <div style="display:flex; gap:15px;">
                <button type="button" onclick="closeModal('featureModal')"
                    style="flex:1; padding:12px; border:1px solid #ddd; background:#fff; border-radius:8px; cursor:pointer; font-weight:600;">Cancel</button>
                <button type="submit"
                    style="flex:1; padding:12px; border:none; background:#f39c12; color:#fff; border-radius:8px; cursor:pointer; font-weight:600;">Save
                    Highlight</button>
            </div>
        </form>
    </div>
</div>
<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'block';
        if (id === 'featureModal') {
            document.getElementById('modalTitle').innerText = 'Add Highlight';
            document.getElementById('featureId').value = '';
            document.getElementById('featureTitle').value = '';
            document.getElementById('featureDescription').value = '';
            document.getElementById('featureIcon').value = 'fa-solid fa-check';
        }
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    function editFeature(f) {
        openModal('featureModal');
        document.getElementById('modalTitle').innerText = 'Edit Highlight';
        document.getElementById('featureId').value = f.id;
        document.getElementById('featureTitle').value = f.title;
        document.getElementById('featureDescription').value = f.description;
        document.getElementById('featureIcon').value = f.icon_class;
        updateIconPreview(f.icon_class);
    }
    document.getElementById('featureIcon').addEventListener('input', function (e) {
        updateIconPreview(e.target.value);
    });
    function updateIconPreview(val) {
        const preview = document.getElementById('iconPreview');
        preview.className = val || 'fa-solid fa-check';
    }
</script>