<?php
require_once 'db.php';

try {
    // Check if columns exist and add them if they don't
    $columns = ['ceo_name', 'ceo_title', 'ceo_message', 'ceo_image'];

    foreach ($columns as $col) {
        $check = $pdo->query("SHOW COLUMNS FROM company_info LIKE '$col'")->fetch();
        if (!$check) {
            $type = ($col == 'ceo_message') ? 'TEXT' : 'VARCHAR(255)';
            $pdo->exec("ALTER TABLE company_info ADD COLUMN $col $type");
            echo "Added column: $col\n";
        }
    }

    // Seed data if columns were just added
    $stmt = $pdo->prepare("UPDATE company_info SET 
        ceo_name = 'Major Haile Gebrselassie',
        ceo_title = 'Owner and CEO, Bloom Africa',
        ceo_message = 'Welcome and thank you for visiting our website – I believe Bloom Africa has grouped together a remarkable team of devoted managers and staff to administer your needs. We are still at an early stage of our five-year plan, but everything seems to indicate that we are on the track of becoming the best locally-owned restaurant group.',
        ceo_image = 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=1974&auto=format&fit=crop'
        WHERE id = 1 AND (ceo_name IS NULL OR ceo_name = '')");
    $stmt->execute();

    echo "Database sync complete.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>