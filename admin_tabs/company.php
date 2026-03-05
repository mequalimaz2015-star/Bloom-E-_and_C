<?php $c = $pdo->query("SELECT * FROM company_info WHERE id=1")->fetch(); ?>
<div style="max-width: 1000px; margin: 0 auto; padding: 20px 0 60px;">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_company" value="1">

        <!-- 1. CEO / Founder Management -->
        <div class="card"
            style="border: 2px solid #dfb180; border-radius: 16px; overflow: hidden; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
            <div class="card-header"
                style="background: linear-gradient(135deg, #1a1512, #2a2018); padding: 20px 25px; border-bottom: 2px solid #dfb180;">
                <span class="card-title"
                    style="font-size: 20px; color: #dfb180; display: flex; align-items: center; gap: 10px; margin: 0;">
                    <i class="fa-solid fa-user-tie"></i> CEO / Founder Management
                </span>
                <p style="margin: 5px 0 0; font-size: 12px; color: #999;">Personalize the leadership section of the
                    company</p>
            </div>
            <div style="padding: 25px;">
                <div style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 25px;">
                    <div style="text-align: center;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #dfb180; display: block; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">CEO
                            Photo</label>
                        <div style="width: 160px; height: 200px; border-radius: 12px; overflow: hidden; border: 2px solid #dfb180; cursor: pointer; background: #1a1512; position: relative;"
                            onclick="document.getElementById('ceoPhotoInput').click()">
                            <img id="ceoPhotoPreview"
                                src="<?= !empty($c['ceo_image']) ? htmlspecialchars($c['ceo_image']) : 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=400' ?>"
                                style="width: 100%; height: 100%; object-fit: cover;">
                            <div
                                style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); padding: 5px; color: #fff; font-size: 10px;">
                                <i class="fa-solid fa-camera"></i> Change Photo
                            </div>
                        </div>
                        <input type="file" name="ceo_photo" id="ceoPhotoInput" accept="image/*" style="display: none;"
                            onchange="if(this.files[0]){ let r=new FileReader(); r.onload=function(e){ document.getElementById('ceoPhotoPreview').src=e.target.result; document.getElementById('ceoPreviewThumb').src=e.target.result; }; r.readAsDataURL(this.files[0]); }">
                        <input type="hidden" name="existing_ceo_image"
                            value="<?= htmlspecialchars($c['ceo_image'] ?? '') ?>">
                        <input type="text" name="ceo_image_url" placeholder="Or paste Image URL"
                            style="margin-top: 10px; font-size: 11px; height: 30px;"
                            value="<?= htmlspecialchars($c['ceo_image'] ?? '') ?>">
                    </div>
                    <div style="flex: 1; min-width: 250px;">
                        <div class="form-row">
                            <div>
                                <label style="font-size:12px; font-weight:600; color:#666;">Full Name</label>
                                <input type="text" name="ceo_name" value="<?= htmlspecialchars($c['ceo_name'] ?? '') ?>"
                                    placeholder="e.g. John Doe">
                            </div>
                            <div>
                                <label style="font-size:12px; font-weight:600; color:#666;">Position / Title</label>
                                <input type="text" name="ceo_title"
                                    value="<?= htmlspecialchars($c['ceo_title'] ?? '') ?>"
                                    placeholder="e.g. Founder & CEO">
                            </div>
                        </div>
                        <label style="font-size:12px; font-weight:600; color:#666; margin-top:15px; display:block;">CEO
                            Message Content</label>
                        <textarea name="ceo_message" rows="6"
                            style="line-height:1.6;"><?= htmlspecialchars($c['ceo_message'] ?? '') ?></textarea>
                    </div>
                </div>
                <!-- Mini Live Preview -->
                <div
                    style="background: #fdfaf7; border-radius: 12px; padding: 15px; border: 1px solid #dfb18033; display: flex; gap: 15px; align-items: center;">
                    <img id="ceoPreviewThumb"
                        src="<?= !empty($c['ceo_image']) ? htmlspecialchars($c['ceo_image']) : 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=400' ?>"
                        style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #dfb180;">
                    <div>
                        <p
                            style="font-size: 11px; color: #dfb180; font-weight: 700; text-transform: uppercase; margin: 0 0 2px;">
                            Website View Preview</p>
                        <p style="font-size: 14px; font-weight: 700; color: #1a1512; margin: 0;">
                            <?= htmlspecialchars($c['ceo_name'] ?: 'CEO Name') ?>
                        </p>
                        <p style="font-size: 12px; color: #666; font-style: italic; margin: 0;">
                            <?= htmlspecialchars($c['ceo_title'] ?: 'Position Title') ?>
                        </p>
                    </div>
                    <div style="margin-left: auto; font-size: 20px; color: #dfb180;"><i
                            class="fa-solid fa-quote-right"></i></div>
                </div>
            </div>
        </div>

        <!-- 2. About Us Images Section -->
        <div class="card"
            style="border-radius: 16px; border: 1px solid #eee; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div class="card-header" style="background: #fafafa; border-bottom: 1px solid #eee;"><span
                    class="card-title"><i class="fa-solid fa-images"></i> About Us Images & Headline</span></div>
            <div style="padding: 25px;">
                <div style="margin-bottom: 20px;">
                    <label style="font-size:12px; font-weight:600; color:#666;">About Us Headline / Subtitle</label>
                    <input type="text" name="about_subtitle" value="<?= htmlspecialchars($c['about_subtitle'] ?? '') ?>"
                        placeholder="e.g. Traditional Ethiopian Flavour">
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div
                        style="text-align: center; background: #fafafa; padding: 15px; border-radius: 12px; border: 1px solid #f0f0f0;">
                        <label
                            style="font-size:11px; font-weight:700; color:#888; display:block; margin-bottom:10px;">MAIN
                            LARGE IMAGE</label>
                        <div
                            style="width: 100%; height: 140px; background: #eee; border-radius: 8px; overflow: hidden; margin-bottom: 15px;">
                            <img id="aboutMainPreview"
                                src="<?= htmlspecialchars($c['about_image_main'] ?: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=400') ?>"
                                style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <input type="file" name="about_main_photo" onchange="previewImage(this, 'aboutMainPreview')"
                            style="font-size: 10px; width: 100%;">
                        <input type="hidden" name="existing_about_main"
                            value="<?= htmlspecialchars($c['about_image_main'] ?? '') ?>">
                    </div>
                    <div
                        style="text-align: center; background: #fafafa; padding: 15px; border-radius: 12px; border: 1px solid #f0f0f0;">
                        <label
                            style="font-size:11px; font-weight:700; color:#888; display:block; margin-bottom:10px;">SIDE
                            IMAGE 1</label>
                        <div
                            style="width: 100%; height: 140px; background: #eee; border-radius: 8px; overflow: hidden; margin-bottom: 15px;">
                            <img id="aboutSub1Preview"
                                src="<?= htmlspecialchars($c['about_image_sub1'] ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=400') ?>"
                                style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <input type="file" name="about_sub1_photo" onchange="previewImage(this, 'aboutSub1Preview')"
                            style="font-size: 10px; width: 100%;">
                        <input type="hidden" name="existing_about_sub1"
                            value="<?= htmlspecialchars($c['about_image_sub1'] ?? '') ?>">
                    </div>
                    <div
                        style="text-align: center; background: #fafafa; padding: 15px; border-radius: 12px; border: 1px solid #f0f0f0;">
                        <label
                            style="font-size:11px; font-weight:700; color:#888; display:block; margin-bottom:10px;">SIDE
                            IMAGE 2</label>
                        <div
                            style="width: 100%; height: 140px; background: #eee; border-radius: 8px; overflow: hidden; margin-bottom: 15px;">
                            <img id="aboutSub2Preview"
                                src="<?= htmlspecialchars($c['about_image_sub2'] ?: 'https://images.unsplash.com/photo-1544148103-0773bb108726?q=80&w=400') ?>"
                                style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <input type="file" name="about_sub2_photo" onchange="previewImage(this, 'aboutSub2Preview')"
                            style="font-size: 10px; width: 100%;">
                        <input type="hidden" name="existing_about_sub2"
                            value="<?= htmlspecialchars($c['about_image_sub2'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="border-radius: 16px; margin-bottom: 30px;">
            <div class="card-header"><span class="card-title"><i class="fa-solid fa-building-circle-check"></i>
                    Company Information Management</span></div>
            <div style="padding: 25px;">
                <div class="form-row">
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#666;">Company Name</label>
                        <input type="text" name="company_name"
                            value="<?= htmlspecialchars($c['company_name'] ?? 'Bloom Africa Restaurant') ?>"
                            placeholder="e.g. Bloom Africa Restaurant" required>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#666;">Public Email</label>
                        <input type="email" name="email"
                            value="<?= htmlspecialchars($c['email'] ?? 'info@bloomafrica.com') ?>"
                            placeholder="e.g. info@bloomafrica.com" required>
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
                <div style="margin-top: 15px;">
                    <label style="font-size:12px; font-weight:600; color:#666;">Short Company Intro (For
                        Search/Footer)</label>
                    <textarea name="about_text" rows="3"><?= htmlspecialchars($c['about_text'] ?? '') ?></textarea>
                </div>
                <!-- Social Media URLs -->
                <div style="margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px;">
                    <label
                        style="font-size: 13px; font-weight: 700; color: #1a1512; margin-bottom: 15px; display: block; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-share-nodes" style="color: #dfb180;"></i> Social Media Presence
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px;">
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fa-brands fa-facebook"
                                style="position: absolute; left: 12px; top: 12px; color: #1877f2;"></i>
                            <input type="text" name="facebook" value="<?= htmlspecialchars($c['facebook'] ?? '') ?>"
                                placeholder="Facebook URL" style="padding-left: 35px;">
                        </div>
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fa-brands fa-instagram"
                                style="position: absolute; left: 12px; top: 12px; color: #e4405f;"></i>
                            <input type="text" name="instagram" value="<?= htmlspecialchars($c['instagram'] ?? '') ?>"
                                placeholder="Instagram URL" style="padding-left: 35px;">
                        </div>
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fa-brands fa-x-twitter"
                                style="position: absolute; left: 12px; top: 12px; color: #000;"></i>
                            <input type="text" name="twitter" value="<?= htmlspecialchars($c['twitter'] ?? '') ?>"
                                placeholder="X (Twitter) URL" style="padding-left: 35px;">
                        </div>
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fa-brands fa-tiktok"
                                style="position: absolute; left: 12px; top: 12px; color: #000;"></i>
                            <input type="text" name="tiktok" value="<?= htmlspecialchars($c['tiktok'] ?? '') ?>"
                                placeholder="TikTok URL" style="padding-left: 35px;">
                        </div>
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fa-brands fa-telegram"
                                style="position: absolute; left: 12px; top: 12px; color: #0088cc;"></i>
                            <input type="text" name="telegram" value="<?= htmlspecialchars($c['telegram'] ?? '') ?>"
                                placeholder="Telegram Link" style="padding-left: 35px;">
                        </div>
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fa-brands fa-whatsapp"
                                style="position: absolute; left: 12px; top: 12px; color: #25d366;"></i>
                            <input type="text" name="whatsapp" value="<?= htmlspecialchars($c['whatsapp'] ?? '') ?>"
                                placeholder="WhatsApp Link" style="padding-left: 35px;">
                        </div>
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fa-brands fa-linkedin"
                                style="position: absolute; left: 12px; top: 12px; color: #0077b5;"></i>
                            <input type="text" name="linkedin" value="<?= htmlspecialchars($c['linkedin'] ?? '') ?>"
                                placeholder="LinkedIn URL" style="padding-left: 35px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Hero Section (Main Header) -->
        <div class="card" style="border-radius: 16px; border-left: 6px solid #dfb180; margin-bottom: 30px;">
            <div class="card-header"><span class="card-title"><i class="fa-solid fa-star"></i> Hero Section (Main
                    Header)</span></div>
            <div style="padding: 25px;">
                <div style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap;">
                    <div style="flex: 1.5; min-width: 300px;">
                        <label style="font-size:12px; font-weight:600; color:#666;">Hero Main Title (Large
                            Tagline)</label>
                        <input type="text" name="hero_title" value="<?= htmlspecialchars($c['hero_title'] ?? '') ?>"
                            placeholder="e.g. Authentic Ethiopian Culinary Experience">
                        <label style="font-size:12px; font-weight:600; color:#666; margin-top:15px; display:block;">Hero
                            Subtitle / Description</label>
                        <textarea name="hero_subtitle"
                            rows="3"><?= htmlspecialchars($c['hero_subtitle'] ?? '') ?></textarea>
                        <div style="margin-top: 15px;">
                            <label style="font-size:12px; font-weight:600; color:#666;">Call-to-Action Button
                                Text</label>
                            <input type="text" name="hero_button_text"
                                value="<?= htmlspecialchars($c['hero_button_text'] ?? '') ?>"
                                placeholder="e.g. View Our Menu">
                        </div>
                    </div>
                    <div
                        style="flex: 1; text-align: center; background: #f8f9fa; padding: 20px; border-radius: 12px; border: 2px dashed #ddd;">
                        <label
                            style="font-size:11px; font-weight:700; color:#dfb180; display:block; margin-bottom:10px; text-transform: uppercase;">Hero
                            Background Image</label>
                        <div
                            style="width: 100%; height: 160px; background: #eee; border-radius: 8px; overflow: hidden; margin-bottom: 12px; box-shadow: 0 5px 10px rgba(0,0,0,0.1);">
                            <img id="heroPreview"
                                src="<?= htmlspecialchars($c['hero_image'] ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=400') ?>"
                                style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <input type="file" name="hero_photo" onchange="previewImage(this, 'heroPreview')"
                            style="font-size: 11px;">
                        <input type="hidden" name="existing_hero_image"
                            value="<?= htmlspecialchars($c['hero_image'] ?? '') ?>">
                        <p style="font-size: 10px; color: #999; margin-top: 8px;">Recommended: 1920 x 1080 px</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4b. Hero Video Configuration -->
        <div class="card" style="border-radius: 16px; border-left: 6px solid #dfb180; margin-bottom: 30px;">
            <div class="card-header"
                style="background: linear-gradient(135deg, #1a1512, #2a2018); padding: 15px 25px; border-bottom: 1px solid #dfb18022;">
                <span class="card-title"
                    style="font-size: 18px; color: #dfb180; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-video"></i> Hero Video Configuration
                </span>
            </div>
            <div style="padding: 25px;">
                <div style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap;">
                    <div style="flex: 1.5; min-width: 300px;">
                        <label style="font-size:12px; font-weight:600; color:#666;">Video Source / URL</label>
                        <input type="text" name="hero_video_url" value="<?= htmlspecialchars($c['hero_video'] ?? '') ?>"
                            placeholder="e.g. assets/videos/home-video.mp4 or YouTube Link">
                        <p style="font-size: 11px; color: #999; margin-top: 5px;">This will be used for the Home Video
                            variation. You can use a local path or external link.</p>
                    </div>
                    <div
                        style="flex: 1; text-align: center; background: #fdfaf7; padding: 20px; border-radius: 12px; border: 2px dashed #dfb18066;">
                        <label
                            style="font-size:11px; font-weight:700; color:#dfb180; display:block; margin-bottom:10px; text-transform: uppercase;">Upload
                            Home Video</label>
                        <i class="fa-solid fa-cloud-arrow-up"
                            style="font-size: 24px; color: #dfb180; margin-bottom: 10px; display: block;"></i>
                        <input type="file" name="hero_video_photo" accept="video/mp4,video/webm"
                            style="font-size: 11px; width: 100%;">
                        <input type="hidden" name="existing_hero_video"
                            value="<?= htmlspecialchars($c['hero_video'] ?? '') ?>">
                        <p style="font-size: 9px; color: #999; margin-top: 5px;">Max size recommended: 20MB</p>
                    </div>
                    <div
                        style="flex: 1; text-align: center; background: #fdfaf7; padding: 20px; border-radius: 12px; border: 2px dashed #dfb18066;">
                        <label
                            style="font-size:11px; font-weight:700; color:#dfb180; display:block; margin-bottom:10px; text-transform: uppercase;">Upload
                            Video Audio</label>
                        <i class="fa-solid fa-cloud-arrow-up"
                            style="font-size: 24px; color: #dfb180; margin-bottom: 10px; display: block;"></i>
                        <input type="file" name="hero_audio_photo" accept="audio/mp3,audio/wav,audio/ogg"
                            style="font-size: 11px; width: 100%;">
                        <input type="hidden" name="existing_hero_audio"
                            value="<?= htmlspecialchars($c['hero_audio'] ?? '') ?>">
                        <p style="font-size: 9px; color: #999; margin-top: 5px;">Optional Background Audio</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4c. Hero Carousel (Additional Slides) -->
        <div class="card" style="border-radius: 16px; border-left: 6px solid #dfb180; margin-bottom: 30px;">
            <div class="card-header"
                style="background: linear-gradient(135deg, #1a1512, #2a2018); padding: 15px 25px; border-bottom: 1px solid #dfb18022;">
                <span class="card-title"
                    style="font-size: 18px; color: #dfb180; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-images"></i> Hero Carousel (Additional Slides)
                </span>
            </div>
            <div style="padding: 25px;">
                <!-- Slide 2 -->
                <div style="margin-bottom: 30px; padding-bottom: 25px; border-bottom: 1px solid #eee;">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h5
                            style="color: #dfb180; margin:0; font-weight: 700; text-transform: uppercase; font-size: 13px;">
                            Carousel Slide #2</h5>
                        <span
                            style="font-size: 10px; background: rgba(223,177,128,0.1); color: #dfb180; padding: 4px 10px; border-radius: 20px;">Secondary
                            Slide</span>
                    </div>
                    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 250px;">
                            <div class="form-row">
                                <div>
                                    <label style="font-size:12px; font-weight:600; color:#666;">Slide 2 Title</label>
                                    <input type="text" name="hero2_title"
                                        value="<?= htmlspecialchars($c['hero2_title'] ?? '') ?>"
                                        placeholder="e.g. Exquisite Fine Dining">
                                </div>
                                <div>
                                    <label style="font-size:12px; font-weight:600; color:#666;">Button Text</label>
                                    <input type="text" name="hero2_button_text"
                                        value="<?= htmlspecialchars($c['hero2_button_text'] ?? '') ?>"
                                        placeholder="e.g. Book a Table">
                                </div>
                            </div>
                            <label
                                style="font-size:12px; font-weight:600; color:#666; margin-top:10px; display:block;">Slide
                                2 Subtitle</label>
                            <textarea name="hero2_subtitle" rows="2"
                                placeholder="Brief description for the second slide..."><?= htmlspecialchars($c['hero2_subtitle'] ?? '') ?></textarea>
                        </div>
                        <div
                            style="width: 180px; text-align: center; background: #fafafa; padding: 15px; border-radius: 12px; border: 1px solid #eee;">
                            <img id="hero2Preview"
                                src="<?= htmlspecialchars($c['hero2_image'] ?: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=400') ?>"
                                style="width:100%; height:90px; object-fit:cover; border-radius:8px; border:1px solid #ddd; margin-bottom:12px;">
                            <input type="file" name="hero2_photo" onchange="previewImage(this, 'hero2Preview')"
                                style="font-size: 10px; width: 100%;">
                            <input type="hidden" name="existing_hero2_image"
                                value="<?= htmlspecialchars($c['hero2_image'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div>
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h5
                            style="color: #dfb180; margin:0; font-weight: 700; text-transform: uppercase; font-size: 13px;">
                            Carousel Slide #3</h5>
                        <span
                            style="font-size: 10px; background: rgba(223,177,128,0.1); color: #dfb180; padding: 4px 10px; border-radius: 20px;">Third
                            Slide</span>
                    </div>
                    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 250px;">
                            <div class="form-row">
                                <div>
                                    <label style="font-size:12px; font-weight:600; color:#666;">Slide 3 Title</label>
                                    <input type="text" name="hero3_title"
                                        value="<?= htmlspecialchars($c['hero3_title'] ?? '') ?>"
                                        placeholder="e.g. Authentic Flavors">
                                </div>
                                <div>
                                    <label style="font-size:12px; font-weight:600; color:#666;">Button Text</label>
                                    <input type="text" name="hero3_button_text"
                                        value="<?= htmlspecialchars($c['hero3_button_text'] ?? '') ?>"
                                        placeholder="e.g. Our Specialities">
                                </div>
                            </div>
                            <label
                                style="font-size:12px; font-weight:600; color:#666; margin-top:10px; display:block;">Slide
                                3 Subtitle</label>
                            <textarea name="hero3_subtitle" rows="2"
                                placeholder="Brief description for the third slide..."><?= htmlspecialchars($c['hero3_subtitle'] ?? '') ?></textarea>
                        </div>
                        <div
                            style="width: 180px; text-align: center; background: #fafafa; padding: 15px; border-radius: 12px; border: 1px solid #eee;">
                            <img id="hero3Preview"
                                src="<?= htmlspecialchars($c['hero3_image'] ?: 'https://images.unsplash.com/photo-1544148103-0773bb108726?q=80&w=400') ?>"
                                style="width:100%; height:90px; object-fit:cover; border-radius:8px; border:1px solid #ddd; margin-bottom:12px;">
                            <input type="file" name="hero3_photo" onchange="previewImage(this, 'hero3Preview')"
                                style="font-size: 10px; width: 100%;">
                            <input type="hidden" name="existing_hero3_image"
                                value="<?= htmlspecialchars($c['hero3_image'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Our History Content -->
        <div class="card" style="border-radius: 16px; margin-bottom: 30px;">
            <div class="card-header"><span class="card-title"><i class="fa-solid fa-book-open"></i> Our History
                    Content</span></div>
            <div style="padding: 25px;">
                <label style="font-size:12px; font-weight:600; color:#666;">History Section Title</label>
                <input type="text" name="history_title" value="<?= htmlspecialchars($c['history_title'] ?? '') ?>"
                    placeholder="e.g. Journey of Bloom Africa">
                <div class="form-row" style="margin-top: 20px;">
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#666;">History Paragraph 1</label>
                        <textarea name="history_text1" rows="6"
                            style="line-height: 1.6;"><?= htmlspecialchars($c['history_text1'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:600; color:#666;">History Paragraph 2</label>
                        <textarea name="history_text2" rows="6"
                            style="line-height: 1.6;"><?= htmlspecialchars($c['history_text2'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Developer & Footer Credits -->
        <div class="card" style="border-radius: 16px; border-bottom: 5px solid #1e293b; margin-bottom: 30px;">
            <div class="card-header" style="background: #1e293b;"><span class="card-title" style="color: #fff;"><i
                        class="fa-solid fa-code"></i> Developer & Footer Credits</span></div>
            <div style="padding: 25px;">
                <div style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap;">
                    <div style="flex: 2; min-width: 300px;">
                        <div class="form-row">
                            <div>
                                <label style="font-size:12px; font-weight:600; color:#666;">Developer Name</label>
                                <input type="text" name="dev_name" value="<?= htmlspecialchars($c['dev_name'] ?? '') ?>"
                                    placeholder="e.g. Mequannent Masresha">
                            </div>
                            <div>
                                <label style="font-size:12px; font-weight:600; color:#666;">Copyright Text</label>
                                <input type="text" name="copyright_text"
                                    value="<?= htmlspecialchars($c['copyright_text'] ?? '') ?>"
                                    placeholder="e.g. All Rights Reserved">
                            </div>
                        </div>
                        <div class="form-row" style="margin-top:15px;">
                            <div>
                                <label style="font-size:11px; font-weight:600; color:#888;">Developer Contact
                                    Email</label>
                                <input type="email" name="dev_email"
                                    value="<?= htmlspecialchars($c['dev_email'] ?? '') ?>">
                            </div>
                            <div>
                                <label style="font-size:11px; font-weight:600; color:#888;">Developer Phone
                                    Number</label>
                                <input type="text" name="dev_phone"
                                    value="<?= htmlspecialchars($c['dev_phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div style="flex: 1; text-align: center; background: #f1f5f9; padding: 20px; border-radius: 12px;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 12px; text-transform: uppercase;">Dev
                            Profile Photo</label>
                        <div
                            style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; margin: 0 auto 15px; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            <img id="devPreview"
                                src="<?= htmlspecialchars($c['dev_photo'] ?: 'https://ui-avatars.com/api/?name=Dev') ?>"
                                style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <input type="file" name="dev_photo" onchange="previewImage(this, 'devPreview')"
                            style="font-size: 11px; width: 100%;">
                        <input type="hidden" name="existing_dev_photo"
                            value="<?= htmlspecialchars($c['dev_photo'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Save Button -->
        <button type="submit" class="btn btn-primary"
            style="margin: 0 auto; display: block; border-radius: 12px; padding: 18px 80px; font-size: 18px; font-weight: 700; box-shadow: 0 8px 25px rgba(223,177,128,0.4); text-transform: uppercase; letter-spacing: 1px;">
            <i class="fa-solid fa-cloud-arrow-up" style="margin-right: 10px;"></i> Update Global Portal Configuration
        </button>
    </form>
</div>