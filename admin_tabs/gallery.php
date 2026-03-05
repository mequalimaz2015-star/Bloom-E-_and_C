<?php
// Gallery Management Tab
$gallery = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
$gallery_count = count($gallery);
?>
<div
    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">Gallery Management</h2>
        <p style="color: #64748b; font-size: 14px;">
            <i class="fa-solid fa-images" style="color:#4361ee; margin-right:6px;"></i>
            <?= $gallery_count ?> image<?= $gallery_count !== 1 ? 's' : '' ?> in gallery
        </p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <!-- Import from Directory Button -->
        <a href="../import_gallery.php" target="_blank" class="btn"
            style="background: #7c3aed; color: #fff; border-radius: 10px; padding: 10px 18px; font-weight: 600; display: flex; align-items: center; gap: 8px; text-decoration: none;"
            title="Auto-import all images from uploads/gallery folder into the database">
            <i class="fa-solid fa-folder-open"></i> Import from Directory
        </a>
        <!-- Add New Image Button -->
        <button onclick="document.getElementById('addGalleryModal').style.display='flex';" class="btn"
            style="background: #059669; color: #fff; border-radius: 10px; padding: 10px 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus"></i> Add New Image
        </button>
    </div>
</div>

<!-- Gallery Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
    <?php foreach ($gallery as $img): ?>
        <div class="card"
            style="padding: 0; overflow: hidden; position: relative; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: transform 0.2s, box-shadow 0.2s;"
            onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.15)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.08)';">
            <!-- Image -->
            <div style="position: relative; height: 190px; overflow: hidden;">
                <img src="../<?= htmlspecialchars($img['image_url']) ?>"
                    style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                <!-- Category Badge -->
                <span
                    style="position: absolute; top: 10px; left: 10px; background: rgba(67,97,238,0.9); color: #fff; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 20px; letter-spacing: 0.5px; text-transform: uppercase;">
                    <?= htmlspecialchars($img['category']) ?>
                </span>
            </div>
            <!-- Info -->
            <div style="padding: 14px;">
                <p
                    style="margin: 0 0 4px; font-size: 13px; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($img['title'] ?: 'No Title') ?>
                </p>
                <?php if (!empty($img['description'])): ?>
                    <p
                        style="margin: 0 0 10px; font-size: 11px; color: #64748b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        <?= htmlspecialchars($img['description']) ?>
                    </p>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 10px; color: #94a3b8;">
                        <i class="fa-regular fa-calendar"></i> <?= date("M d, Y", strtotime($img['created_at'])) ?>
                    </span>
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                        <button type="button" class="btn-icon btn-delete"
                            onclick="modernDelete('delete_gallery', '<?= $img['id'] ?>', '<?= htmlspecialchars($img['title'] ?: 'Image', ENT_QUOTES) ?>', 'Gallery Image')"
                            style="padding: 6px 10px; font-size: 12px; border-radius: 8px; border: none; background: #fee2e2; color: #ef4444; cursor: pointer;"
                            title="Delete Image">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        <span
                            style="font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase;">Delete</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($gallery)): ?>
        <div
            style="grid-column: 1/-1; text-align: center; padding: 60px 20px; background: #f8fafc; border-radius: 16px; border: 2px dashed #e2e8f0;">
            <i class="fa-solid fa-images" style="font-size: 40px; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
            <p style="color: #94a3b8; font-weight: 600; font-size: 15px; margin-bottom: 8px;">No images in gallery yet</p>
            <p style="color: #cbd5e1; font-size: 13px; margin-bottom: 20px;">Upload images manually or use <strong>Import
                    from Directory</strong> to bulk-add existing images.</p>
            <button onclick="document.getElementById('addGalleryModal').style.display='flex';" class="btn btn-primary"
                style="border-radius: 10px; padding: 10px 20px; font-size: 13px; font-weight: 600;">
                <i class="fa-solid fa-plus"></i> Add First Image
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Add Gallery Modal -->
<div class="modal-overlay" id="addGalleryModal" style="display: none;">
    <div class="modal-content" style="max-width: 520px;">
        <div class="card-header" style="border-bottom:none; margin-bottom:5px; padding-bottom:0;">
            <span class="card-title" style="font-size: 20px;"><i class="fa-solid fa-image"
                    style="margin-right:8px; color:#059669;"></i>Add Gallery Image</span>
            <button type="button" onclick="document.getElementById('addGalleryModal').style.display='none';" class="btn"
                style="background:none; border:none; color:#888; font-size:22px; cursor:pointer; padding:0;"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="add_gallery" value="1">

            <div style="margin-top: 15px; text-align: center;">
                <div style="width: 100%; height: 220px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 14px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; margin-bottom: 20px; cursor: pointer; transition: border-color 0.2s;"
                    onmouseover="this.style.borderColor='#4361ee';" onmouseout="this.style.borderColor='#cbd5e1';">
                    <input type="file" name="gallery_photo" accept="image/*" required
                        style="position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;"
                        onchange="previewMenuImage(this, 'gallery_preview', 'gallery_placeholder')">
                    <div id="gallery_placeholder" style="text-align: center; color: #94a3b8; pointer-events: none;">
                        <i class="fa-solid fa-cloud-arrow-up"
                            style="font-size: 36px; margin-bottom: 12px; color: #4361ee;"></i><br>
                        <strong style="color: #475569;">Click or drag to upload</strong><br>
                        <small>JPG, PNG, GIF, WEBP accepted</small>
                    </div>
                    <img id="gallery_preview" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label
                    style="font-size: 13px; color: #64748b; display: block; margin-bottom: 5px; font-weight: 600;">Image
                    Title</label>
                <input type="text" name="title" placeholder="e.g., Dining Hall, Signature Dish"
                    style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-family: inherit;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label
                    style="font-size: 13px; color: #64748b; display: block; margin-bottom: 5px; font-weight: 600;">Category</label>
                <select name="category"
                    style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-family: inherit;">
                    <option value="Restaurant">🏠 Restaurant</option>
                    <option value="Food">🍽️ Food</option>
                    <option value="Beverage">🥤 Beverage</option>
                    <option value="Ambience">✨ Ambience</option>
                    <option value="Events">🎉 Events</option>
                    <option value="Kitchen">👨‍🍳 Kitchen</option>
                    <option value="Outdoor">🌿 Outdoor</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label
                    style="font-size: 13px; color: #64748b; display: block; margin-bottom: 5px; font-weight: 600;">Description</label>
                <textarea name="description" placeholder="Add more details about this photo..." rows="3"
                    style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-family: inherit; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn"
                    onclick="document.getElementById('addGalleryModal').style.display='none';"
                    style="background: #f1f5f9; color: #475569; border-radius: 8px; padding: 12px 20px; font-weight: 600;">Cancel</button>
                <button type="submit" class="btn btn-primary"
                    style="border-radius: 8px; padding: 12px 25px; font-weight: 600;">
                    <i class="fa-solid fa-upload" style="margin-right: 6px;"></i>Upload to Gallery
                </button>
            </div>
        </form>
    </div>
</div>