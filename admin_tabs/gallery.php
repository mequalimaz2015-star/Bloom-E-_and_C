<?php
// Gallery Management Tab
?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-size: 28px; font-weight: 800; color: #1e293b;">Gallery Management</h2>
    <button onclick="document.getElementById('addGalleryModal').style.display='flex';" class="btn"
        style="background: #059669; color: #fff; border-radius: 10px; padding: 10px 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-plus"></i> Add New Image
    </button>
</div>

<!-- Gallery Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
    <?php
    $gallery = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
    foreach ($gallery as $img):
        ?>
        <div class="card"
            style="padding: 0; overflow: hidden; position: relative; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <img src="<?= htmlspecialchars($img['image_url']) ?>" style="width: 100%; height: 180px; object-fit: cover;">
            <div style="padding: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span class="badge confirmed"
                            style="background:#e0e8ff; color:#4361ee; font-size: 10px; padding: 2px 8px;">
                            <?= htmlspecialchars($img['category']) ?>
                        </span>
                        <p style="margin: 5px 0 0; font-size: 13px; font-weight: 600; color: #1e293b;">
                            <?= htmlspecialchars($img['title'] ?: 'No Title') ?>
                        </p>
                        <?php if (!empty($img['description'])): ?>
                            <p style="margin: 3px 0 0; font-size: 11px; color: #64748b; line-height: 1.3;">
                                <?= htmlspecialchars(substr($img['description'], 0, 80)) . (strlen($img['description']) > 80 ? '...' : '') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn-icon btn-delete"
                        onclick="modernDelete('delete_gallery', '<?= $img['id'] ?>', '<?= htmlspecialchars($img['title'] ?: 'Image', ENT_QUOTES) ?>', 'Gallery Image')"
                        style="padding: 5px; font-size: 12px;" title="Delete Image">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($gallery)): ?>
        <p style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;">No images in gallery yet.</p>
    <?php endif; ?>
</div>

<!-- Add Gallery Modal -->
<div class="modal-overlay" id="addGalleryModal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="card-header" style="border-bottom:none; margin-bottom:5px; padding-bottom:0;">
            <span class="card-title" style="font-size: 20px;">Add Gallery Image</span>
            <button type="button" onclick="document.getElementById('addGalleryModal').style.display='none';" class="btn"
                style="background:none; border:none; color:#888; font-size:22px; cursor:pointer; padding:0;"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="add_gallery" value="1">

            <div style="margin-top: 15px; text-align: center;">
                <div
                    style="width: 100%; height: 200px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; margin-bottom: 20px;">
                    <input type="file" name="gallery_photo" accept="image/*" required
                        style="position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;"
                        onchange="previewMenuImage(this, 'gallery_preview', 'gallery_placeholder')">
                    <div id="gallery_placeholder" style="text-align: center; color: #94a3b8;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; margin-bottom: 10px;"></i><br>
                        <span>Select Gallery Photo</span>
                    </div>
                    <img id="gallery_preview" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-size: 13px; color: #64748b; display: block; margin-bottom: 5px;">Image Title</label>
                <input type="text" name="title" placeholder="e.g., Dining Hall, Signature Dish"
                    style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-size: 13px; color: #64748b; display: block; margin-bottom: 5px;">Category</label>
                <select name="category"
                    style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <option value="Restaurant">Restaurant</option>
                    <option value="Food">Food</option>
                    <option value="Beverage">Beverage</option>
                    <option value="Ambience">Ambience</option>
                    <option value="Events">Events</option>
                    <option value="Kitchen">Kitchen</option>
                    <option value="Outdoor">Outdoor</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-size: 13px; color: #64748b; display: block; margin-bottom: 5px;">Detailed
                    Description</label>
                <textarea name="description" placeholder="Add more details about this photo..." rows="3"
                    style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-family: inherit; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn"
                    onclick="document.getElementById('addGalleryModal').style.display='none';"
                    style="background: #f1f5f9; color: #475569; border-radius: 8px; padding: 12px 20px; font-weight: 600;">Cancel</button>
                <button type="submit" class="btn btn-primary"
                    style="border-radius: 8px; padding: 12px 25px; font-weight: 600;">Upload to Gallery</button>
            </div>
        </form>
    </div>
</div>