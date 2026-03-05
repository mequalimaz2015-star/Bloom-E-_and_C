<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-size: 28px; font-weight: 800; color: #1e293b;">Menu Management</h2>
    <div style="display: flex; gap: 10px;">
        <!-- Hidden file input for Excel/CSV import -->
        <input type="file" id="excelFileInput" accept=".csv,.xls,.xlsx" style="display: none;"
            onchange="handleExcelFile(this)">
        <button class="btn" onclick="document.getElementById('excelFileInput').click();"
            style="background: #10b981; color: #fff; border-radius: 10px; padding: 10px 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i
                class="fa-solid fa-file-excel"></i> Import Excel</button>
        <button class="btn" onclick="document.getElementById('addDishModal').style.display='flex';"
            style="background: #059669; color: #fff; border-radius: 10px; padding: 10px 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i
                class="fa-solid fa-plus"></i> Add New Item</button>
    </div>
</div>

<!-- Import Excel Modal -->
<div class="modal-overlay" id="importExcelModal" style="display: none;">
    <div class="modal-content" style="max-width: 650px;">
        <div class="card-header" style="border-bottom:none; margin-bottom:5px; padding-bottom:0;">
            <span class="card-title" style="color: #10b981;"><i class="fa-solid fa-file-excel"></i> Import Menu from
                Excel/CSV</span>
            <button type="button" onclick="document.getElementById('importExcelModal').style.display='none';"
                class="btn" style="background:none; border:none; color:#888; font-size:22px; cursor:pointer;"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="margin: 15px 0;">
            <div id="excelFileInfo"
                style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 15px; display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-file-excel" style="font-size: 30px; color: #10b981;"></i>
                <div>
                    <div id="excelFileName" style="font-weight: 700; color: #1e293b;"></div>
                    <div id="excelFileSize" style="font-size: 12px; color: #64748b;"></div>
                </div>
            </div>
        </div>
        <div id="excelPreview"
            style="max-height: 300px; overflow: auto; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 15px;">
        </div>
        <p style="font-size: 12px; color: #64748b; margin-bottom: 15px;">
            <i class="fa-solid fa-circle-info"></i>
            <strong>CSV format:</strong> name, category, price, description (one item per row). First row can be
            headers.
        </p>
        <form method="POST" action="admin.php?tab=menu" enctype="multipart/form-data" id="importExcelForm">
            <input type="hidden" name="import_excel" value="1">
            <input type="file" name="excel_file" id="excelFormFile" accept=".csv,.xls,.xlsx" style="display: none;">
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn"
                    onclick="document.getElementById('importExcelModal').style.display='none';"
                    style="background: #f1f5f9; color: #475569; border: none; border-radius: 10px; padding: 10px 20px;">Cancel</button>
                <button type="submit" class="btn" id="importSubmitBtn"
                    style="background: #10b981; color: #fff; border-radius: 10px; padding: 10px 20px; font-weight: 600;">
                    <i class="fa-solid fa-upload"></i> Import Items
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function handleExcelFile(input) {
        if (!input.files || !input.files[0]) return;
        var file = input.files[0];

        // Show file info
        document.getElementById('excelFileName').textContent = file.name;
        var sizeKB = (file.size / 1024).toFixed(1);
        document.getElementById('excelFileSize').textContent = sizeKB + ' KB';

        // Copy file to the form's file input
        var formFileInput = document.getElementById('excelFormFile');
        var dt = new DataTransfer();
        dt.items.add(file);
        formFileInput.files = dt.files;

        // If CSV, preview content
        var preview = document.getElementById('excelPreview');
        if (file.name.endsWith('.csv')) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var lines = e.target.result.split('\n').filter(function (l) { return l.trim() !== ''; });
                var html = '<table style="width:100%; border-collapse:collapse; font-size:13px;">';
                lines.forEach(function (line, idx) {
                    var cols = line.split(',');
                    var tag = idx === 0 ? 'th' : 'td';
                    var bg = idx === 0 ? 'background:#f8fafc; font-weight:700;' : '';
                    html += '<tr>';
                    cols.forEach(function (col) {
                        html += '<' + tag + ' style="padding:8px 10px; border-bottom:1px solid #e2e8f0; text-align:left;' + bg + '">' + col.trim().replace(/^"|"$/g, '') + '</' + tag + '>';
                    });
                    html += '</tr>';
                });
                html += '</table>';
                preview.innerHTML = html;
            };
            reader.readAsText(file);
        } else {
            preview.innerHTML = '<div style="padding:20px; text-align:center; color:#64748b;"><i class="fa-solid fa-table" style="font-size:30px; margin-bottom:10px;"></i><br>Excel file selected. Click "Import Items" to process.</div>';
        }

        // Show modal
        document.getElementById('importExcelModal').style.display = 'flex';
        // Reset file input for re-selection
        input.value = '';
    }
