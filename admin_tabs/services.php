<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-size: 28px; font-weight: 800; color: #1e293b;">Service Management</h2>
    <button onclick="document.getElementById('addServiceModal').style.display='flex';" class="btn"
        style="background: #a855f7; color: #fff; border-radius: 10px; padding: 10px 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i
            class="fa-solid fa-plus"></i> Add New Service</button>
</div>

<!-- Add Service Full-Page Modal -->
<div id="addServiceModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1100; overflow-y: auto;">
    <div
        style="max-width: 800px; margin: 30px auto; background: #fff; border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.3); animation: serviceSlideUp 0.3s ease;">
        <div
            style="background: linear-gradient(135deg, #a855f7, #7c3aed); color: #fff; padding: 30px 35px; border-radius: 20px 20px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 24px; font-weight: 800;"><i class="fa-solid fa-plus-circle"></i> Add
                    New Service</h2>
                <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">Fill in the details to create a new service
                </p>
            </div>
            <button type="button" onclick="document.getElementById('addServiceModal').style.display='none';"
                style="background: rgba(255,255,255,0.2); border: none; color: #fff; width: 40px; height: 40px; border-radius: 50%; font-size: 18px; cursor: pointer;"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="admin.php?tab=services" enctype="multipart/form-data" style="padding: 35px;">
            <input type="hidden" name="add_service" value="1">
            <div style="margin-bottom: 20px;">
                <label
                    style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Service
                    Title *</label>
                <input type="text" name="title" placeholder="e.g., Wedding Catering, Birthday Party Package" required
                    style="width: 100%; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 15px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label
                        style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Icon</label>
                    <input type="text" name="icon" placeholder="e.g., fa-truck"
                        style="width: 100%; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px;">
                </div>
                <div>
                    <label
                        style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Category</label>
                    <select name="category"
                        style="width: 100%; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px; background: #fff;">
                        <option value="Food Delivery">Food Delivery</option>
                        <option value="Catering Service">Catering Service</option>
                        <option value="Wedding Events">Wedding Events</option>
                        <option value="Birthday Parties">Birthday Parties</option>
                        <option value="Corporate Events">Corporate Events</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div>
                    <label
                        style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Video
                        URL</label>
                    <input type="text" name="video_url" placeholder="https://..."
                        style="width: 100%; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px;">
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label
                    style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Service
                    Image</label>
                <div style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px; text-align: center; background: #f8fafc; position: relative; cursor: pointer;"
                    onclick="this.querySelector('input[type=file]').click();">
                    <input type="file" name="service_photo" accept="image/*"
                        style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer;"
                        onchange="if(this.files[0]) this.parentElement.querySelector('.upload-text').textContent = this.files[0].name;">
                    <i class="fa-solid fa-cloud-arrow-up"
                        style="font-size: 36px; color: #a855f7; margin-bottom: 10px;"></i><br>
                    <span class="upload-text" style="color: #64748b; font-size: 14px;">Click to browse and upload an
                        image</span>
                </div>
            </div>
            <div style="margin-bottom: 25px;">
                <label
                    style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Description</label>
                <textarea name="description" placeholder="Describe the service in detail..." rows="6"
                    style="width: 100%; border-radius: 10px; padding: 14px 16px; border: 1px solid #e2e8f0; font-size: 14px; resize: vertical;"></textarea>
            </div>
            <div
                style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                <button type="button" class="btn"
                    onclick="document.getElementById('addServiceModal').style.display='none';"
                    style="background: #f1f5f9; color: #475569; border: none; border-radius: 12px; padding: 14px 28px; font-size: 15px; font-weight: 600;">Cancel</button>
                <button type="submit" class="btn"
                    style="background: linear-gradient(135deg, #a855f7, #7c3aed); color: #fff; border-radius: 12px; padding: 14px 28px; font-size: 15px; font-weight: 600; border: none;">
                    <i class="fa-solid fa-check"></i> Save Service</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Service Full-Page Modal -->
