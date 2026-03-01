<?php
require_once 'db.php';

$services = [
    [
        'title' => 'Gourmet Catering',
        'category' => 'Catering Service',
        'icon' => 'fa-utensils',
        'description' => 'Professional catering for all your high-end events and private dinners.',
        'image_url' => 'https://images.unsplash.com/photo-1555244162-803834f70033?q=80&w=2070'
    ],
    [
        'title' => 'Dream Weddings',
        'category' => 'Wedding Events',
        'icon' => 'fa-heart',
        'description' => 'Full-service wedding planning and catering to make your big day perfect.',
        'image_url' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2069'
    ],
    [
        'title' => 'Birthday Bashes',
        'category' => 'Birthday Parties',
        'icon' => 'fa-cake-candles',
        'description' => 'Custom cakes and party menus designed for unforgettable birthday celebrations.',
        'image_url' => 'https://images.unsplash.com/photo-1464306208223-e0b4495a0100?q=80&w=2070'
    ],
    [
        'title' => 'Corporate Galas',
        'category' => 'Corporate Events',
        'icon' => 'fa-briefcase',
        'description' => 'Elegant dining solutions for corporate meetings, galas, and conferences.',
        'image_url' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=2069'
    ]
];

foreach ($services as $srv) {
    $check = $pdo->prepare("SELECT id FROM services WHERE title = ?");
    $check->execute([$srv['title']]);
    if (!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO services (title, category, icon, description, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$srv['title'], $srv['category'], $srv['icon'], $srv['description'], $srv['image_url']]);
        echo "Imported: " . $srv['title'] . "\n";
    }
}
?>