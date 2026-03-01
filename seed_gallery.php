<?php
require_once 'db.php';

$default_images = [
    [
        'title' => 'Elegant Dining Hall',
        'category' => 'Ambience',
        'image_url' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?q=80&w=2070&auto=format&fit=crop',
        'description' => 'Our main dining hall offers a sophisticated atmosphere with breathtaking views.'
    ],
    [
        'title' => 'Signature Cocktail',
        'category' => 'Food',
        'image_url' => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=2070&auto=format&fit=crop',
        'description' => 'Expertly crafted cocktails using the finest local and international spirits.'
    ],
    [
        'title' => 'Gourmet Presentation',
        'category' => 'Food',
        'image_url' => 'https://images.unsplash.com/photo-1544025162-8111d4e06223?q=80&w=1969&auto=format&fit=crop',
        'description' => 'Artfully plated dishes that taste as good as they look.'
    ],
    [
        'title' => 'Outdoor Terrace',
        'category' => 'Outdoor',
        'image_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop',
        'description' => 'Enjoy your meal in the fresh air on our beautiful outdoor terrace.'
    ],
    [
        'title' => 'Kitchen Mastery',
        'category' => 'Kitchen',
        'image_url' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?q=80&w=1974&auto=format&fit=crop',
        'description' => 'Our chefs working hard to bring you the best African-European fusion.'
    ],
    [
        'title' => 'Signature Beverage',
        'category' => 'Beverage',
        'image_url' => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=2070&auto=format&fit=crop',
        'description' => 'A refreshing selection of house-made beverages and exotic blends.'
    ],
    [
        'title' => 'Premium Aged Wine',
        'category' => 'Beverage',
        'image_url' => 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?q=80&w=2070&auto=format&fit=crop',
        'description' => 'A fine selection of vintage wines from the best vineyards.'
    ],
    [
        'title' => 'Classic Dry Gin',
        'category' => 'Beverage',
        'image_url' => 'https://images.unsplash.com/photo-1551538827-9c037cb4f32a?q=80&w=1965&auto=format&fit=crop',
        'description' => 'Our signature Gin, served with premium tonic and botanical garnishes.'
    ],
    [
        'title' => 'Exotic Fruit Mocktail',
        'category' => 'Beverage',
        'image_url' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=1964&auto=format&fit=crop',
        'description' => 'A vibrant blend of seasonal tropical fruits, perfect for any time of day.'
    ]
];

foreach ($default_images as $img) {
    // Check if it already exists by URL
    $check = $pdo->prepare("SELECT id FROM gallery WHERE image_url = ?");
    $check->execute([$img['image_url']]);
    if (!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO gallery (title, category, image_url, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$img['title'], $img['category'], $img['image_url'], $img['description']]);
        echo "Imported: " . $img['title'] . "\n";
    } else {
        echo "Skipped (exists): " . $img['title'] . "\n";
    }
}
?>