<div id="editServiceModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1100; overflow-y: auto;">
    <div
        style="max-width: 800px; margin: 30px auto; background: #fff; border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.3); animation: serviceSlideUp 0.3s ease;">
        <div
            style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #fff; padding: 30px 35px; border-radius: 20px 20px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 24px; font-weight: 800;"><i class="fa-solid fa-pen-to-square"></i> Edit
                    Service</h2>
                <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8;">Update the service details below</p>
            </div>
            <button type="button" onclick="document.getElementById('editServiceModal').style.display='none';"
                style="background: rgba(255,255,255,0.2); border: none; color: #fff; width: 40px; height: 40px; border-radius: 50%; font-size: 18px; cursor: pointer;"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="admin.php?tab=services" enctype="multipart/form-data" style="padding: 35px;">
            <input type="hidden" name="update_service" value="1">
            <input type="hidden" name="id" id="edit_service_id">
            <input type="hidden" name="existing_image" id="edit_service_existing_image">
            <div style="margin-bottom: 20px;">
                <label
                    style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Service
                    Title *</label>
                <input type="text" name="title" id="edit_service_title" placeholder="Service Title" required
                    style="width: 100%; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 15px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label
                        style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Icon</label>
                    <input type="text" name="icon" id="edit_service_icon" placeholder="fa-truck"
                        style="width: 100%; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px;">
                </div>
                <div>
                    <label
                        style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Category</label>
                    <select name="category" id="edit_service_category"
                        style="width: 100%; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px; background: #fff;">
                        <option value="Food Delivery">Food Delivery</option>
                        <option value="Catering Service">Catering Service</option>
                        <option value="Wedding Events">Wedding Events</option>
                        <option value="Birthday Parties">Birthday Parties</option>
                        <option value="Corporate Events">Corporate Events</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div>
                    <label
                        style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Status</label>
                    <select name="status" id="edit_service_status"
                        style="width: 100%; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px; background: #fff;">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label
                    style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Video
                    URL</label>
                <input type="text" name="video_url" id="edit_service_video_url" placeholder="https://..."
                    style="width: 100%; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">
                    Service Image <span style="color:#94a3b8; font-weight: 400;">(leave empty to keep current)</span>
                </label>
                <div id="edit_current_image_preview" style="margin-bottom: 10px;"></div>
                <div style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px; text-align: center; background: #f8fafc; position: relative; cursor: pointer;"
                    onclick="this.querySelector('input[type=file]').click();">
                    <input type="file" name="service_photo" accept="image/*"
                        style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer;"
                        onchange="if(this.files[0]) this.parentElement.querySelector('.upload-text').textContent = this.files[0].name;">
                    <i class="fa-solid fa-cloud-arrow-up"
                        style="font-size: 36px; color: #3b82f6; margin-bottom: 10px;"></i><br>
                    <span class="upload-text" style="color: #64748b; font-size: 14px;">Click to browse and upload a new
                        image</span>
                </div>
            </div>
            <div style="margin-bottom: 25px;">
                <label
                    style="font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Description</label>
                <textarea name="description" id="edit_service_description"
                    placeholder="Describe the service in detail..." rows="6"
                    style="width: 100%; border-radius: 10px; padding: 14px 16px; border: 1px solid #e2e8f0; font-size: 14px; resize: vertical;"></textarea>
            </div>
            <div
                style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                <button type="button" class="btn"
                    onclick="document.getElementById('editServiceModal').style.display='none';"
                    style="background: #f1f5f9; color: #475569; border: none; border-radius: 12px; padding: 14px 28px; font-size: 15px; font-weight: 600;">Cancel</button>
                <button type="submit" class="btn"
                    style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #fff; border-radius: 12px; padding: 14px 28px; font-size: 15px; font-weight: 600; border: none;">
                    <i class="fa-solid fa-floppy-disk"></i> Update Service</button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes serviceSlideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    <?php
    $services = $pdo->query("SELECT * FROM services ORDER BY id DESC")->fetchAll();
    foreach ($services as $s):
        $service_json = htmlspecialchars(json_encode([
            'id' => $s['id'],
            'title' => $s['title'],
            'icon' => $s['icon'] ?? '',
            'category' => $s['category'] ?? 'Others',
            'status' => $s['status'],
            'video_url' => $s['video_url'] ?? '',
            'description' => $s['description'],
            'image_url' => $s['image_url'] ?? ''
        ]), ENT_QUOTES, 'UTF-8');
        ?>
        <div class="card"
            style="padding: 0; overflow: hidden; border-radius: 15px; border: 1px solid #e2e8f0; background: #fff;">
            <div
                style="height: 180px; position: relative; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                <?php if ($s['image_url']): ?>
                    <img src="<?= htmlspecialchars($s['image_url']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <i class="fa-solid <?= htmlspecialchars($s['icon'] ?: 'fa-concierge-bell') ?>"
                        style="font-size: 60px; color: #cbd5e1;"></i>
                <?php endif; ?>
                <div style="position: absolute; top: 10px; right: 10px;">
                    <span class="badge"
                        style="background: <?= $s['status'] == 'Active' ? '#dcfce7; color: #166534;' : '#fee2e2; color: #991b1b;' ?> font-size: 11px;">
                        <?= $s['status'] ?>
                    </span>
                    <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 11px; margin-left: 5px;">
                        <?= htmlspecialchars($s['category'] ?? 'Others') ?>
                    </span>
                </div>
            </div>
            <div style="padding: 20px;">
                <h3 style="margin: 0 0 10px 0; font-size: 18px; color: #1e293b;">
                    <?= htmlspecialchars($s['title']) ?>
                </h3>
                <p
                    style="margin: 0 0 20px 0; font-size: 14px; color: #64748b; line-height: 1.6; height: 60px; overflow: hidden;">
                    <?= htmlspecialchars($s['description']) ?>
                </p>
                <div
                    style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 15px;">
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                        <button class="btn-icon edit-service-btn"
                            style="color: #a855f7; background: #f5f3ff; border-radius: 8px; padding: 8px 12px; border: none; cursor: pointer;"
                            data-service="<?= $service_json ?>">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <span
                            style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Edit</span>
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                        <button type="button" class="btn-icon"
                            style="color: #ef4444; border:none; background:#fee2e2; border-radius: 8px; padding: 8px 12px; cursor:pointer;"
                            onclick="modernDelete('delete_service', '<?= $s['id'] ?>', '<?= htmlspecialchars($s['title'], ENT_QUOTES) ?>', 'Service')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        <span
                            style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Delete</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    // Attach click handlers to all edit buttons
    document.querySelectorAll('.edit-service-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var s = JSON.parse(this.getAttribute('data-service'));

            document.getElementById('edit_service_id').value = s.id;
            document.getElementById('edit_service_title').value = s.title;
            document.getElementById('edit_service_icon').value = s.icon;
            document.getElementById('edit_service_video_url').value = s.video_url;
            document.getElementById('edit_service_description').value = s.description;
            document.getElementById('edit_service_existing_image').value = s.image_url;

            // Set category dropdown
            var catSelect = document.getElementById('edit_service_category');
            for (var i = 0; i < catSelect.options.length; i++) {
                if (catSelect.options[i].value === s.category) {
                    catSelect.selectedIndex = i;
                    break;
                }
            }

            // Set status dropdown
            var statusSelect = document.getElementById('edit_service_status');
            for (var i = 0; i < statusSelect.options.length; i++) {
                if (statusSelect.options[i].value === s.status) {
                    statusSelect.selectedIndex = i;
                    break;
                }
            }

            // Show current image preview
            var preview = document.getElementById('edit_current_image_preview');
            if (s.image_url) {
                preview.innerHTML = '<img src="' + s.image_url + '" style="height:70px; border-radius:8px; border:2px solid #e2e8f0;" title="Current image">';
            } else {
                preview.innerHTML = '<span style="font-size:12px; color:#94a3b8;">No current image</span>';
            }

            document.getElementById('editServiceModal').style.display = 'flex';
        });
    });
</script>