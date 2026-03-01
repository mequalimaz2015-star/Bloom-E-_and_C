<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
    <!-- Profile Info -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-user-pen"></i> Update Profile</span>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div
                style="background: #d1fae5; color: #065f46; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <i class="fa-solid fa-circle-check"></i>
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="update_profile" value="1">

            <div
                style="display: flex; gap: 20px; align-items: center; margin-bottom: 25px; background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div
                    style="position: relative; width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    <?php
                    $current_pic = !empty($_SESSION['admin_pic']) ? $_SESSION['admin_pic'] : 'admin_logo.png';
                    ?>
                    <img id="admin_pic_preview" src="<?= $current_pic ?>"
                        style="width: 100%; height: 100%; object-fit: cover;">
                    <input type="file" name="profile_pic" accept="image/*"
                        style="position: absolute; inset: 0; opacity: 0; cursor: pointer;"
                        onchange="const fr=new FileReader(); fr.onload=e=>document.getElementById('admin_pic_preview').src=e.target.result; fr.readAsDataURL(this.files[0])">
                </div>
                <div>
                    <h4 style="margin: 0; color: #1e293b;">Profile Picture</h4>
                    <p style="margin: 3px 0 0; font-size: 12px; color: #64748b;">Click the circle to upload a new photo
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>"
                    required style="width: 100%; padding: 12px; border: 1px solid #e1e5ee; border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-top: 20px;">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?>"
                    required style="width: 100%; padding: 12px; border: 1px solid #e1e5ee; border-radius: 8px;">
                <?php if (!isset($_SESSION['admin_email'])): ?>
                    <small style="color: #64748b; display: block; margin-top: 5px;">
                        <i class="fa-solid fa-circle-info"></i> Please log out and back in to see your saved email address.
                    </small>
                <?php endif; ?>
            </div>
            <button type="submit" class="stat-btn"
                style="margin-top: 30px; background: var(--blue); color: #fff; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                Save Profile Changes
            </button>
        </form>
    </div>
    <!-- Password Change -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-lock"></i> Security & Password</span>
        </div>
        <?php if (isset($_GET['err'])): ?>
            <div
                style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($_GET['err']) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="change_password" value="1">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="old_password" placeholder="••••••••" required
                    style="width: 100%; padding: 12px; border: 1px solid #e1e5ee; border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-top: 20px;">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="••••••••" required
                    style="width: 100%; padding: 12px; border: 1px solid #e1e5ee; border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-top: 20px;">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="••••••••" required
                    style="width: 100%; padding: 12px; border: 1px solid #e1e5ee; border-radius: 8px;">
            </div>
            <button type="submit" class="stat-btn"
                style="margin-top: 30px; background: #ef4444; color: #fff; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                Update Password
            </button>
        </form>
    </div>
</div>
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <span class="card-title">Login Information</span>
    </div>
    <div style="padding: 10px 0;">
        <p style="color: #64748b; font-size: 14px;">
            <strong>Last Login:</strong>
            <?= $_SESSION['login_time'] ?? 'Just Now' ?><br>
            <strong>Account Type:</strong> System Administrator<br>
            <strong>User ID:</strong>
            <?= $_SESSION['admin_id'] ?? 'Not Tracked (Re-login)' ?>
        </p>
    </div>
</div>