<?php
require_once 'db.php';

try {
    // Add columns to company_info if they don't exist
    $cols_to_add = [
        'tiktok' => 'VARCHAR(255)',
        'linkedin' => 'VARCHAR(255)',
        'telegram' => 'VARCHAR(255)',
        'whatsapp' => 'VARCHAR(255)',
        'hero_title' => 'VARCHAR(255)',
        'hero_subtitle' => 'TEXT',
        'hero_button_text' => 'VARCHAR(100)',
        'hero_image' => 'VARCHAR(255)',
        'about_subtitle' => 'TEXT',
        'about_image_main' => 'VARCHAR(255)',
        'about_image_sub1' => 'VARCHAR(255)',
        'about_image_sub2' => 'VARCHAR(255)',
        'history_title' => 'VARCHAR(255)',
        'history_text1' => 'TEXT',
        'history_text2' => 'TEXT',
        'dev_name' => 'VARCHAR(255)',
        'dev_photo' => 'VARCHAR(255)',
        'dev_email' => 'VARCHAR(255)',
        'dev_phone' => 'VARCHAR(50)',
        'copyright_text' => 'VARCHAR(255)'
    ];

    foreach ($cols_to_add as $col => $type) {
        try {
            $pdo->exec("ALTER TABLE company_info ADD COLUMN $col $type");
            echo "Added column: $col\n";
        } catch (PDOException $e) {
            // Probably already exists
        }
    }

    // Create team_members table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS team_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        role VARCHAR(100) NOT NULL,
        image_url VARCHAR(255),
        order_index INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table team_members check passed.\n";

    // Set default values for the first record in company_info (the one with id=1)
    $stmt = $pdo->prepare("UPDATE company_info SET 
        hero_title = ?, hero_subtitle = ?, hero_button_text = ?, 
        about_subtitle = ?, history_title = ?, history_text1 = ?, history_text2 = ?,
        dev_name = ?, dev_email = ?, dev_phone = ?, copyright_text = ?
        WHERE id = 1");

    $stmt->execute([
        'Taste the Soul of Africa',
        'A modern and authentic dining experience fusing rich African heritage with contemporary culinary arts.',
        'Explore Menu',
        'Two Decades of Culinary Mastery, Crafted with Passion and Served with Heart. Welcome to a Legacy of Flavor Since 2005.',
        'Our Rich History',
        'Established in 2026, Bloom Africa emerged from a singular vision: to create a sanctuary where the diverse tastes of Africa could be celebrated with modern elegance. What began as an intimate family-run kitchen has transformed into a culinary landmark, known for its unwavering commitment to quality and heritage.',
        'Over the last two decades, we have mentored dozens of chefs and hosted thousands of unforgettable moments. Today, Bloom Africa stands as a testament to the power of authentic flavors and the spirit of African hospitality.',
        'Mequannent Gashaw',
        'mequannentgashaw12@gmail.com',
        '0918592028',
        'All Rights Reserved.'
    ]);
    echo "Updated default content in company_info.\n";

    // Check if team_members is empty
    $count = $pdo->query("SELECT COUNT(*) FROM team_members")->fetchColumn();
    if ($count == 0) {
        $stmt_team = $pdo->prepare("INSERT INTO team_members (name, role, image_url, order_index) VALUES (?, ?, ?, ?)");
        $stmt_team->execute(['Abebe Bikila', 'Executive Chef', 'https://images.unsplash.com/photo-1583394838336-acd977730f90?q=80&w=1968&auto=format&fit=crop', 1]);
        $stmt_team->execute(['Sara Tadesse', 'Pastry Specialist', 'https://images.unsplash.com/photo-1595273670150-db0a3d39074f?q=80&w=2070&auto=format&fit=crop', 2]);
        $stmt_team->execute(['Desta Kassahun', 'General Manager', 'https://images.unsplash.com/photo-1574015974293-817f0efebb1b?q=80&w=1946&auto=format&fit=crop', 3]);
        echo "Seeded team_members table.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>