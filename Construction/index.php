<?php
require_once '../db.php';

// Fetch Construction Info
$c = $pdo->query("SELECT * FROM construction_info WHERE id=1")->fetch();

// Fetch Projects
$projects = $pdo->query("SELECT * FROM construction_projects ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Fetch Features (Highlights)
$features = $pdo->query("SELECT * FROM construction_features ORDER BY id ASC LIMIT 4")->fetchAll();

// Fetch Services
$services = $pdo->query("SELECT * FROM construction_services ORDER BY id ASC LIMIT 3")->fetchAll();

// Fetch Testimonials
$testimonials = $pdo->query("SELECT * FROM construction_testimonials WHERE status='Active' ORDER BY id DESC LIMIT 6")->fetchAll();

// Fetch Equipment
$equipment = $pdo->query("SELECT * FROM construction_equipment ORDER BY id DESC")->fetchAll();

// Handle Quote Submission
$form_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quote'])) {
    $stmt = $pdo->prepare("INSERT INTO construction_quotes (client_name, email, phone, project_type, budget, message, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $data = [
        $_POST['name'],
        $_POST['email'],
        $_POST['phone'] ?? '+251',
        $_POST['project_type'] ?? 'General',
        $_POST['budget'] ?? 'Not Specified',
        $_POST['message']
    ];

    if ($stmt->execute($data)) {
        header("Location: index.php?quote_success=1#sec-5");
        exit;
    } else {
        $form_msg = "Sorry, there was an error submitting your request. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/animate.compat.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="../uploads/gallery/BLOOM.jpg?v=1.0" type="image/jpeg">
    <link rel="shortcut icon" href="../uploads/gallery/BLOOM.jpg?v=1.0" type="image/jpeg">
    <script defer src="js/scripts.js"></script>
    <title>
        <?= htmlspecialchars($c['company_name'] ?? 'Bloom Construction') ?> | Engineering Excellence
    </title>
    <style>
        /* Equipment Section Styles */
        #sec-equipment {
            background-color: #fff;
            padding: 80px 0;
            text-align: center;
        }

        #sec-equipment h1 {
            font-size: 2.5rem;
            color: #1a1c27;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 50px;
            letter-spacing: 2px;
        }

        .equipment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 400px));
            gap: 40px;
            justify-content: center;
        }

        .equipment-card {
            background: #f8fafc;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            transition: 0.4s ease;
            border: 1px solid #e2e8f0;
            width: 100%;
        }

        .equipment-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: #f39c12;
        }

        .equip-img-box {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .equip-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.6s;
        }

        .equipment-card:hover .equip-img-box img {
            transform: scale(1.1);
        }

        .equip-status {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status-available {
            background: #dcfce7;
            color: #15803d;
        }

        .status-in-use {
            background: #fef9c3;
            color: #a16207;
        }

        .status-maintenance {
            background: #fee2e2;
            color: #b91c1c;
        }

        .equip-info {
            padding: 20px;
            text-align: left;
        }

        .equip-info h3 {
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .equip-sn {
            display: block;
            font-size: 11px;
            color: #64748b;
            margin-bottom: 12px;
            font-family: monospace;
        }

        .equip-info p {
            font-size: 0.9rem;
            color: #475569;
            line-height: 1.5;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body>
    <?php if (isset($_GET['quote_success'])): ?>
        <div id="quoteSuccessMessage"
            style="position: fixed; top: 100px; left: 50%; transform: translateX(-50%); z-index: 10000; background: #10b981; color: #fff; padding: 15px 40px; border-radius: 50px; box-shadow: 0 10px 30px rgba(16,185,129,0.4); display: flex; align-items: center; gap: 15px; font-weight: 700; animation: bounceInDown 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);">
            <i class="fa-solid fa-circle-check" style="font-size: 24px;"></i>
            <span>Quote Request Sent Successfully!</span>
        </div>
        <script>
            setTimeout(() => {
                const msg = document.getElementById('quoteSuccessMessage');
                msg.style.transition = 'all 0.6s ease';
                msg.style.opacity = '0';
                msg.style.transform = 'translateX(-50%) translateY(-20px)';
                setTimeout(() => msg.remove(), 600);
            }, 5000);
        </script>
    <?php endif; ?>

    <section id="sec-0"
        style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), url('<?= !empty($c['hero_image']) ? '../' . $c['hero_image'] : 'https://images.unsplash.com/photo-1541888946425-d81bb19480c5?q=80&w=2070' ?>'); background-size: cover; background-position: center;">
        <header class="top">
            <div class="container">
                <p>
                    <?= htmlspecialchars($c['phone'] ?? '+251 911 222 333') ?>
                </p>
                <p>
                    <?= htmlspecialchars($c['email'] ?? 'info@bloomconstruction.et') ?>
                </p>
                <p>Mon-Sat 9:00-19:00</p>
                <div class="social">
                    <?php if (!empty($c['facebook'])): ?><a href="<?= $c['facebook'] ?>" title="Facebook"><i
                                class="fa-brands fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($c['twitter'])): ?><a href="<?= $c['twitter'] ?>"><i
                                class="fa-brands fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($c['linkedin'])): ?><a href="<?= $c['linkedin'] ?>"><i
                                class="fa-brands fa-linkedin-in"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($c['youtube'])): ?><a href="<?= $c['youtube'] ?>"><i
                                class="fa-brands fa-youtube"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($c['instagram'])): ?><a href="<?= $c['instagram'] ?>"><i
                                class="fa-brands fa-instagram"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        <header>
            <div class="hide" id="searchBar">
                <input type="search" name="searchBox" id="searchBox"
                    placeholder="Type your search here..    Press [Esc] to exist">
            </div>
            <div class="container">
                <h1>
                    <span>
                        <?= substr($c['company_name'] ?? 'CONST', 0, 5) ?>
                    </span>
                    <?= substr($c['company_name'] ?? 'RUCTION', 5) ?>
                    <p>
                        <?= htmlspecialchars($c['why_choose_us_msg'] ?? 'Quality and Excellence in every build.') ?>
                    </p>
                </h1>
                <label for="menu"><i class="fas fa-bars"></i></label>
                <input type="checkbox" id="menu">
                <nav>
                    <a href="index.php">Home</a>
                    <div class="nav-dropdown" id="navOMEDropdown">
                        <button class="nav-dropdown-btn" onclick="toggleDropdown(event, 'navOMEDropdown')">
                            <i class="fa-solid fa-shapes nd-icon"></i> OME PAGE
                            <i class="fa-solid fa-chevron-down nd-chevron"></i>
                        </button>
                        <div class="nav-dropdown-panel">
                            <div class="nd-panel-header">Select View</div>
                            <a href="index.php">
                                <div class="nd-item-icon"><i class="fa-solid fa-images"></i></div> Home Carousel
                            </a>
                            <a href="index-image.php">
                                <div class="nd-item-icon"><i class="fa-solid fa-image"></i></div> Home Image
                            </a>
                            <a href="index-video.php">
                                <div class="nd-item-icon"><i class="fa-solid fa-video"></i></div> Home Video
                            </a>
                            <div style="border-top: 1px solid rgba(255,255,255,0.04); margin-top: 5px;"></div>
                            <a href="../restaurant_home.php" style="color: #f39c12 !important;">
                                <div class="nd-item-icon" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;">
                                    <i class="fa-solid fa-utensils"></i>
                                </div> Bloom Restaurant
                            </a>
                        </div>
                    </div>
                    <?php if (!empty($c['blog_url'])): ?><a href="<?= $c['blog_url'] ?>">Blog</a>
                    <?php endif; ?>
                    <?php if (!empty($c['portfolio_url'])): ?><a href="<?= $c['portfolio_url'] ?>">Portfolio</a>
                    <?php endif; ?>
                    <a href="#sec-3">Services</a>
                    <a href="#sec-5">Contact</a>
                    <i class="fa-solid fa-magnifying-glass" id="search"></i>
                </nav>
            </div>
        </header>
        <!-- Dropdown Scripts & Styles for Construction Side -->
        <style>
            /* ── Nav Dropdown – Click-toggle accordion ── */
            .nav-dropdown {
                position: relative;
                display: inline-block;
                margin-right: 15px;
            }

            .nav-dropdown-btn {
                display: flex;
                align-items: center;
                gap: 7px;
                background: none;
                border: none;
                color: #fff;
                font-family: inherit;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                padding: 6px 4px;
                letter-spacing: 0.3px;
                transition: color 0.2s;
            }

            .nav-dropdown-btn:hover {
                color: #f39c12;
            }

            .nav-dropdown-btn .nd-icon {
                font-size: 14px;
                color: #f39c12;
                opacity: 0.85;
            }

            .nav-dropdown-btn .nd-chevron {
                font-size: 10px;
                margin-left: 2px;
                transition: transform 0.3s ease;
            }

            .nav-dropdown.open .nd-chevron {
                transform: rotate(180deg);
            }

            .nav-dropdown-panel {
                display: none;
                position: absolute;
                top: calc(100% + 12px);
                left: 50%;
                transform: translateX(-50%);
                background: #1a1512;
                border: 1px solid rgba(243, 156, 18, 0.4);
                border-radius: 14px;
                min-width: 230px;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
                z-index: 9999;
                overflow: hidden;
                animation: ndFadeIn 0.2s ease;
            }

            .nav-dropdown.open .nav-dropdown-panel {
                display: block;
            }

            @keyframes ndFadeIn {
                from {
                    opacity: 0;
                    transform: translateX(-50%) translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }
            }

            .nav-dropdown-panel::before {
                content: '';
                position: absolute;
                top: -7px;
                left: 50%;
                transform: translateX(-50%) rotate(45deg);
                width: 13px;
                height: 13px;
                background: #1a1512;
                border-left: 1px solid rgba(243, 156, 18, 0.4);
                border-top: 1px solid rgba(243, 156, 18, 0.4);
            }

            .nd-panel-header {
                padding: 14px 18px 10px;
                font-size: 10px;
                font-weight: 800;
                color: #f39c12;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                border-bottom: 1px solid rgba(243, 156, 18, 0.2);
            }

            .nav-dropdown-panel a {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 13px 18px;
                color: rgba(255, 255, 255, 0.85) !important;
                text-decoration: none;
                font-size: 14px;
                font-weight: 500;
                transition: background 0.18s, color 0.18s, padding-left 0.18s;
                border-bottom: 1px solid rgba(255, 255, 255, 0.04);
                margin: 0;
            }

            .nav-dropdown-panel a:last-child {
                border-bottom: none;
            }

            .nav-dropdown-panel a .nd-item-icon {
                width: 30px;
                height: 30px;
                background: rgba(243, 156, 18, 0.1);
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                color: #f39c12;
                flex-shrink: 0;
                transition: background 0.18s;
            }

            .nav-dropdown-panel a:hover {
                background: rgba(243, 156, 18, 0.08);
                color: #f39c12 !important;
                padding-left: 22px;
            }

            .nav-dropdown-panel a:hover .nd-item-icon {
                background: rgba(243, 156, 18, 0.2);
            }
        </style>
        <script>
            function toggleDropdown(event, id) {
                event.preventDefault();
                event.stopPropagation();
                // Close others
                document.querySelectorAll('.nav-dropdown').forEach(d => {
                    if (d.id !== id) d.classList.remove('open');
                });
                document.getElementById(id).classList.toggle('open');
            }
            // Close when clicking outside
            document.addEventListener('click', function (event) {
                if (!event.target.closest('.nav-dropdown')) {
                    document.querySelectorAll('.nav-dropdown').forEach(d => d.classList.remove('open'));
                }
            });
        </script>
        <article>
            <div class="container">
                <h1><?= htmlspecialchars($c['hero_title'] ?? 'We are Certified Engineers') ?></h1>
                <h2><?= !empty($c['hero_subtitle']) ? $c['hero_subtitle'] : 'Construction services<br><span>Creative AND professional</span>' ?>
                </h2>
                <p>
                    <?= htmlspecialchars($c['hero_description'] ?? $c['services_desc'] ?? 'Leading construction services in Ethiopia. We deliver quality and professional results on time.') ?>
                </p>
                <a href="#sec-1">Show More</a>
                <a href="#sec-4">view project</a>
            </div>
        </article>
    </section>
    <section id="sec-1" style="background-color: #fdf7f0; padding: 100px 0; overflow: hidden;">
        <div class="container" style="display: flex; align-items: center; gap: 80px; flex-wrap: wrap;">
            <article style="flex: 1.2; min-width: 350px; text-align: left; padding: 0;">
                <h1
                    style="font-size: 2.8rem; color: #c8832a; font-weight: 800; text-transform: uppercase; font-family: 'Playfair Display', serif; margin-bottom: 20px; line-height: 1.2;">
                    Welcome to our company</h1>
                <h2 style="font-size: 1.5rem; font-weight: 600; color: #1a1a2e; margin-bottom: 25px;">Building your
                    vision with precision.</h2>
                <div style="width: 60px; height: 4px; background: #c8832a; margin-bottom: 30px; border-radius: 2px;">
                </div>
                <p
                    style="font-size: 1.1rem; color: #4a4a68; text-align: justify; margin-bottom: 40px; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($c['about_text'] ?? "At " . ($c['company_name'] ?? 'Bloom Construction') . ", we bring years of expertise to the Ethiopian construction landscape. We specialize in high-quality architectural design, renovation, and complete construction management. \n\nOur team is dedicated to delivering excellence, ensuring that every project we undertake meets the highest standards of safety and aesthetic appeal.")) ?>
                </p>
                <a href="<?= $c['portfolio_url'] ?? '#' ?>"
                    style="color: #fff; background: #c8832a; text-transform: uppercase; text-decoration: none; padding: 15px 40px; border-radius: 50px; display: inline-block; font-weight: 700; transition: all 0.3s ease; letter-spacing: 1.5px; box-shadow: 0 10px 20px rgba(200, 131, 42, 0.2);">Our
                    Portfolio</a>
            </article>
            <aside
                style="flex: 1; min-width: 350px; display: flex; justify-content: center; align-items: center; position: relative;">
                <div
                    style="position: absolute; width: 110%; height: 110%; background: radial-gradient(circle, rgba(200, 131, 42, 0.08) 0%, rgba(200, 131, 42, 0) 70%); z-index: 0;">
                </div>
                <img src="<?= !empty($c['hero_image']) ? '../' . $c['hero_image'] : 'Images/img1.png' ?>"
                    alt="company image"
                    style="width: 100%; max-width: 480px; aspect-ratio: 1/1; object-fit: cover; border-radius: 50%; border: 12px solid #ffffff; box-shadow: 0 25px 50px rgba(0,0,0,0.15); transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1; position: relative;"
                    onmouseover="this.style.transform='scale(1.03) translateY(-10px)'; this.style.boxShadow='0 35px 70px rgba(0,0,0,0.2)';"
                    onmouseout="this.style.transform='scale(1) translateY(0)'; this.style.boxShadow='0 25px 50px rgba(0,0,0,0.15)';"
                    class="wow animated zoomIn">
            </aside>
        </div>
    </section>
    <section id="sec-2">
        <div class="container">
            <h1 class="wow animated fadeInUp"><?= htmlspecialchars($c['why_choose_us_title'] ?? 'WHY CHOOSE US?') ?>
            </h1>
            <P class="wow animated fadeInUp" data-wow-delay="0.2s">
                <?= htmlspecialchars($c['why_choose_us_subtitle'] ?? $c['why_choose_us_msg'] ?? 'Quality and Excellence in every build. We are passionate about what we do.') ?>
            </P>
            <article>
                <?php $delay = 0.1;
                foreach ($features as $f): ?>
                    <figure class="wow animated zoomIn" data-wow-delay="<?= $delay ?>s">
                        <div>
                            <i class="<?= htmlspecialchars($f['icon_class'] ?? 'fa-solid fa-hard-hat') ?>"
                                style="font-size: 50px; color: #333; display: flex; align-items: center; justify-content: center; width: 100px; height: 100px; background: #f39c12; border-radius: 50%; border: 5px solid #fff;"></i>
                        </div>
                        <div class="cont">
                            <h2><?= htmlspecialchars($f['title']) ?></h2>
                            <p><?= htmlspecialchars($f['description']) ?></p>
                        </div>
                    </figure>
                    <?php $delay += 0.2; endforeach; ?>
                <?php if (empty($features)): ?>
                    <figure class="wow animated zoomIn">
                        <div><img src="Images/ico1.png" alt=""></div>
                        <div class="cont">
                            <h2>we deliver quality</h2>
                            <p>We use premium materials and skilled labor to ensure your building stands the test of time.
                            </p>
                        </div>
                    </figure>
                    <figure class="wow animated zoomIn" data-wow-delay="0.2s">
                        <div><img src="Images/ico2.png" alt=""></div>
                        <div class="cont">
                            <h2>Always on time</h2>
                            <p>Strict project management keeps us on schedule, every time.</p>
                        </div>
                    </figure>
                <?php endif; ?>
            </article>
        </div>
    </section>
    <section id="sec-3">
        <div class="container">
            <h1 class="wow animated fadeInUp"><?= htmlspecialchars($c['services_title'] ?? 'OUR SERVICES') ?></h1>
            <p class="wow animated fadeInUp" data-wow-delay="0.2s"
                style="text-align: center; color: #666; max-width: 800px; margin: 0 auto 50px;">
                <?= htmlspecialchars($c['services_subtitle'] ?? $c['services_desc'] ?? 'Comprehensive solutions for every project.') ?>
            </p>
            <div class="service-grid">
                <?php $delay = 0.1;
                foreach ($services as $s): ?>
                    <div class="service-card wow animated fadeInUp" data-wow-delay="<?= $delay ?>s">
                        <div class="service-inner">
                            <div class="service-img-container">
                                <img src="<?= !empty($s['image_url']) ? '../' . htmlspecialchars($s['image_url']) : 'Images/card1.jpg' ?>"
                                    alt="<?= htmlspecialchars($s['title']) ?>">
                                <div class="service-overlay">
                                    <h4><?= htmlspecialchars($s['title']) ?></h4>
                                </div>
                            </div>
                            <div class="service-info">
                                <p><?= htmlspecialchars($s['description']) ?></p>
                                <a href="<?= !empty($s['button_url']) ? htmlspecialchars($s['button_url']) : '#' ?>"
                                    class="service-btn">
                                    <?= !empty($s['button_text']) ? htmlspecialchars($s['button_text']) : 'Learn More' ?> <i
                                        class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php $delay += 0.2; endforeach; ?>
                <?php if (empty($services)): ?>
                    <div class="service-card wow animated fadeInUp">
                        <div class="service-inner">
                            <div class="service-img-container">
                                <img src="Images/card1.jpg" alt="Construction Management">
                                <div class="service-overlay">
                                    <h4>Construction Management</h4>
                                </div>
                            </div>
                            <div class="service-info">
                                <p>Full lifecycle management of your construction project, ensuring site safety, resource
                                    efficiency, and regulatory compliance from start to finish.</p>
                                <a href="#" class="service-btn">Learn More <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="service-card wow animated fadeInUp" data-wow-delay="0.2s">
                        <div class="service-inner">
                            <div class="service-img-container">
                                <img src="Images/card2.jpg" alt="Renovation">
                                <div class="service-overlay">
                                    <h4>Renovation</h4>
                                </div>
                            </div>
                            <div class="service-info">
                                <p>Modernizing existing structures with the latest materials and designs, breathing new life
                                    into your residential or commercial space.</p>
                                <a href="#" class="service-btn">Learn More <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="service-card wow animated fadeInUp" data-wow-delay="0.4s">
                        <div class="service-inner">
                            <div class="service-img-container">
                                <img src="Images/card3.jpg" alt="Interior Design">
                                <div class="service-overlay">
                                    <h4>Interior Design</h4>
                                </div>
                            </div>
                            <div class="service-info">
                                <p>Creating functional and aesthetically pleasing interior spaces tailored to your personal
                                    style and operational needs.</p>
                                <a href="#" class="service-btn">Learn More <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <section id="sec-4">
        <div class="container">
            <h1 class="wow animated fadeInUp"><?= htmlspecialchars($c['projects_title'] ?? 'OUR LATEST PROJECTS') ?>
            </h1>
            <p class="wow animated fadeInUp" data-wow-delay="0.2s" style="margin-bottom: 50px; opacity: 0.8;">
                <?= htmlspecialchars($c['projects_subtitle'] ?? 'Explore some of our most recent and proudest achievements in engineering excellence.') ?>
            </p>

            <div class="project-grid">
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $p): ?>
                        <div class="project-card wow animated zoomIn" data-wow-delay="0.1s">
                            <div class="project-inner">
                                <div class="project-img-container">
                                    <img src="<?= !empty($p['image_url']) ? '../' . htmlspecialchars($p['image_url']) : 'Images/gallery1.jpg' ?>"
                                        alt="<?= htmlspecialchars($p['title']) ?>">
                                    <div class="project-badge"><?= htmlspecialchars($p['status'] ?? 'Construction') ?></div>
                                </div>
                                <div class="project-info">
                                    <h3><?= htmlspecialchars($p['title']) ?></h3>
                                    <p><?= htmlspecialchars(substr($p['description'] ?? 'High-quality engineering project delivered with precision and expertise.', 0, 95)) ?>...
                                    </p>
                                    <div class="project-footer">
                                        <span class="project-date"><i class="fa-regular fa-calendar-days"></i>
                                            <?= date('M Y', strtotime($p['created_at'])) ?></span>
                                        <a href="#" class="project-link">Details <i class="fa-solid fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback placeholders -->
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="project-card wow animated fadeInUp">
                            <div class="project-inner">
                                <div class="project-img-container">
                                    <img src="Images/gallery<?= $i ?>.jpg" alt="Project">
                                    <div class="project-badge">Completed</div>
                                </div>
                                <div class="project-info">
                                    <h3>Premium Structure <?= $i ?></h3>
                                    <p>A modern architectural landmark built with sustainable materials and innovative design.
                                    </p>
                                    <div class="project-footer">
                                        <span class="project-date">Jan 2024</span>
                                        <a href="#" class="project-link">View More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Equipment Showcase Section -->
    <?php if (!empty($equipment)): ?>
        <section id="sec-equipment">
            <div class="container">
                <h1 class="wow animated fadeInUp">Machinery & Equipment</h1>
                <div class="equipment-grid">
                    <?php foreach ($equipment as $e):
                        $status_class = 'status-' . strtolower(str_replace(' ', '-', $e['status']));
                        ?>
                        <div class="equipment-card wow animated fadeInUp">
                            <div class="equip-img-box">
                                <?php
                                $img_src = 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?q=80&w=800'; // High-quality default
                                if (!empty($e['image_url'])) {
                                    if (strpos($e['image_url'], 'http') === 0) {
                                        $img_src = $e['image_url'];
                                    } else {
                                        $img_src = '../' . $e['image_url'];
                                    }
                                }
                                ?>
                                <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($e['name']) ?>"
                                    onerror="this.src='https://images.unsplash.com/photo-1581092160607-ee22621dd758?q=80&w=800'">
                                <div class="equip-status <?= $status_class ?>">
                                    <?= htmlspecialchars($e['status']) ?>
                                </div>
                            </div>
                            <div class="equip-info">
                                <h3><?= htmlspecialchars($e['name']) ?></h3>
                                <?php if (!empty($e['serial_number'])): ?>
                                    <span class="equip-sn">ID: <?= htmlspecialchars($e['serial_number']) ?></span>
                                <?php endif; ?>
                                <p><?= htmlspecialchars($e['description']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section id="sec-5">
        <div class="container">
            <h1><?= htmlspecialchars($c['reviews_title'] ?? 'CUSTOMER HIGHLIGHTS') ?></h1>
            <p><?= htmlspecialchars($c['reviews_subtitle'] ?? 'What our clients say about our commitment to excellence.') ?>
            </p>
            <div class="cont"
                style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; justify-items: center;">
                <?php foreach ($testimonials as $t): ?>
                    <article class="quoteblock wow animated fadeInUp"
                        style="margin-bottom: 30px; width: 100%; max-width: 420px;">
                        <div class="quotetxt arrow">
                            <p><img src="Images/blockquote2.png" alt="quote mark"></p>
                            <p>
                                <?= htmlspecialchars($t['message']) ?>
                            </p>
                            <div style="color: #f39c12; font-size: 11px; margin-top: 10px;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa-<?= $i <= $t['rating'] ? 'solid' : 'regular' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <figure style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                            <img class="customer"
                                src="<?= !empty($t['image_url']) ? '../' . htmlspecialchars($t['image_url']) : 'https://ui-avatars.com/api/?name=' . urlencode($t['client_name']) ?>"
                                alt="<?= htmlspecialchars($t['client_name']) ?>"
                                style="width:80px; height:80px; border-radius:50%; object-fit:cover; border: 3px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                            <figcaption style="margin-top: 10px;">
                                <strong
                                    style="display: block; color: #333; font-size: 14px;"><?= htmlspecialchars($t['client_name']) ?></strong>
                                <small
                                    style="color: #777; font-size: 12px;"><?= htmlspecialchars($t['client_role']) ?></small>
                            </figcaption>
                        </figure>
                    </article>
                <?php endforeach; ?>

                <?php if (empty($testimonials)): ?>
                    <article class="quoteblock" style="margin: 0 auto;">
                        <div class="quotetxt arrow">
                            <p><img src="Images/blockquote2.png" alt="quote mark"></p>
                            <p>
                                <?= htmlspecialchars($c['review_text'] ?? 'The team delivered our project ahead of schedule with exceptional attention to detail. Highly recommend for any major construction work in Addis.') ?>
                            </p>
                        </div>
                        <figure>
                            <img class="customer"
                                src="<?= !empty($c['review_image']) ? '../' . $c['review_image'] : 'Images/cust1.png' ?>"
                                alt="customer1" style="width:80px; height:80px; border-radius:50%; object-fit:cover;">
                            <figcaption>Featured Client</figcaption>
                        </figure>
                    </article>
                <?php endif; ?>
            </div>
        </div>
        <div class="contactUs">
            <div class="container">
                <aside>
                    <h1><?= htmlspecialchars($c['quote_title'] ?? 'Request a Professional Quote') ?></h1>
                    <h2><?= htmlspecialchars($c['quote_subtitle'] ?? 'Get an estimate for your project today!') ?></h2>
                    <?php if ($form_msg): ?>
                        <div class="alert alert-danger"
                            style="margin-top:20px; background:#fee2e2; color:#b91c1c; border:none; padding:15px; border-radius:10px;">
                            <?= $form_msg ?>
                        </div>
                    <?php endif; ?>
                </aside>
                <form id="form" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <input type="text" name="name" placeholder="Your Name" required style="margin-bottom: 10px;">
                        <input type="email" name="email" placeholder="Your Email" required style="margin-bottom: 10px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <input type="text" name="phone" placeholder="Phone (e.g. +251 9...)" required
                            style="margin-bottom: 10px;">
                        <select name="budget" required
                            style="width:100%; margin-bottom:10px; padding:12px; border:none; border-radius:5px; background:rgba(255,255,255,0.05); color:#fff; outline:none; height: 45px;">
                            <option value="" disabled selected style="color:#000;">Estimated Budget</option>
                            <option value="Economy" style="color:#000;">Economy (Under 5M ETB)</option>
                            <option value="Premium" style="color:#000;">Premium (5M - 20M ETB)</option>
                            <option value="Luxury" style="color:#000;">Luxury (Over 20M ETB)</option>
                        </select>
                    </div>
                    <select name="project_type"
                        style="width:100%; margin-bottom:15px; padding:12px; border:none; border-radius:5px; background:rgba(255,255,255,0.05); color:#fff; outline:none;">
                        <option value="Residential" style="color:#000;">Residential Building</option>
                        <option value="Commercial" style="color:#000;">Commercial Building</option>
                        <option value="Renovation" style="color:#000;">Renovation</option>
                        <option value="Other" style="color:#000;">Other Service</option>
                    </select>
                    <textarea name="message" placeholder="Describe your project requirements..." required
                        style="width:100%; min-height:100px; margin-bottom:15px; padding:12px; border:none; border-radius:5px; background:rgba(255,255,255,0.05); color:#fff; outline:none;"></textarea>
                    <input type="submit" name="submit_quote" value="Request Quote" id="submit"
                        style="cursor:pointer; background:#fff; color:#000; font-weight:700;">
                </form>
            </div>
        </div>
    </section>
    <section id="sec-6">
        <div class="container">
            <div class="about">
                <h1>About Company</h1>
                <h2>
                    <?= substr($c['company_name'] ?? 'BLOOM', 0, 5) ?>
                    <p>
                        <?= htmlspecialchars($c['why_choose_us_msg'] ?? 'Engineering Excellence.') ?>
                    </p>
                </h2>
                <p>
                    <?= htmlspecialchars($c['services_desc'] ?? 'Building a better Ethiopia through sustainable and innovative engineering solutions.') ?>
                </p>
                <div style="font-size:24px; display:flex; gap:20px;">
                    <?php if (!empty($c['facebook'])): ?><a href="<?= $c['facebook'] ?>"><i
                                class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($c['twitter'])): ?><a href="<?= $c['twitter'] ?>"><i
                                class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($c['linkedin'])): ?><a href="<?= $c['linkedin'] ?>"><i
                                class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($c['google_plus'])): ?><a href="<?= $c['google_plus'] ?>"><i
                                class="fab fa-google-plus-g"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($c['youtube'])): ?><a href="<?= $c['youtube'] ?>"><i
                                class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="links">
                <h1>Explore links</h1>
                <a href="#sec-3">Our services</a>
                <a href="<?= $c['portfolio_url'] ?? '#' ?>">Our Portfolio</a>
                <a href="<?= $c['blog_url'] ?? '#' ?>">Read Blog</a>
                <a href="<?= $c['ome_page_url'] ?? '#' ?>">OME Portal</a>
                <a href="#sec-5">Contact Us</a>
                <a href="#">Privacy Policy</a>
            </div>
            <div class="posts">
                <h1>Latest Projects</h1>
                <?php foreach ($projects as $p): ?>
                    <a href="#"><span>
                            <?= date('d', strtotime($p['created_at'])) ?><br>
                            <?= strtoupper(date('M', strtotime($p['created_at']))) ?>
                        </span>
                        <?= htmlspecialchars($p['title'] ?? 'Project') ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="contact">
                <h1>Contact us</h1>
                <p>
                    <?= htmlspecialchars($c['address'] ?? 'Addis Ababa, Ethiopia') ?>
                </p>
                <p>
                    <?= htmlspecialchars($c['email'] ?? 'info@bloomconstruction.et') ?>
                </p>
                <p>
                    <?= htmlspecialchars($c['phone'] ?? '+251 9... ') ?>
                </p>

            </div>
        </div>
    </section>
    <section id="sec-7">
        <div class="container" style="display:flex; justify-content:space-between; align-items:center;">
            <p>Copyright @
                <?= date('Y') ?> | Designed by <span>Bloom Africa Team</span>
            </p>
            <a href="../admin.php" style="color:rgba(255,255,255,0.3); font-size:11px; text-decoration:none;">Admin
                Login</a>
        </div>
    </section>
    <style>
        /* Chatbot CSS */
        .float-btn-group {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 10000;
        }

        .chat-toggle {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: #f39c12;
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            transition: 0.3s;
            pointer-events: auto;
        }

        .chat-toggle:hover {
            transform: scale(1.1) rotate(10deg);
        }

        .chatbot-container {
            position: fixed;
            bottom: 100px;
            right: 30px;
            z-index: 10001;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            font-family: inherit;
            pointer-events: none;
        }

        .chat-window {
            width: 350px;
            height: 500px;
            background: #212529;
            border-radius: 20px;
            margin-bottom: 20px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(243, 156, 18, 0.4);
            animation: slideUp 0.4s ease;
            pointer-events: auto;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .chat-header {
            background: rgba(243, 156, 18, 0.1);
            padding: 15px 20px;
            border-bottom: 1px solid rgba(243, 156, 18, 0.2);
            color: #f39c12;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .chat-bubble {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 15px;
            font-size: 13.5px;
            line-height: 1.5;
            position: relative;
            animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .chat-bubble.bot {
            background: #333;
            color: rgba(255, 255, 255, 0.9);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }

        .chat-bubble.user {
            background: #f39c12;
            color: #000;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            font-weight: 500;
        }

        .chat-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .chat-option-btn {
            background: rgba(243, 156, 18, 0.15);
            border: 1px solid rgba(243, 156, 18, 0.3);
            color: #f39c12;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .chat-option-btn:hover {
            background: rgba(243, 156, 18, 0.3);
            transform: translateY(-2px);
            color: #fff;
        }

        .chat-input-area {
            padding: 15px;
            background: rgba(255, 255, 255, 0.02);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .chat-input-area input {
            flex: 1;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 12px 15px;
            border-radius: 20px;
            font-family: inherit;
            font-size: 13px;
        }

        .chat-input-area input:focus {
            outline: none;
            border-color: rgba(243, 156, 18, 0.4);
        }

        .chat-send-btn {
            background: #f39c12;
            color: #000;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .chat-send-btn:hover {
            transform: scale(1.1);
            background: #fff;
        }

        .chat-messages::-webkit-scrollbar {
            width: 5px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: rgba(243, 156, 18, 0.3);
            border-radius: 10px;
        }
    </style>

    <div class="chatbot-container">
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-hard-hat"></i>
                    <strong style="font-weight: 700;">Construction Chat</strong>
                </div>
                <i class="fa-solid fa-xmark" style="cursor: pointer;" onclick="toggleChat()"></i>
            </div>
            <div id="chatRegForm" style="padding: 20px; display: none; background: #212529;">
                <p style="font-size: 13px; color: #888; margin-bottom: 15px;">Introduce yourself to discuss your project
                    with our consultants!</p>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <input type="text" id="regName" placeholder="Full Name"
                        style="width: 100%; padding: 10px; background: #333; border: 1px solid #444; border-radius: 8px; color: #fff;">
                    <input type="email" id="regEmail" placeholder="Email Address"
                        style="width: 100%; padding: 10px; background: #333; border: 1px solid #444; border-radius: 8px; color: #fff;">
                    <input type="tel" id="regPhone" placeholder="Phone Number"
                        style="width: 100%; padding: 10px; background: #333; border: 1px solid #444; border-radius: 8px; color: #fff;">
                    <button onclick="registerChat()"
                        style="background: #f39c12; color: #000; padding: 12px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; margin-top: 10px;">Connect
                        With Engineer</button>
                </div>
            </div>
            <div class="chat-messages" id="chatMessages" style="display: none;"></div>
            <div class="chat-input-area" id="chatInputArea" style="display: none;">
                <input type="text" id="chatInput" placeholder="Type a message..."
                    onkeypress="if(event.key === 'Enter') sendChatMessage()">
                <button class="chat-send-btn" onclick="sendChatMessage()"><i
                        class="fa-solid fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
    <script>
        let chatPollInterval = null;
        function registerChat() {
            const name = document.getElementById('regName').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const phone = document.getElementById('regPhone').value.trim();

            if (!name || !email || !phone) return alert('All fields required');
            fetch('../chat_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ register: true, name, email, phone, department: 'Construction' })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showChatInterface();
                        loadChatHistory();
                    }
                });
        }

        function showChatInterface() {
            document.getElementById('chatRegForm').style.display = 'none';
            document.getElementById('chatMessages').style.display = 'flex';
            document.getElementById('chatInputArea').style.display = 'flex';
        }

        function toggleChat() {
            const win = document.getElementById('chatWindow');
            const isOpen = win.style.display === 'flex';
            win.style.display = isOpen ? 'none' : 'flex';

            if (win.style.display === 'flex') {
                checkChatReg();
                chatPollInterval = setInterval(loadChatHistory, 3000);
            } else {
                clearInterval(chatPollInterval);
            }
        }

        function checkChatReg() {
            fetch('../chat_handler.php?check_reg=1')
                .then(res => res.json())
                .then(data => {
                    if (data.registered && data.customer.department === 'Construction') {
                        showChatInterface();
                        loadChatHistory();
                    } else {
                        document.getElementById('chatRegForm').style.display = 'block';
                        document.getElementById('chatMessages').style.display = 'none';
                        document.getElementById('chatInputArea').style.display = 'none';
                    }
                });
        }

        function sendChatMessage() {
            const input = document.getElementById('chatInput');
            const msg = input.value.trim();
            if (!msg) return;
            appendMessage('User', msg);
            input.value = '';
            fetch('../chat_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: msg })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.reply) appendMessage('Bot', data.reply, data.buttons);
                });
        }

        function appendMessage(sender, text, buttons = []) {
            const container = document.getElementById('chatMessages');
            const exists = Array.from(container.children).some(c => c.dataset.text === text && c.dataset.sender === sender && !buttons.length);
            if (exists) return;

            const div = document.createElement('div');
            div.className = `chat-bubble ${sender.toLowerCase()}`;
            div.innerHTML = `<strong>${sender}:</strong> <br> ${text.replace(/\n/g, '<br>')}`;

            if (buttons && buttons.length > 0) {
                const optBox = document.createElement('div');
                optBox.className = 'chat-options';
                buttons.forEach(label => {
                    const btn = document.createElement('button');
                    btn.className = 'chat-option-btn';
                    btn.innerText = label;
                    btn.onclick = () => {
                        document.getElementById('chatInput').value = label;
                        sendChatMessage();
                    };
                    optBox.appendChild(btn);
                });
                div.appendChild(optBox);
            }
            div.dataset.text = text;
            div.dataset.sender = sender;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }

        function loadChatHistory() {
            fetch('../chat_handler.php')
                .then(res => res.json())
                .then(data => {
                    if (data.messages && data.registered && data.customer.department === 'Construction') {
                        data.messages.forEach(m => appendMessage(m.sender, m.message));
                    }
                });
        }
    </script>
    <div class="float-btn-group" id="socialFloatGroup">
        <div class="chat-toggle" onclick="toggleChat()" id="chatToggle" style="margin-bottom: 5px;">
            <i class="fa-solid fa-hard-hat"></i>
        </div>
    </div>

    <script src="js/wow.min.js"></script>
    <script> new WOW().init();</script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>