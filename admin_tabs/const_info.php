<?php $c = $pdo->query("SELECT * FROM construction_info WHERE id=1")->fetch(); ?>
<div style="max-width: 1000px; margin: 0 auto; padding: 20px 0 60px;">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_construction_info" value="1">

        <!-- 1. Hero Section Setup -->
        <div class="card"
            style="border: 2px solid #f39c12; border-radius: 16px; overflow: hidden; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(243, 156, 18, 0.1);">
            <div class="card-header"
                style="background: linear-gradient(135deg, #f39c12, #e67e22); padding: 20px 25px; border-bottom: 2px solid #f39c12;">
                <span class="card-title"
                    style="font-size: 20px; color: #fff; display: flex; align-items: center; gap: 10px; margin: 0;">
                    <i class="fa-solid fa-person-digging"></i> Hero Section (Main Banner)
                </span>
                <p style="margin: 5px 0 0; font-size: 12px; color: #fff;">The first thing users see on the construction
                    portal</p>
            </div>
            <div style="padding: 25px;">
                <div style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap;">
                    <div style="text-align: center;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #e67e22; display: block; margin-bottom: 10px; text-transform: uppercase;">Banner
                            Image</label>
                        <div
                            style="width: 200px; height: 260px; border-radius: 12px; overflow: hidden; border: 4px solid #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.1); background: #eee;">
                            <img id="heroPreview"
                                src="<?= !empty($c['hero_image']) ? htmlspecialchars($c['hero_image']) : 'https://images.unsplash.com/photo-1541888946425-d81bb19480c5?q=80&w=2070' ?>"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <input type="file" name="hero_photo" onchange="previewImage(this, 'heroPreview')"
                            style="margin-top: 15px; font-size: 11px; width: 100%;">
                        <input type="hidden" name="existing_hero_image"
                            value="<?= htmlspecialchars($c['hero_image'] ?? '') ?>">
                        <p style="font-size: 10px; color: #999; margin-top: 5px;">Recommended: 1920x1080px</p>
                    </div>
                    <div style="flex: 1; min-width: 300px;">
                        <div style="margin-bottom:15px;">
                            <label style="font-size:12px; font-weight:600; color:#666;">Hero Welcome Text (Top)</label>
                            <input type="text" name="hero_title"
                                value="<?= htmlspecialchars($c['hero_title'] ?? 'WELCOME TO OUR COMPANY') ?>"
                                placeholder="e.g. WELCOME TO OUR COMPANY">
                        </div>
                        <div style="margin-bottom:15px;">
                            <label style="font-size:12px; font-weight:600; color:#666;">Hero Big Heading</label>
                            <input type="text" name="hero_subtitle"
                                value="<?= htmlspecialchars($c['hero_subtitle'] ?? 'Building your vision with precision.') ?>"
                                placeholder="e.g. Building your vision with precision.">
                        </div>
                        <div style="margin-bottom:15px;">
                            <label style="font-size:12px; font-weight:600; color:#666;">Hero Short Description</label>
                            <textarea name="hero_description"
                                rows="4"><?= htmlspecialchars($c['hero_description'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label style="font-size:12px; font-weight:600; color:#e67e22;">Hero Background Video</label>
                            <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                                <input type="file" name="hero_video_photo" style="font-size: 11px; flex: 1;">
                                <span style="font-size: 11px; color: #999;">OR</span>
                                <input type="text" name="hero_video_url"
                                    value="<?= htmlspecialchars($c['hero_video'] ?? '') ?>"
                                    placeholder="Video URL (e.g. assets/videos/hero.mp4)"
                                    style="flex: 2; padding: 8px; border-radius: 4px; border: 1px solid #ddd; font-size: 12px;">
                            </div>
                            <input type="hidden" name="existing_hero_video"
                                value="<?= htmlspecialchars($c['hero_video'] ?? '') ?>">
                            <p style="font-size: 10px; color: #999; margin-top: 5px;">Upload a video or provide a
                                relative path/external URL. Used in Video Hero view.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Basic Company Information -->
        <div class="card"
            style="border: 1px solid #eee; border-radius: 16px; overflow: hidden; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div class="card-header" style="background: #fafafa; padding: 20px 25px; border-bottom: 1px solid #eee;">
                <span class="card-title"
                    style="font-size: 18px; color: #333; display: flex; align-items: center; gap: 10px; margin: 0;">
                    <i class="fa-solid fa-building"></i> Company Identity & Contact
                </span>
            </div>
            <div style="padding: 25px;">
                <div class="form-row">
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#666;">Company Name</label>
                        <input type="text" name="company_name"
                            value="<?= htmlspecialchars($c['company_name'] ?? 'Bloom Construction') ?>" required>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#666;">Public Email</label>
                        <input type="email" name="email"
                            value="<?= htmlspecialchars($c['email'] ?? 'info@bloomconstruction.et') ?>" required>
                    </div>
                </div>
                <div class="form-row" style="margin-top:20px;">
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#666;">Phone Number</label>
                        <input type="text" name="phone"
                            value="<?= htmlspecialchars($c['phone'] ?? '+251 911 222 333') ?>" required>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#666;">Office Address</label>
                        <input type="text" name="address"
                            value="<?= htmlspecialchars($c['address'] ?? 'Addis Ababa, Ethiopia') ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Section Headers (Manage titles across pages) -->
        <div class="card"
            style="border-radius: 16px; border: 1px solid #eee; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div class="card-header" style="background: #fafafa; border-bottom: 1px solid #eee;">
                <span class="card-title"><i class="fa-solid fa-heading"></i> Site Section Headers & Subtitles</span>
            </div>
            <div style="padding: 25px;">
                <!-- FEATURE SECTION -->
                <div class="form-row" style="margin-bottom:20px;">
                    <div>
                        <label style="font-size:11px; font-weight:700; color:#999;">WHY CHOOSE US - TITLE</label>
                        <input type="text" name="why_choose_us_title"
                            value="<?= htmlspecialchars($c['why_choose_us_title'] ?? 'WHY CHOOSE US?') ?>">
                    </div>
                    <div>
                        <label style="font-size:11px; font-weight:700; color:#999;">WHY CHOOSE US - SUBTITLE</label>
                        <input type="text" name="why_choose_us_subtitle"
                            value="<?= htmlspecialchars($c['why_choose_us_subtitle'] ?? 'Quality and Excellence in every build.') ?>">
                    </div>
                </div>

                <!-- SERVICES SECTION -->
                <div class="form-row" style="margin-bottom:20px;">
                    <div>
                        <label style="font-size:11px; font-weight:700; color:#999;">OUR SERVICES - TITLE</label>
                        <input type="text" name="services_title"
                            value="<?= htmlspecialchars($c['services_title'] ?? 'OUR SERVICES') ?>">
                    </div>
                    <div>
                        <label style="font-size:11px; font-weight:700; color:#999;">OUR SERVICES - SUBTITLE</label>
                        <input type="text" name="services_subtitle"
                            value="<?= htmlspecialchars($c['services_subtitle'] ?? 'Comprehensive solutions for every project.') ?>">
                    </div>
                </div>

                <!-- PROJECTS SECTION -->
                <div class="form-row" style="margin-bottom:20px;">
                    <div>
                        <label style="font-size:11px; font-weight:700; color:#999;">OUR PROJECTS - TITLE</label>
                        <input type="text" name="projects_title"
                            value="<?= htmlspecialchars($c['projects_title'] ?? 'OUR LATEST PROJECTS') ?>">
                    </div>
                    <div>
                        <label style="font-size:11px; font-weight:700; color:#999;">OUR PROJECTS - SUBTITLE</label>
                        <input type="text" name="projects_subtitle"
                            value="<?= htmlspecialchars($c['projects_subtitle'] ?? 'Explore some of our most recent and proudest achievements.') ?>">
                    </div>
                </div>

                <!-- REVIEWS SECTION -->
                <div class="form-row" style="margin-bottom:20px;">
                    <div>
                        <label style="font-size:11px; font-weight:700; color:#999;">REVIEWS - TITLE</label>
                        <input type="text" name="reviews_title"
                            value="<?= htmlspecialchars($c['reviews_title'] ?? 'CUSTOMER HIGHLIGHTS') ?>">
                    </div>
                    <div>
                        <label style="font-size:11px; font-weight:700; color:#999;">REVIEWS - SUBTITLE</label>
                        <input type="text" name="reviews_subtitle"
                            value="<?= htmlspecialchars($c['reviews_subtitle'] ?? 'What our clients say about our commitment to excellence.') ?>">
                    </div>
                </div>

                <!-- QUOTE SECTION -->
                <div class="form-row">
                    <div>
                        <label style="font-size:11px; font-weight:700; color:#999;">QUOTE - TITLE</label>
                        <input type="text" name="quote_title"
                            value="<?= htmlspecialchars($c['quote_title'] ?? 'REQUEST A PROFESSIONAL QUOTE') ?>">
                    </div>
                    <div>
                        <label style="font-size:11px; font-weight:700; color:#999;">QUOTE - SUBTITLE</label>
                        <input type="text" name="quote_subtitle"
                            value="<?= htmlspecialchars($c['quote_subtitle'] ?? 'GET AN ESTIMATE FOR YOUR PROJECT TODAY!') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Portal Links -->
        <div class="card"
            style="border-radius: 16px; border: 1px solid #eee; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div class="card-header" style="background: #fafafa; border-bottom: 1px solid #eee;">
                <span class="card-title"><i class="fa-solid fa-link"></i> External Portal Links</span>
            </div>
            <div style="padding: 25px;">
                <div class="form-row">
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#666;">OME Page URL</label>
                        <input type="text" name="ome_page_url" value="<?= htmlspecialchars($c['ome_page_url'] ?? '') ?>"
                            placeholder="e.g. https://ome.bloomconstruction.et">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#666;">Blog URL</label>
                        <input type="text" name="blog_url" value="<?= htmlspecialchars($c['blog_url'] ?? '') ?>"
                            placeholder="e.g. https://blog.bloomconstruction.et">
                    </div>
                </div>
                <div style="margin-top:20px;">
                    <label style="font-size:12px; font-weight:600; color:#666;">Portfolio URL</label>
                    <input type="text" name="portfolio_url" value="<?= htmlspecialchars($c['portfolio_url'] ?? '') ?>"
                        placeholder="e.g. https://portfolio.bloomconstruction.et" style="width:100%;">
                </div>
            </div>
        </div>

        <!-- 5. Marketing Description -->
        <div class="card"
            style="border-radius: 16px; border: 1px solid #eee; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div class="card-header" style="background: #fafafa; border-bottom: 1px solid #eee;">
                <span class="card-title"><i class="fa-solid fa-bullhorn"></i> Global Marketing Descriptions</span>
            </div>
            <div style="padding: 25px;">
                <div style="margin-bottom:20px;">
                    <label style="font-size:12px; font-weight:600; color:#666;">Why Choose Us? (Summary Message)</label>
                    <textarea name="why_choose_us_msg"
                        rows="4"><?= htmlspecialchars($c['why_choose_us_msg'] ?? '') ?></textarea>
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:#666;">Our Services (Global
                        Description)</label>
                    <textarea name="services_desc"
                        rows="4"><?= htmlspecialchars($c['services_desc'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- 6. Manage Client Testimonials (Multiple) -->
        <div class="card"
            style="border-radius: 16px; border: 1px solid #10b98133; margin-bottom: 30px; background: #fff;">
            <div class="card-header"
                style="background: #f0fdf4; border-bottom: 1px solid #10b98133; display: flex; justify-content: space-between; align-items: center;">
                <span class="card-title" style="color: #065f46;"><i class="fa-solid fa-star"></i> Manage Client
                    Testimonials (Reviews)</span>
                <button type="button" onclick="openTestimonialModal()" class="btn"
                    style="background: #10b981; color: #fff; padding: 5px 15px; font-size: 12px; border-radius: 8px; border: none; cursor: pointer;">
                    <i class="fa-solid fa-plus"></i> Add New Review
                </button>
            </div>
            <div style="padding: 20px;">
                <?php
                $testimonials = $pdo->query("SELECT * FROM construction_testimonials ORDER BY id DESC")->fetchAll();
                if ($testimonials): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                        <?php foreach ($testimonials as $t): ?>
                            <div
                                style="border: 1px solid #eee; padding: 15px; border-radius: 12px; position: relative; background: #fafafa;">
                                <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 10px;">
                                    <div
                                        style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; border: 2px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                                        <img src="<?= !empty($t['image_url']) ? '../' . htmlspecialchars($t['image_url']) : 'https://ui-avatars.com/api/?name=' . urlencode($t['client_name']) ?>"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 13px; color: #333;">
                                            <?= htmlspecialchars($t['client_name']) ?>
                                        </div>
                                        <div style="font-size: 11px; color: #777;"><?= htmlspecialchars($t['client_role']) ?>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    style="font-size: 12px; color: #555; font-style: italic; line-height: 1.5; margin-bottom: 10px; min-height: 45px;">
                                    "<?= htmlspecialchars(substr($t['message'], 0, 100)) ?><?= strlen($t['message']) > 100 ? '...' : '' ?>"
                                </div>
                                <div style="color: #f39c12; font-size: 11px; margin-bottom: 10px;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa-<?= $i <= $t['rating'] ? 'solid' : 'regular' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <div style="display: flex; gap: 5px;">
                                    <button type="button" onclick='editTestimonial(<?= json_encode($t) ?>)'
                                        style="flex: 1; font-size: 10px; padding: 5px; background: #fff; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Edit</button>
                                    <button type="button" onclick="deleteTestimonial(<?= $t['id'] ?>)"
                                        style="flex: 1; font-size: 10px; padding: 5px; background: #fff; color: #e74c3c; border: 1px solid #ffebeb; border-radius: 4px; cursor: pointer;">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #999; font-size: 13px;">No client reviews added yet. Add at least
                        five to showcase your reputation!</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 7. Manage Highlight Features (Why Choose Us) -->
        <div class="card"
            style="border-radius: 16px; border: 1px solid #f39c1233; margin-bottom: 30px; background: #fff;">
            <div class="card-header"
                style="background: #fdf5e6; border-bottom: 1px solid #f39c1233; display: flex; justify-content: space-between; align-items: center;">
                <span class="card-title" style="color: #d35400;"><i class="fa-solid fa-lightbulb"></i> Manage Highlights
                    (Why Choose Us)</span>
                <button type="button" onclick="openFeatureModal()" class="btn"
                    style="background: #f39c12; color: #fff; padding: 5px 15px; font-size: 12px; border-radius: 8px; border: none; cursor: pointer;">
                    <i class="fa-solid fa-plus"></i> Add New
                </button>
            </div>
            <div style="padding: 20px;">
                <?php
                $features = $pdo->query("SELECT * FROM construction_features ORDER BY id ASC")->fetchAll();
                if ($features): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                        <?php foreach ($features as $f): ?>
                            <div style="border: 1px solid #eee; padding: 15px; border-radius: 12px; position: relative;">
                                <div style="color: #f39c12; margin-bottom: 10px; font-size: 18px;"><i
                                        class="<?= $f['icon_class'] ?>"></i></div>
                                <div style="font-weight: 700; font-size: 14px; margin-bottom: 5px;">
                                    <?= htmlspecialchars($f['title']) ?>
                                </div>
                                <div style="font-size: 11px; color: #666; line-height: 1.4;">
                                    <?= htmlspecialchars(substr($f['description'], 0, 60)) ?>...
                                </div>
                                <div style="margin-top: 10px; display: flex; gap: 5px;">
                                    <button type="button" onclick='editFeature(<?= json_encode($f) ?>)'
                                        style="flex: 1; font-size: 10px; padding: 5px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Edit</button>
                                    <button type="button" onclick="deleteFeature(<?= $f['id'] ?>)"
                                        style="flex: 1; font-size: 10px; padding: 5px; background: #fff5f5; color: #e74c3c; border: 1px solid #ffebeb; border-radius: 4px; cursor: pointer;">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #999; font-size: 13px;">No highlight features added yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 8. Manage Our Services -->
        <div class="card"
            style="border-radius: 16px; border: 1px solid #e67e2233; margin-bottom: 30px; background: #fff;">
            <div class="card-header"
                style="background: #fff4e6; border-bottom: 1px solid #e67e2233; display: flex; justify-content: space-between; align-items: center;">
                <span class="card-title" style="color: #d35400;"><i class="fa-solid fa-screwdriver-wrench"></i> Manage
                    Services</span>
                <button type="button" onclick="openServiceModal()" class="btn"
                    style="background: #e67e22; color: #fff; padding: 5px 15px; font-size: 12px; border-radius: 8px; border: none; cursor: pointer;">
                    <i class="fa-solid fa-plus"></i> Add New </button>
            </div>
            <div style="padding: 20px;">
                <?php
                $services = $pdo->query("SELECT * FROM construction_services ORDER BY id ASC")->fetchAll();
                if ($services): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                        <?php foreach ($services as $s): ?>
                            <div style="border: 1px solid #eee; border-radius: 12px; overflow: hidden; position: relative;">
                                <img src="<?= !empty($s['image_url']) ? '../' . $s['image_url'] : 'Images/card1.jpg' ?>"
                                    style="width: 100%; height: 100px; object-fit: cover;">
                                <div style="padding: 10px;">
                                    <div style="font-weight: 700; font-size: 13px; margin-bottom: 5px;">
                                        <?= htmlspecialchars($s['title']) ?>
                                    </div>
                                    <div style="margin-top: 10px; display: flex; gap: 5px;">
                                        <button type="button" onclick='editService(<?= json_encode($s) ?>)'
                                            style="flex: 1; font-size: 10px; padding: 5px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Edit</button>
                                        <button type="button" onclick="deleteService(<?= $s['id'] ?>)"
                                            style="flex: 1; font-size: 10px; padding: 5px; background: #fff5f5; color: #e74c3c; border: 1px solid #ffebeb; border-radius: 4px; cursor: pointer;">Delete</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #999; font-size: 13px;">No services added yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 9. Social Media Presence -->
        <div class="card" style="border-radius: 16px; border: 1px solid #eee; margin-bottom: 30px;">
            <div class="card-header"><span class="card-title"><i class="fa-solid fa-share-nodes"></i> Social Media
                    Presence</span></div>
            <div style="padding: 25px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                    <div class="input-with-icon" style="position: relative;">
                        <i class="fa-brands fa-facebook"
                            style="position: absolute; left: 15px; top: 18px; color: #1877f2;"></i>
                        <input type="text" name="facebook" value="<?= htmlspecialchars($c['facebook'] ?? '') ?>"
                            placeholder="Facebook URL" style="padding-left: 45px;">
                    </div>
                    <div class="input-with-icon" style="position: relative;">
                        <i class="fa-brands fa-twitter"
                            style="position: absolute; left: 15px; top: 18px; color: #1da1f2;"></i>
                        <input type="text" name="twitter" value="<?= htmlspecialchars($c['twitter'] ?? '') ?>"
                            placeholder="Twitter URL" style="padding-left: 45px;">
                    </div>
                    <div class="input-with-icon" style="position: relative;">
                        <i class="fa-brands fa-linkedin"
                            style="position: absolute; left: 15px; top: 18px; color: #0077b5;"></i>
                        <input type="text" name="linkedin" value="<?= htmlspecialchars($c['linkedin'] ?? '') ?>"
                            placeholder="LinkedIn URL" style="padding-left: 45px;">
                    </div>
                    <div class="input-with-icon" style="position: relative;">
                        <i class="fa-brands fa-google-plus-g"
                            style="position: absolute; left: 15px; top: 18px; color: #db4437;"></i>
                        <input type="text" name="google_plus" value="<?= htmlspecialchars($c['google_plus'] ?? '') ?>"
                            placeholder="Google+ URL" style="padding-left: 45px;">
                    </div>
                    <div class="input-with-icon" style="position: relative;">
                        <i class="fa-brands fa-youtube"
                            style="position: absolute; left: 15px; top: 18px; color: #ff0000;"></i>
                        <input type="text" name="youtube" value="<?= htmlspecialchars($c['youtube'] ?? '') ?>"
                            placeholder="YouTube URL" style="padding-left: 45px;">
                    </div>
                    <div class="input-with-icon" style="position: relative;">
                        <i class="fa-brands fa-instagram"
                            style="position: absolute; left: 15px; top: 18px; color: #e1306c;"></i>
                        <input type="text" name="instagram" value="<?= htmlspecialchars($c['instagram'] ?? '') ?>"
                            placeholder="Instagram URL" style="padding-left: 45px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary"
            style="background: #10b981; margin: 0 auto; display: block; border-radius: 12px; padding: 18px 80px; font-size: 18px; font-weight: 700; box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4); text-transform: uppercase; letter-spacing: 1px; color: #fff; border: none; cursor: pointer;">
            <i class="fa-solid fa-cloud-arrow-up" style="margin-right: 10px;"></i> Update Construction Portal
        </button>
    </form>
</div>

<!-- Hidden Delete Forms -->
<form id="deleteFeatureForm" method="POST" style="display:none;">
    <input type="hidden" name="delete_const_feature" value="1">
    <input type="hidden" name="id" id="deleteFeatureId">
</form>
<form id="deleteServiceForm" method="POST" style="display:none;">
    <input type="hidden" name="delete_const_service" value="1">
    <input type="hidden" name="id" id="deleteServiceId">
</form>
<form id="deleteTestimonialForm" method="POST" style="display:none;">
    <input type="hidden" name="delete_const_testimonial" value="1">
    <input type="hidden" name="id" id="deleteTestimonialId">
</form>

<!-- Modal: Feature Highlights -->
<div id="featureModal" class="modal"
    style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(5px);">
    <div
        style="background:#fff; margin:10% auto; padding:30px; border-radius:20px; width:100%; max-width:500px; box-shadow:0 15px 50px rgba(0,0,0,0.2);">
        <h2 id="fModalTitle" style="margin-top:0; color:#333;">Add Highlight</h2>
        <form method="POST">
            <input type="hidden" name="save_const_feature" value="1">
            <input type="hidden" name="id" id="fId">
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Title</label>
                <input type="text" name="title" id="fTitle" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Description</label>
                <textarea name="description" id="fDesc" rows="3" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;"></textarea>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Icon (e.g. fa-solid fa-leaf)</label>
                <input type="text" name="icon_class" id="fIcon"
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
            </div>
            <div style="display:flex; gap:10px;">
                <button type="button" onclick="closeM('featureModal')"
                    style="flex:1; padding:12px; border:1px solid #ddd; background:#fff; border-radius:8px; cursor:pointer;">Cancel</button>
                <button type="submit"
                    style="flex:1; padding:12px; border:none; background:#f39c12; color:#fff; border-radius:8px; cursor:pointer; font-weight:600;">Save
                    Highlight</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Testimonials -->
<div id="testimonialModal" class="modal"
    style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(5px);">
    <div
        style="background:#fff; margin:5% auto; padding:30px; border-radius:20px; width:100%; max-width:550px; box-shadow:0 15px 50px rgba(0,0,0,0.2);">
        <h2 id="tModalTitle" style="margin-top:0; color:#333;">Add Client Review</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_const_testimonial" value="1">
            <input type="hidden" name="id" id="tId">
            <input type="hidden" name="existing_image" id="tExistingImage">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Client Name</label>
                    <input type="text" name="client_name" id="tName" required
                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Client Role/Company</label>
                    <input type="text" name="client_role" id="tRole" placeholder="e.g. CEO of TechCorp"
                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
                </div>
            </div>

            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Rating (1-5)</label>
                <select name="rating" id="tRating"
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
                    <option value="5">5 Stars - Excellent</option>
                    <option value="4">4 Stars - Very Good</option>
                    <option value="3">3 Stars - Good</option>
                    <option value="2">2 Stars - Fair</option>
                    <option value="1">1 Star - Poor</option>
                </select>
            </div>

            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Review Message</label>
                <textarea name="message" id="tMsg" rows="4" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;"></textarea>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Client Photo</label>
                <input type="file" name="testimonial_photo" onchange="previewImage(this, 'tPreview')"
                    style="width:100%;">
                <div style="margin-top:10px; display: flex; align-items: center; gap: 10px;">
                    <img id="tPreview" src="https://ui-avatars.com/api/?name=Client"
                        style="width:60px; height:60px; object-fit:cover; border-radius:50%; border: 2px solid #eee;">
                    <span style="font-size: 11px; color: #999;">Square photo works best.</span>
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="button" onclick="closeM('testimonialModal')"
                    style="flex:1; padding:12px; border:1px solid #ddd; background:#fff; border-radius:8px; cursor:pointer;">Cancel</button>
                <button type="submit"
                    style="flex:1; padding:12px; border:none; background:#10b981; color:#fff; border-radius:8px; cursor:pointer; font-weight:600;">Save
                    Review</button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function closeM(id) { document.getElementById(id).style.display = 'none'; }

    // FEATURE FUNCTIONS
    function openFeatureModal() {
        document.getElementById('featureModal').style.display = 'block';
        document.getElementById('fModalTitle').innerText = 'Add Highlight';
        document.getElementById('fId').value = '';
        document.getElementById('fTitle').value = '';
        document.getElementById('fDesc').value = '';
        document.getElementById('fIcon').value = 'fa-solid fa-hard-hat';
    }
    function editFeature(f) {
        document.getElementById('featureModal').style.display = 'block';
        document.getElementById('fModalTitle').innerText = 'Edit Highlight';
        document.getElementById('fId').value = f.id;
        document.getElementById('fTitle').value = f.title;
        document.getElementById('fDesc').value = f.description;
        document.getElementById('fIcon').value = f.icon_class;
    }
    function deleteFeature(id) {
        if (confirm('Remove this highlight?')) {
            document.getElementById('deleteFeatureId').value = id;
            document.getElementById('deleteFeatureForm').submit();
        }
    }

    // SERVICE FUNCTIONS
    function openServiceModal() {
        document.getElementById('serviceModal').style.display = 'block';
        document.getElementById('sModalTitle').innerText = 'Add Service';
        document.getElementById('sId').value = '';
        document.getElementById('sTitle').value = '';
        document.getElementById('sDesc').value = '';
        document.getElementById('sBtnText').value = 'Learn More';
        document.getElementById('sBtnUrl').value = '#';
        document.getElementById('sExistingImage').value = '';
        document.getElementById('sPreviewCont').style.display = 'none';
    }
    function editService(s) {
        document.getElementById('serviceModal').style.display = 'block';
        document.getElementById('sModalTitle').innerText = 'Edit Service';
        document.getElementById('sId').value = s.id;
        document.getElementById('sTitle').value = s.title;
        document.getElementById('sDesc').value = s.description;
        document.getElementById('sBtnText').value = s.button_text;
        document.getElementById('sBtnUrl').value = s.button_url;
        document.getElementById('sExistingImage').value = s.image_url;
        if (s.image_url) {
            document.getElementById('sPreview').src = '../' + s.image_url;
            document.getElementById('sPreviewCont').style.display = 'block';
        }
    }
    function deleteService(id) {
        if (confirm('Remove this service?')) {
            document.getElementById('deleteServiceId').value = id;
            document.getElementById('deleteServiceForm').submit();
        }
    }

    // TESTIMONIAL FUNCTIONS
    function openTestimonialModal() {
        document.getElementById('testimonialModal').style.display = 'block';
        document.getElementById('tModalTitle').innerText = 'Add Client Review';
        document.getElementById('tId').value = '';
        document.getElementById('tName').value = '';
        document.getElementById('tRole').value = '';
        document.getElementById('tRating').value = '5';
        document.getElementById('tMsg').value = '';
        document.getElementById('tExistingImage').value = '';
        document.getElementById('tPreview').src = 'https://ui-avatars.com/api/?name=Client';
    }
    function editTestimonial(t) {
        document.getElementById('testimonialModal').style.display = 'block';
        document.getElementById('tModalTitle').innerText = 'Edit Review';
        document.getElementById('tId').value = t.id;
        document.getElementById('tName').value = t.client_name;
        document.getElementById('tRole').value = t.client_role;
        document.getElementById('tRating').value = t.rating;
        document.getElementById('tMsg').value = t.message;
        document.getElementById('tExistingImage').value = t.image_url;
        if (t.image_url) {
            document.getElementById('tPreview').src = '../' + t.image_url;
        } else {
            document.getElementById('tPreview').src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(t.client_name);
        }
    }
    function deleteTestimonial(id) {
        if (confirm('Delete this client review permanently?')) {
            document.getElementById('deleteTestimonialId').value = id;
            document.getElementById('deleteTestimonialForm').submit();
        }
    }
</script>