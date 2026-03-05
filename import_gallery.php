<?php
/**
 * Gallery Auto-Import Script
 * Run this once to import all images from uploads/gallery into the database
 * Access: http://localhost/Bloom Africa Resturant/import_gallery.php
 */
require_once 'db.php';

$gallery_dir = __DIR__ . '/uploads/gallery/';
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Get all existing image_urls from DB to avoid duplicates
$existing = $pdo->query("SELECT image_url FROM gallery")->fetchAll(PDO::FETCH_COLUMN);

$imported = 0;
$skipped = 0;
$files = scandir($gallery_dir);

// Category mapping by filename keywords
function guessCategoryAndTitle($filename)
{
    $name = strtolower($filename);
    $categories = [
        'coffee' => ['Coffee', 'Ethiopian Coffee Ceremony'],
        'kitchen' => ['Kitchen', 'Behind the Scenes Kitchen'],
        'doro' => ['Food', 'Doro Wat – Our Signature Dish'],
        'kitfo' => ['Food', 'Ethiopian Kitfo Platter'],
        'veggie' => ['Food', 'Ethiopian Veggie Platter'],
        'tibs' => ['Food', 'Sizzling Beef Tibs'],
        'beef' => ['Food', 'Sizzling Beef Tibs'],
        'restaurant' => ['Restaurant', 'Restaurant Interior'],
        'ambience' => ['Ambience', 'Restaurant Ambience'],
        'event' => ['Events', 'Special Event'],
        'outdoor' => ['Outdoor', 'Outdoor Dining'],
        'beverage' => ['Beverage', 'Signature Beverages'],
        'drink' => ['Beverage', 'Signature Drinks'],
    ];
    foreach ($categories as $keyword => [$cat, $title]) {
        if (strpos($name, $keyword) !== false) {
            return [$cat, $title];
        }
    }
    // Default: clean up filename
    $clean = preg_replace('/[_\-]+/', ' ', pathinfo($filename, PATHINFO_FILENAME));
    $clean = preg_replace('/\d{10,}/', '', $clean); // remove timestamps
    $clean = trim(ucwords($clean));
    return ['Restaurant', $clean ?: 'Gallery Image'];
}

foreach ($files as $file) {
    if ($file === '.' || $file === '..')
        continue;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext))
        continue;

    $image_url = 'uploads/gallery/' . $file;

    if (in_array($image_url, $existing)) {
        $skipped++;
        continue;
    }

    [$category, $title] = guessCategoryAndTitle($file);
    $description = "A beautiful moment captured at Bloom Africa Restaurant - showcasing our " . strtolower($category) . " experience.";

    $stmt = $pdo->prepare("INSERT INTO gallery (image_url, category, title, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$image_url, $category, $title, $description]);
    $imported++;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gallery Import – Bloom Africa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f0b09;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .card {
            background: #1a1512;
            border: 1px solid #dfb180;
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            max-width: 480px;
            width: 90%;
        }

        .icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 1.8rem;
            color: #dfb180;
            margin-bottom: 10px;
        }

        p {
            color: #aaa;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .stat {
            display: flex;
            justify-content: space-between;
            background: #251d18;
            border-radius: 12px;
            padding: 15px 20px;
            margin: 20px 0 8px;
        }

        .stat span {
            font-weight: 800;
            color: #dfb180;
            font-size: 1.4rem;
        }

        .stat label {
            color: #888;
            font-size: 0.85rem;
            margin-top: 4px;
            display: block;
        }

        .btn {
            display: inline-block;
            margin-top: 25px;
            background: #dfb180;
            color: #000;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn:hover {
            background: #fff;
        }

        .success {
            color: #10b981;
        }

        .skip {
            color: #f59e0b;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">🖼️</div>
        <h1>Gallery Import Complete!</h1>
        <p>All images from your <code>uploads/gallery</code> directory have been processed.</p>

        <div class="stat">
            <div>
                <span class="success">
                    <?= $imported ?>
                </span>
                <label>Images Imported</label>
            </div>
            <div>
                <span class="skip">
                    <?= $skipped ?>
                </span>
                <label>Already in DB (Skipped)</label>
            </div>
            <div>
                <span>
                    <?= $imported + $skipped ?>
                </span>
                <label>Total Files Found</label>
            </div>
        </div>

        <p style="margin-top:15px; font-size:0.9rem;">Categories and titles were auto-assigned based on filenames. You
            can edit them anytime in the Admin Panel under <strong>Gallery Management</strong>.</p>

        <a href="admin.php?tab=gallery" class="btn">
            View in Admin Panel →
        </a>
        &nbsp;
        <a href="home_video.php#gallery_section" class="btn"
            style="background:#251d18; color:#dfb180; border:1px solid #dfb180;">
            See Gallery →
        </a>
    </div>
</body>

</html>