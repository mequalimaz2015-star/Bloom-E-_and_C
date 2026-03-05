<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-size: 28px; font-weight: 800; color: #1e293b;">Staff Directory</h2>
    <button onclick="document.getElementById('registerEmpModal').style.display='flex';" class="btn"
        style="background: #e11d48; color: #fff; border-radius: 10px; padding: 12px 24px; font-weight: 700; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3); transition: 0.3s;"><i
            class="fa-solid fa-user-plus"></i> Register New Employee</button>
</div>

<div class="modal-overlay" id="registerEmpModal" style="display: none;">
    <div class="modal-content" style="max-width: 700px;">
        <form method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
            <input type="hidden" name="register_employee" value="1">

            <div style="display: flex; gap: 20px; align-items: flex-start;">
                <div style="flex: 1;">
                    <p
                        style="font-weight: 700; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">
                        Photo (3x4 Size)</p>
                    <div
                        style="width: 120px; height: 160px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        <input type="file" name="photo" id="emp_photo_input" accept="image/*"
                            style="position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;"
                            onchange="previewImage(this)">
                        <div id="photo_placeholder" style="text-align: center; color: #94a3b8;">
                            <i class="fa-solid fa-camera" style="font-size: 24px; margin-bottom: 5px;"></i><br>
                            <span style="font-size: 10px;">Select Photo</span>
                        </div>
                        <img id="photo_preview" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
                <div style="flex: 3;">
                    <p
                        style="font-weight: 700; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">
                        Personal Details</p>
                    <div class="form-row">
                        <select name="title" style="flex: 0.5;">
                            <option value="">Title</option>
                            <option value="Mr.">Mr.</option>
                            <option value="Ms.">Ms.</option>
                            <option value="Mrs.">Mrs.</option>
                            <option value="Dr.">Dr.</option>
                            <option value="Professor">Professor</option>
                        </select>
                        <input type="text" name="first_name" placeholder="First Name" required>
                        <input type="text" name="middle_name" placeholder="Middle Name" required>
                    </div>
                    <div class="form-row">
                        <input type="text" name="last_name" placeholder="Last Name" required>
                        <input type="text" name="role" placeholder="Employee Major / Role" required>
                    </div>
                    <div class="form-row">
                        <input type="date" name="dob" required title="Date of Birth">
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
            </div>

            <p
                style="font-weight: 700; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-top:20px; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">
                General Information</p>
            <div class="form-row">
                <select name="salary_type" required>
                    <option value="Monthly">Monthly Salary</option>
                    <option value="Daily">Daily Rate</option>
                    <option value="Hourly">Hourly Rate</option>
                </select>
                <input type="number" step="0.01" name="salary" placeholder="Salary Amount (ETB)" required>
                <input type="date" name="join_date" value="<?= date('Y-m-d') ?>" required title="Joining Date">
            </div>

            <p
                style="font-weight: 700; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-top:20px; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">
                Contact & Address</p>
            <div class="form-row">
                <input type="email" name="email" placeholder="Email Address">
                <input type="tel" name="phone" placeholder="Phone Number" required>
            </div>
            <input type="text" name="address" placeholder="Residential Address (Region, City, Woreda, H.No)"
                style="width: 100%; margin-bottom: 15px;">
            <textarea name="bio" placeholder="Short Bio / Additional Notes" rows="2"
                style="width: 100%; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px; font-family: inherit; font-size: 14px;"></textarea>

            <p
                style="font-weight: 700; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-top:20px; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">
                Emergency Contact</p>
            <div class="form-row">
                <input type="text" name="emergency_name" placeholder="Contact Person Name" required>
                <input type="tel" name="emergency_phone" placeholder="Contact Person Phone" required>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px;">
                <button type="button" onclick="closeModals()" class="btn"
                    style="background: #f1f5f9; color: #475569; padding: 12px 25px; border-radius: 12px;">Cancel</button>
                <button type="submit" class="btn"
                    style="background: #e11d48; color: #fff; padding: 12px 30px; border-radius: 12px; font-weight: 700;">Complete
                    Registration</button>
            </div>
        </form>
    </div>
</div>

<!-- Redesigned ID Card Modal -->
<div class="modal-overlay" id="idCardModal" style="display: none;">
    <div class="id-card-modal id-card-printable">
        <div class="id-card-top-accent">
            <div class="id-card-logo-area">
                <h3 id="id_company_name">BLOOM AFRICA</h3>
                <p>PREMIUM AFRICAN EXPERIENCE</p>
            </div>
        </div>
        <div class="id-card-body">
            <div class="id-avatar-circle">
                <img src="" id="id_card_img">
            </div>
            <div class="id-name-tag" id="id_card_name">NAME SURNAME</div>
            <div class="id-role-tag" id="id_card_role">CREATIVE MANAGER</div>

            <div class="id-details-list">
                <div class="id-detail-item">
                    <span class="id-detail-label">ID No</span>
                    <span class="id-detail-value" id="id_card_no">: 0000000</span>
                </div>
                <div class="id-detail-item">
                    <span class="id-detail-label">DOB</span>
                    <span class="id-detail-value" id="id_card_dob">: MM/DD/YY</span>
                </div>
                <div class="id-detail-item">
                    <span class="id-detail-label">Email</span>
                    <span class="id-detail-value" id="id_card_email">: email@bloom.com</span>
                </div>
                <div class="id-detail-item">
                    <span class="id-detail-label">Phone</span>
                    <span class="id-detail-value" id="id_card_phone">: +251 9...</span>
                </div>
            </div>

            <div class="id-barcode-area">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=BloomID" id="id_card_qr"
                    style="width: 80px; height: 80px;">
                <div style="font-size: 10px; color: #999; margin-top: 5px; font-weight: 700; letter-spacing: 2px;">
                    AUTHORISED ACCESS</div>
            </div>
        </div>
        <div style="padding: 20px; background: #fff;" class="no-print">
            <button onclick="window.print()" class="btn btn-primary"
                style="width:100%; border-radius:12px; height: 45px; background: #ee1d23;"><i
                    class="fa-solid fa-print"></i> Print ID Card</button>
            <button onclick="closeModals()" class="btn"
                style="width:100%; margin-top:10px; background:none; color:#888;">Close</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">Staff List</span></div>
    <div style="overflow-x: auto;">
        <table>
            <tr>
                <th>Name</th>
                <th>Photo</th>
                <th>Role</th>
                <th>Contact</th>
                <th>Salary</th>
                <th>Join Date</th>
                <th>Actions</th>
            </tr>
            <?php
            $staff = $pdo->query("SELECT * FROM employees ORDER BY id DESC")->fetchAll();
            foreach ($staff as $s):
                $emp_photo = !empty($s['photo']) ? $s['photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($s['name']) . '&background=dfb180&color=fff&size=100';
                ?>
                <tr>
                    <td><strong>
                            <?= htmlspecialchars($s['title'] . ' ' . $s['name']) ?>
                        </strong></td>
                    <td>
                        <div
                            style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; border: 2px solid #f1f5f9; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <img src="<?= $emp_photo ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </td>
                    <td><span class="badge confirmed" style="background:#e0e8ff; color:#4361ee;">
                            <?= htmlspecialchars($s['role']) ?>
                        </span></td>
                    <td>
                        <a href="tel:<?= $s['phone'] ?>" style="color: #1e293b; text-decoration: none; font-weight: 600;">
                            <i class="fa-solid fa-phone" style="font-size: 11px; color: #e11d48;"></i>
                            <?= htmlspecialchars($s['phone']) ?>
                        </a><br>
                        <small style="color: #64748b;"><?= htmlspecialchars($s['email']) ?></small>
                        <?php if (!empty($s['bio'])): ?>
                            <div
                                style="font-size: 10px; color: #94a3b8; max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 4px;">
                                <?= htmlspecialchars($s['bio']) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>$
                        <?= number_format($s['salary'], 2) ?>
                    </td>
                    <td>
                        <?= $s['join_date'] ?>
                    </td>
                    <td>
                        <div class="action-flex" style="gap: 12px;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <button onclick='viewEmp(<?= json_encode($s) ?>)' class="btn-icon btn-view"
                                    title="View Profile">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <span
                                    style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">View</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <button onclick='showIDCard(<?= json_encode($s) ?>)' class="btn-icon"
                                    style="background: #e0f2fe; color: #0369a1;" title="Print ID Card">
                                    <i class="fa-solid fa-id-card"></i>
                                </button>
                                <span
                                    style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">ID
                                    Card</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <button onclick='editEmp(<?= json_encode($s) ?>)' class="btn-icon btn-edit"
                                    title="Edit Profile">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <span
                                    style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Edit</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <button onclick="confirmDelete(<?= $s['id'] ?>, '<?= addslashes($s['name']) ?>')"
                                    class="btn-icon btn-danger" title="Delete Profile">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <span
                                    style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Delete</span>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<!-- Edit Employee Modal -->
<div class="modal-overlay" id="editEmpModal" style="display: none;">
    <div class="modal-content" style="max-width: 700px;">
        <div class="card-header">
            <span class="card-title">Edit Employee Profile</span>
            <button onclick="closeModals()" class="btn"
                style="background:none; border:none; font-size:20px;">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="update_employee" value="1">
            <input type="hidden" name="id" id="edit_emp_id">

            <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <label
                        style="display:block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 5px;">Photo</label>
                    <div
                        style="width: 100px; height: 130px; border-radius: 8px; border: 1px solid #ddd; overflow: hidden; background: #f8fafc; position: relative;">
                        <img id="edit_emp_photo_preview" src="" style="width:100%; height:100%; object-fit: cover;">
                        <input type="file" name="photo" style="position:absolute; inset:0; opacity:0; cursor:pointer;"
                            onchange="const fr=new FileReader(); fr.onload=e=>document.getElementById('edit_emp_photo_preview').src=e.target.result; fr.readAsDataURL(this.files[0])">
                    </div>
                </div>
                <div style="flex: 3;">
                    <div class="form-row">
                        <label style="font-size:12px; color:#64748b; flex: 0.5;">Title
                            <select name="title" id="edit_emp_title">
                                <option value="">Title</option>
                                <option value="Mr.">Mr.</option>
                                <option value="Ms.">Ms.</option>
                                <option value="Mrs.">Mrs.</option>
                                <option value="Dr.">Dr.</option>
                                <option value="Professor">Professor</option>
                            </select>
                        </label>
                        <label style="font-size:12px; color:#64748b;">First Name<input type="text" name="first_name"
                                id="edit_emp_first_name" required></label>
                        <label style="font-size:12px; color:#64748b;">Middle Name<input type="text" name="middle_name"
                                id="edit_emp_middle_name" required></label>
                    </div>
                    <div class="form-row">
                        <label style="font-size:12px; color:#64748b;">Last Name<input type="text" name="last_name"
                                id="edit_emp_last_name" required></label>
                        <label style="font-size:12px; color:#64748b;">Major / Role<input type="text" name="role"
                                id="edit_emp_role" required></label>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <label style="font-size:12px; color:#64748b;">Date of Birth<input type="date" name="dob"
                        id="edit_emp_dob"></label>
                <label style="font-size:12px; color:#64748b;">Gender
                    <select name="gender" id="edit_emp_gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </label>
            </div>

            <div class="form-row">
                <label style="font-size:12px; color:#64748b;">Salary Type
                    <select name="salary_type" id="edit_emp_salary_type">
                        <option value="Monthly">Monthly</option>
                        <option value="Daily">Daily</option>
                        <option value="Hourly">Hourly</option>
                    </select>
                </label>
                <label style="font-size:12px; color:#64748b;">Salary Amount<input type="number" step="0.1" name="salary"
                        id="edit_emp_salary" required></label>
            </div>

            <div class="form-row">
                <label style="font-size:12px; color:#64748b;">Join Date<input type="date" name="join_date"
                        id="edit_emp_date" required></label>
                <label style="font-size:12px; color:#64748b;">Email Address<input type="email" name="email"
                        id="edit_emp_email"></label>
            </div>

            <label style="font-size:12px; color:#64748b;">Phone Number<input type="text" name="phone"
                    id="edit_emp_phone" style="margin-bottom:15px;"></label>
            <label style="font-size:12px; color:#64748b;">Residential Address<input type="text" name="address"
                    id="edit_emp_address" style="margin-bottom:15px;"></label>

            <div style="background: #fff5f5; padding: 15px; border-radius: 8px; margin-top: 10px;">
                <p
                    style="font-weight: 800; color: #e11d48; font-size: 11px; text-transform: uppercase; margin-bottom: 15px;">
                    Emergency Contact</p>
                <div class="form-row">
                    <input type="text" name="emergency_name" id="edit_emp_emer_name" placeholder="Contact Name">
                    <input type="text" name="emergency_phone" id="edit_emp_emer_phone" placeholder="Phone Number">
                </div>
            </div>

            <textarea name="bio" id="edit_emp_bio" placeholder="Bio / Notes" rows="2"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 15px; font-family: inherit; font-size: 14px;"></textarea>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="generateIDFromForm()" class="btn"
                    style="flex: 1; background: #0054a6; color: #fff; font-weight: 700; height: 50px;"><i
                        class="fa-solid fa-id-card"></i> Preview ID Card</button>
                <button type="submit" class="btn"
                    style="flex: 2; background: #e11d48; color: #fff; font-weight: 700; height: 50px;">Update Employee
                    Records</button>
            </div>
        </form>
    </div>
</div>

<!-- View Employee Modal -->
<div class="modal-overlay" id="viewEmpModal" style="display: none;">
    <div class="modal-content" style="max-width: 650px;">
        <div class="card-header">
            <span class="card-title">Employee Profile Card</span>
            <button onclick="closeModals()" class="btn"
                style="background:none; border:none; font-size:20px;">&times;</button>
        </div>

        <div style="display: flex; gap: 30px; margin-bottom: 25px; padding: 10px;">
            <div style="text-align: center;">
                <div
                    style="width: 150px; height: 180px; border-radius: 12px; overflow: hidden; border: 4px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 10px;">
                    <img id="view_emp_photo" src="" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div id="view_emp_id_display" style="font-weight: 800; color: #e11d48; font-size: 16px;">BA-000</div>
                <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase;">Official Employee ID</div>
            </div>

            <div style="flex: 1;">
                <h3 style="margin: 0; font-size: 24px; color: #1e293b; font-weight: 800;">
                    <span id="view_emp_title" style="color: #64748b;"></span> <span id="view_emp_name"></span>
                </h3>
                <p id="view_emp_role" class="badge confirmed"
                    style="background:#e0e8ff; color:#4361ee; margin-top: 5px; font-size: 14px; font-weight: 700;"></p>

                <div style="margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label
                            style="font-size:10px; color:#94a3b8; text-transform:uppercase; font-weight:700;">Gender</label>
                        <p id="view_emp_gender" style="margin:2px 0; font-weight:600; color:#475569;"></p>
                    </div>
                    <div>
                        <label style="font-size:10px; color:#94a3b8; text-transform:uppercase; font-weight:700;">Date of
                            Birth</label>
                        <p id="view_emp_dob" style="margin:2px 0; font-weight:600; color:#475569;"></p>
                    </div>
                    <div>
                        <label style="font-size:10px; color:#94a3b8; text-transform:uppercase; font-weight:700;">Join
                            Date</label>
                        <p id="view_emp_date" style="margin:2px 0; font-weight:600; color:#475569;"></p>
                    </div>
                    <div>
                        <label
                            style="font-size:10px; color:#94a3b8; text-transform:uppercase; font-weight:700;">Salary</label>
                        <p id="view_emp_salary" style="margin:2px 0; font-weight:700; color:#059669;"></p>
                    </div>
                </div>
            </div>
        </div>

        <div style="background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
            <p
                style="font-weight: 800; color: #475569; font-size: 11px; text-transform: uppercase; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                Contact & Residence</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <i class="fa-solid fa-envelope" style="color: #64748b; margin-right: 8px;"></i>
                    <span id="view_emp_email" style="font-size: 14px; color: #1e293b;"></span>
                </div>
                <div>
                    <i class="fa-solid fa-phone" style="color: #64748b; margin-right: 8px;"></i>
                    <span id="view_emp_phone" style="font-size: 14px; color: #1e293b; font-weight: 600;"></span>
                </div>
            </div>
            <div style="margin-top: 15px;">
                <i class="fa-solid fa-location-dot" style="color: #64748b; margin-right: 8px;"></i>
                <span id="view_emp_address" style="font-size: 14px; color: #1e293b;"></span>
            </div>
        </div>

        <div style="background: #fff5f5; border-radius: 12px; padding: 20px;">
            <p
                style="font-weight: 800; color: #e11d48; font-size: 11px; text-transform: uppercase; margin-bottom: 15px; border-bottom: 1px solid #fee2e2; padding-bottom: 5px;">
                Emergency Contact</p>
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <div style="font-size: 10px; color: #94a3b8;">Contact Name</div>
                    <div id="view_emp_emer_name" style="font-weight: 600; color: #1e293b;"></div>
                </div>
                <div>
                    <div style="font-size: 10px; color: #94a3b8;">Contact Phone</div>
                    <div id="view_emp_emer_phone" style="font-weight: 700; color: #e11d48;"></div>
                </div>
            </div>
        </div>

        <button onclick="closeModals()" class="btn"
            style="width:100%; margin-top:20px; background:#1e293b; color:#fff; padding:15px; border-radius:12px; font-weight:700;">Close
            Profile</button>
    </div>
</div>