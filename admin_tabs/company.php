<?php $c = $pdo->query("SELECT * FROM company_info WHERE id=1")->fetch(); ?>
<div class="card">
    <div class="card-header"><span class="card-title">Company Information Management</span></div>
    <form method="POST">
        <input type="hidden" name="update_company" value="1">
        <div class="form-row">
            <div>
                <label style="font-size:12px; font-weight:600; color:#666;">Company Name</label>
                <input type="text" name="company_name" value="<?= htmlspecialchars($c['company_name']) ?>" required>
            </div>
            <div>
                <label style="font-size:12px; font-weight:600; color:#666;">Contact Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($c['email']) ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label style="font-size:12px; font-weight:600; color:#666;">Phone Number</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($c['phone']) ?>" required>
            </div>
            <div>
                <label style="font-size:12px; font-weight:600; color:#666;">Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($c['address']) ?>" required>
            </div>
        </div>
        <div style="margin-bottom:15px;">
            <label style="font-size:12px; font-weight:600; color:#666;">About Company</label>
            <textarea name="about_text" rows="4"><?= htmlspecialchars($c['about_text']) ?></textarea>
        </div>
        <div class="form-row">
            <div>
                <label style="font-size:12px; font-weight:600; color:#666;">Facebook URL</label>
                <input type="text" name="facebook" value="<?= htmlspecialchars($c['facebook'] ?? '') ?>">
            </div>
            <div>
                <label style="font-size:12px; font-weight:600; color:#666;">Instagram URL</label>
                <input type="text" name="instagram" value="<?= htmlspecialchars($c['instagram'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label style="font-size:12px; font-weight:600; color:#666;">Twitter URL</label>
                <input type="text" name="twitter" value="<?= htmlspecialchars($c['twitter'] ?? '') ?>">
            </div>
            <div>
                <label style="font-size:12px; font-weight:600; color:#666;">TikTok URL</label>
                <input type="text" name="tiktok" value="<?= htmlspecialchars($c['tiktok'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label style="font-size:12px; font-weight:600; color:#666;">LinkedIn URL</label>
                <input type="text" name="linkedin" value="<?= htmlspecialchars($c['linkedin'] ?? '') ?>">
            </div>
            <div>
                <label style="font-size:12px; font-weight:600; color:#666;">Telegram URL/Link</label>
                <input type="text" name="telegram" value="<?= htmlspecialchars($c['telegram'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div>
                <label style="font-size:12px; font-weight:600; color:#666;">WhatsApp Number/Link</label>
                <input type="text" name="whatsapp" value="<?= htmlspecialchars($c['whatsapp'] ?? '') ?>">
            </div>
        </div>
</div>

<div class="card-header" style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
    <span class="card-title" style="font-size: 18px;">CEO Information</span>
</div>

<div class="form-row">
    <div>
        <label style="font-size:12px; font-weight:600; color:#666;">CEO Name</label>
        <input type="text" name="ceo_name" value="<?= htmlspecialchars($c['ceo_name'] ?? '') ?>"
            placeholder="e.g. Major Haile Gebrselassie">
    </div>
    <div>
        <label style="font-size:12px; font-weight:600; color:#666;">CEO Title</label>
        <input type="text" name="ceo_title" value="<?= htmlspecialchars($c['ceo_title'] ?? '') ?>"
            placeholder="e.g. Owner and CEO">
    </div>
</div>

<div style="margin-bottom:15px;">
    <label style="font-size:12px; font-weight:600; color:#666;">CEO Message</label>
    <textarea name="ceo_message" rows="5"
        placeholder="Welcome message to visitors..."><?= htmlspecialchars($c['ceo_message'] ?? '') ?></textarea>
</div>

<div style="margin-bottom:15px;">
    <label style="font-size:12px; font-weight:600; color:#666;">CEO Image URL</label>
    <input type="text" name="ceo_image" value="<?= htmlspecialchars($c['ceo_image'] ?? '') ?>"
        placeholder="Link to CEO portrait image">
</div>
<button type="submit" class="btn btn-primary" style="margin-top:20px; border-radius:10px;">Save Company
    Info</button>
</form>
</div>