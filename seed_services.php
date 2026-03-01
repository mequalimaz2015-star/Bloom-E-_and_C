<?php
require_once 'db.php';

$default_services = [
    [
        'title' => 'Food Delivery',
        'icon' => 'fa-truck',
        'description' => 'Fast and reliable food delivery service to your home or office. Freshly prepared and delivered in under 30 minutes.',
        'image_url' => 'https://images.unsplash.com/photo-1526367790999-015078648402?q=80&w=2070&auto=format&fit=crop'
    ],
    [
        'title' => 'Catering Service',
        'icon' => 'fa-utensils',
        'description' => 'Professional catering for corporate events, private parties, and large gatherings with customized menus.',
        'image_url' => 'https://images.unsplash.com/photo-1555244162-803834f70033?q=80&w=2070&auto=format&fit=crop'
    ],
    [
        'title' => 'Wedding Events',
        'icon' => 'fa-heart',
        'description' => 'Exquisite wedding planning and catering services to make your special day truly unforgettable.',
        'image_url' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2069&auto=format&fit=crop'
    ],
    [
        'title' => 'Birthday Parties',
        'icon' => 'fa-cake-candles',
        'description' => 'Celebrate your birthday with our special party packages, including customized cakes and décor.',
        'image_url' => 'https://images.unsplash.com/photo-1464306208223-e0b4495a0100?q=80&w=2070&auto=format&fit=crop'
    ]
];

foreach ($default_services as $srv) {
    $check = $pdo->prepare("SELECT id FROM services WHERE title = ?");
    $check->execute([$srv['title']]);
    if (!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO services (title, icon, description, image_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$srv['title'], $srv['icon'], $srv['description'], $srv['image_url']]);
        echo "Imported Service: " . $srv['title'] . "\n";
    }
}
?>