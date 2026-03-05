<?php
$services = $pdo->query("SELECT * FROM construction_services ORDER BY id DESC")->fetchAll();
?>

<div style="padding: 20px 0 60px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #333; font-size: 24px;">Construction Services</h2>
            <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Manage specific services with descriptions and
                imagery.</p>
        </div>
        <button onclick="openModal('serviceModal')" class="btn"
            style="background: #e67e22; color: #fff; padding: 12px 25px; border-radius: 10px; font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3);">
            <i class="fa-solid fa-plus"></i> Add New Service
        </button>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(330px, 1fr)); gap: 20px;">
        <?php foreach ($services as $s): ?>
            <div class="card"
                style="border-radius: 16px; overflow: hidden; border: 1px solid #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.03); background: #fff; transition: transform 0.3s ease;">
                <div style="height: 200px; position: relative; overflow: hidden;">
                    <img src="<?= !empty($s['image_url']) ? htmlspecialchars($s['image_url']) : 'https://images.unsplash.com/photo-1503387762-592fbc0b45b?q=80&w=1931' ?>"
                        style="width: 100%; height: 100%; object-fit: cover;">
                    <div
                        style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.7)); padding: 20px 15px;">
                        <h3 style="margin: 0; color: #fff; font-size: 18px;">
                            <?= htmlspecialchars($s['title']) ?>
                        </h3>
                    </div>
                </div>
                <div style="padding: 20px;">
                    <p style="color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 20px; min-height: 60px;">
                        <?= htmlspecialchars($s['description']) ?>
                    </p>
                    <div style="display: flex; gap: 10px; border-top: 1px solid #f9f9f9; padding-top: 15px;">
                        <button onclick='editService(<?= json_encode($s) ?>)' class="btn"
                            style="flex: 1; padding: 10px 15px; background: #fff; border: 1px solid #eee; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 13px;">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>
                        <form method="POST" style="flex: 1;" onsubmit="return confirm('Delete this service?');">
                            <input type="hidden" name="delete_const_service" value="1">
                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                            <button type="submit"
                                style="width: 100%; padding: 10px 15px; background: #fff5f5; color: #e74c3c; border: 1px solid #ffebeb; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 13px;">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($services)): ?>
        <div
            style="text-align: center; padding: 100px 20px; background: #fafafa; border-radius: 20px; border: 2px dashed #eee;">
            <i class="fa-solid fa-hard-hat" style="font-size: 40px; color: #ccc; margin-bottom: 15px;"></i>
            <h3 style="color: #999;">No services listed yet</h3>
            <p style="color: #bbb;">Define what you offer (e.g., Architecture, Renovation).</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="serviceModal" class="modal"
    style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(5px);">
    <div
        style="background:#fff; margin:10% auto; padding:30px; border-radius:24px; width:100%; max-width:600px; box-shadow:0 15px 50px rgba(0,0,0,0.2);">
        <h2 id="modalTitle" style="margin-top:0; color:#333;">Add Service</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_const_service" value="1">
            <input type="hidden" name="id" id="serviceId">
            <input type="hidden" name="existing_image" id="serviceExistingImage">

            <div style="display: flex; gap: 25px; align-items: flex-start; margin-bottom: 25px;">
                <div style="text-align: center;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:#555;">Image</label>
                    <div
                        style="width: 140px; height: 140px; border-radius: 16px; overflow: hidden; border: 3px solid #f5f5f5; background: #eee;">
                        <img id="servicePreview" src="https://ui-avatars.com/api/?name=Service"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <input type="file" name="service_photo" onchange="previewImage(this, 'servicePreview')"
                        style="margin-top: 10px; font-size: 11px; width: 140px;">
                </div>
                <div style="flex: 1;">
                    <div style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#555;">Service
                            Title</label>
                        <input type="text" name="title" id="serviceTitle" required
                            placeholder="e.g. Construction Management"
                            style="width:100%; padding:12px; border:1px solid #ddd; border-radius:10px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#555;">Short
                            Description</label>
                        <textarea name="description" id="serviceDescription" rows="3" required
                            placeholder="Tell clients what this service covers..."
                            style="width:100%; padding:12px; border:1px solid #ddd; border-radius:10px;"></textarea>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                <div style="flex: 1;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:#555;">Button Text</label>
                    <input type="text" name="button_text" id="serviceButtonText" placeholder="LEARN MORE"
                        style="width:100%; padding:12px; border:1px solid #ddd; border-radius:10px;">
                </div>
                <div style="flex: 1;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:#555;">Button Link</label>
                    <input type="text" name="button_url" id="serviceButtonUrl" placeholder="Full Details URL..."
                        style="width:100%; padding:12px; border:1px solid #ddd; border-radius:10px;">
                </div>
            </div>

            <div style="display:flex; gap:15px;">
                <button type="button" onclick="closeModal('serviceModal')"
                    style="flex:1; padding:15px; border:1px solid #ddd; background:#fff; border-radius:12px; cursor:pointer; font-weight:700;">Cancel</button>
                <button type="submit"
                    style="flex:2; padding:15px; border:none; background:#e67e22; color:#fff; border-radius:12px; cursor:pointer; font-weight:700;">Save
                    Service Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function editService(s) {
        openModal('serviceModal');
        document.getElementById('modalTitle').innerText = 'Edit Service';
        document.getElementById('serviceId').value = s.id;
        document.getElementById('serviceTitle').value = s.title;
        document.getElementById('serviceDescription').value = s.description;
        document.getElementById('serviceButtonText').value = s.button_text;
        document.getElementById('serviceButtonUrl').value = s.button_url;
        document.getElementById('serviceExistingImage').value = s.image_url;

        if (s.image_url) {
            document.getElementById('servicePreview').src = s.image_url;
        } else {
            document.getElementById('servicePreview').src = 'https://ui-avatars.com/api/?name=' + s.title;
        }
    }

    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>