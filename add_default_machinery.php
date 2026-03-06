<?php
require 'db.php';
try {
    $defaults = [
        [
            'name' => 'Heavy Duty Excavator',
            'serial' => 'EQ-EX-001',
            'desc' => 'High-performance hydraulic excavator for major earthmoving and trenching operations.',
            'status' => 'Available',
            'img' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?q=80&w=800'
        ],
        [
            'name' => 'Tower Crane - 50M',
            'serial' => 'EQ-CR-042',
            'desc' => 'Reliable vertical transport for high-rise construction projects with precision control systems.',
            'status' => 'In Use',
            'img' => 'https://images.unsplash.com/photo-1541888946425-d81bb19480c5?q=80&w=800'
        ],
        [
            'name' => 'Industrial Cement Mixer',
            'serial' => 'EQ-MX-099',
            'desc' => 'Efficient concrete mixing and delivery for structural foundations and large-scale flooring.',
            'status' => 'Available',
            'img' => 'https://images.unsplash.com/photo-1533160600052-a5676735237c?q=80&w=800'
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO construction_equipment (name, serial_number, description, status, image_url) VALUES (?, ?, ?, ?, ?)");

    foreach ($defaults as $d) {
        $stmt->execute([$d['name'], $d['serial'], $d['desc'], $d['status'], $d['img']]);
    }

    echo "Successfully added 3 default machines to your inventory! <br>";
    echo "<a href='admin.php'>Go back to Admin</a>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>