</script>

<div class="modal-overlay" id="addDishModal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="card-header" style="border-bottom:none; margin-bottom:5px; padding-bottom:0;">
            <span class="card-title" style="font-size: 20px;">Add New Dish</span>
            <button type="button" onclick="document.getElementById('addDishModal').style.display='none';" class="btn"
                style="background:none; border:none; color:#888; font-size:22px; cursor:pointer; padding:0;"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="add_menu" value="1">
            <div style="display: flex; gap: 20px; margin-top: 15px;">
                <div style="flex: 1;">
                    <div
                        style="width: 150px; height: 150px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        <input type="file" name="dish_photo" accept="image/*"
                            style="position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;"
                            onchange="previewMenuImage(this, 'add_menu_preview', 'add_menu_placeholder')">
                        <div id="add_menu_placeholder" style="text-align: center; color: #94a3b8;">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 24px; margin-bottom: 5px;"></i><br>
                            <span style="font-size: 10px;">Upload Image</span>
                        </div>
                        <img id="add_menu_preview" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
                <div style="flex: 2;">
                    <div class="form-row">
                        <input type="text" name="name" placeholder="Dish Name" required style="width: 100%;">
                    </div>
                    <div class="form-row" style="margin-top: 10px;">
                        <input type="text" name="category" placeholder="Category (e.g., Main, Starter)" required
                            style="width: 100%;">
                    </div>
                </div>
            </div>
            <div class="form-row" style="margin-top: 15px;">
                <input type="number" step="0.01" name="price" placeholder="Price (ETB)" required>
                <input type="url" name="image_url" placeholder="Or Image URL (External)">
            </div>
            <textarea name="description" placeholder="Description" rows="3"
                style="width: 100%; margin-top: 10px;"></textarea>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn"
                    onclick="document.getElementById('addDishModal').style.display='none';"
                    style="background: #f8f9fa; color: #333; border: 1px solid #ddd; border-radius: 20px; padding: 10px 20px;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 20px; padding: 10px 20px;">Save
                    Item</button>
            </div>
        </form>
    </div>
</div>

<div class="card" style="padding: 0; background: none; border: none; box-shadow: none;">
    <table style="border-spacing: 0 10px;">
        <thead>
            <tr style="background: none;">
                <th style="padding-left: 20px;">Item</th>
                <th>Category</th>
                <th>Price</th>
                <th>Tax (%)</th>
                <th>Status</th>
                <th><i class="fa-solid fa-heart" style="color: #e74c3c;"></i> Loved</th>
                <th>Interested Customers</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $menus = $pdo->query("SELECT m.*, (SELECT GROUP_CONCAT(customer_email SEPARATOR ', ') FROM favorites WHERE menu_item_id = m.id) as lover_emails FROM menu_items m ORDER BY likes DESC, m.id DESC")->fetchAll();
            foreach ($menus as $m):
                ?>
                <tr class="menu-row">
                    <td>
                        <div class="item-cell">
                            <img src="<?= !empty($m['image_url']) ? htmlspecialchars($m['image_url']) : 'https://via.placeholder.com/50' ?>"
                                class="item-img">
                            <div class="item-info">
                                <span class="item-name">
                                    <?= htmlspecialchars($m['name']) ?>
                                </span>
                                <span class="item-desc">
                                    <?= htmlspecialchars($m['description'] ?: 'Tasty and fresh dish prepared daily.') ?>
                                </span>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge-cat">
                            <?= htmlspecialchars($m['category']) ?>
                        </span></td>
                    <td><span class="price-text">
                            <?= number_format($m['price'], 2) ?> ETB
                        </span></td>
                    <td><span class="tax-text">15.0%</span></td>
                    <td><span class="badge-status">Available</span></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #334155;">
                            <i class="fa-solid fa-heart" style="color: #e74c3c; font-size: 14px;"></i>
                            <?= $m['likes'] ?> <small style="font-weight: 400; color: #64748b;">likes</small>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; flex-wrap: wrap; gap: 5px; max-width: 250px;">
                            <?php
                            if (!empty($m['lover_emails'])) {
                                $email_array = explode(', ', $m['lover_emails']);
                                $count = count($email_array);
                                $display_emails = array_slice($email_array, 0, 2);
                                foreach ($display_emails as $email) {
                                    echo '<span class="badge" style="background: rgba(223, 177, 128, 0.1); color: #9a6852; font-size: 10px; padding: 2px 8px; border-radius: 10px; border: 1px solid rgba(223, 177, 128, 0.2); white-space: nowrap;">' . htmlspecialchars($email) . '</span>';
                                }
                                if ($count > 2) {
                                    echo '<button type="button" onclick="showAllLovers(\'' . addslashes($m['name']) . '\', \'' . addslashes($m['lover_emails']) . '\')" style="background: var(--primary); color: #000; border: none; font-size: 10px; padding: 2px 8px; border-radius: 10px; cursor: pointer; font-weight: 700; transition: 0.3s;" onmouseover="this.style.background=\'#000\'; this.style.color=\'var(--primary)\'" onmouseout="this.style.background=\'var(--primary)\'; this.style.color=\'#000\'">+' . ($count - 2) . ' more</button>';
                                }
                            } else {
                                echo '<span style="color:#cbd5e1;">-</span>';
                            }
                            ?>
                        </div>
                    </td>
                    <td>
                        <div class="action-flex" style="justify-content: center; gap: 12px;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <button class="action-btn-circle btn-circle-view"
                                    onclick="viewMenu(<?= htmlspecialchars(json_encode($m)) ?>)" title="View Details"
                                    style="border-color: #0ea5e9; color: #0ea5e9;">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <span
                                    style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">View</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <button class="action-btn-circle btn-circle-edit"
                                    onclick="editMenu(<?= htmlspecialchars(json_encode($m)) ?>)" title="Edit Item">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <span
                                    style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Edit</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <button type="button" class="action-btn-circle btn-circle-delete"
                                    onclick="modernDelete('delete_menu', '<?= $m['id'] ?>', '<?= htmlspecialchars($m['name'], ENT_QUOTES) ?>', 'Menu Item')"
                                    title="Delete Item">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <span
                                    style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Delete</span>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Edit Menu Modal -->
<div class="modal-overlay" id="editMenuModal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="card-header">
            <span class="card-title">Edit Menu Item</span>
            <button onclick="closeModals()" class="btn"
                style="background:none; border:none; font-size:20px;">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="update_menu" value="1">
            <input type="hidden" name="id" id="edit_menu_id">

            <div style="display: flex; gap: 20px; margin-top: 15px;">
                <div style="flex: 1;">
                    <div
                        style="width: 150px; height: 150px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        <input type="file" name="dish_photo" accept="image/*"
                            style="position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;"
                            onchange="previewMenuImage(this, 'edit_menu_preview', 'edit_menu_placeholder')">
                        <div id="edit_menu_placeholder" style="text-align: center; color: #94a3b8; display: none;">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 24px; margin-bottom: 5px;"></i><br>
                            <span style="font-size: 10px;">Change Image</span>
                        </div>
                        <img id="edit_menu_preview"
                            style="display: block; width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
                <div style="flex: 2;">
                    <div class="form-row">
                        <input type="text" name="name" id="edit_menu_name" placeholder="Name" required
                            style="width: 100%;">
                    </div>
                    <div class="form-row" style="margin-top: 10px;">
                        <input type="text" name="category" id="edit_menu_category" placeholder="Category" required
                            style="width: 100%;">
                    </div>
                </div>
            </div>

            <div class="form-row" style="margin-top: 15px;">
                <input type="number" step="0.01" name="price" id="edit_menu_price" placeholder="Price" required>
                <input type="url" name="image_url" id="edit_menu_image" placeholder="Image URL">
            </div>
            <textarea name="description" id="edit_menu_desc" placeholder="Description" rows="3"
                style="width:100%; margin-top:10px;"></textarea>
            <button type="submit" class="btn btn-primary"
                style="width:100%; margin-top:15px; border-radius: 10px; padding: 12px;">Update Item</button>
        </form>
    </div>
</div>
<!--  View Menu Modal -->
<div class="modal-overlay" id="viewMenuModal" style="display: none;">
    <div class="modal-content">
        <div class="card-header">
            <span class="card-title">Dish Details</span>
            <button onclick="closeModals()" class="btn"
                style="background:none; border:none; font-size:20px;">&times;</button>
        </div>
        <div style="text-align: center; margin-bottom: 20px;">
            <img id="view_menu_img" src=""
                style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 12px; display: none;">
        </div>
        <div class="form-row">
            <div><label style="font-size:12px; color:#888;">Name</label>
                <p id="view_menu_name" style="font-weight:700;"></p>
            </div>
            <div><label style="font-size:12px; color:#888;">Category</label>
                <p id="view_menu_category"></p>
            </div>
        </div>
        <div class="form-row">
            <div><label style="font-size:12px; color:#888;">Price</label>
                <p id="view_menu_price" style="color:var(--primary); font-weight:700;"></p>
            </div>
        </div>
        <div style="margin-top:10px;">
            <label style="font-size:12px; color:#888;">Description</label>
            <p id="view_menu_desc" style="font-size:14px; color:#555; line-height:1.5;"></p>
        </div>
        <button onclick="closeModals()" class="btn btn-primary" style="width:100%; margin-top:20px;">Close
            View</button>
    </div>
</div>

<!-- Interested Customers Modal -->
<div class="modal-overlay" id="loversModal"
    style="display: none; justify-content: center; align-items: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="modal-content"
        style="max-width: 500px; background: #fff; border-radius: 15px; width: 90%; overflow: hidden;">
        <div class="card-header"
            style="padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <span class="card-title" style="font-weight: 700; color: #1e293b;"><i class="fa-solid fa-heart"
                    style="color: #e74c3c;"></i> Interested Customers</span>
            <button type="button" onclick="document.getElementById('loversModal').style.display='none'" class="btn"
                style="background:none; border:none; font-size:24px; cursor:pointer; color: #64748b;">&times;</button>
        </div>
        <div style="padding: 25px;">
            <h3 id="loverDishName" style="margin-bottom: 20px; color: #1e293b; font-size: 18px; font-weight: 800;">Dish
                Name</h3>
            <div id="loverEmailsList"
                style="display: flex; flex-direction: column; gap: 12px; max-height: 400px; overflow-y: auto; padding-right: 5px;">
                <!-- Emails will be injected here -->
            </div>
        </div>
        <div
            style="padding: 15px 25px; background: #f8fafc; border-top: 1px solid #eee; display: flex; justify-content: flex-end;">
            <button type="button" onclick="document.getElementById('loversModal').style.display='none'"
                class="btn btn-primary" style="padding: 10px 25px; border-radius: 10px;">Close Window</button>
        </div>
    </div>
</div>

<script>
    function showAllLovers(dishName, emails) {
        document.getElementById('loverDishName').innerText = "Loved by customers for: " + dishName;
        const list = document.getElementById('loverEmailsList');
        list.innerHTML = '';

        const emailArray = emails.split(', ');
        emailArray.forEach(email => {
            const div = document.createElement('div');
            div.style.padding = '14px 18px';
            div.style.background = '#ffffff';
            div.style.borderRadius = '12px';
            div.style.border = '1px solid #e2e8f0';
            div.style.display = 'flex';
            div.style.alignItems = 'center';
            div.style.gap = '12px';
            div.style.fontSize = '14px';
            div.style.color = '#334155';
            div.style.boxShadow = '0 2px 4px rgba(0,0,0,0.02)';
            div.innerHTML = `<i class="fa-solid fa-envelope" style="color: var(--primary); font-size: 16px;"></i> <strong style="font-weight: 600;">${email}</strong>`;
            list.appendChild(div);
        });

        document.getElementById('loversModal').style.display = 'flex';
    }
</script>