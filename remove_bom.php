<?php
$files = ['admin.php', 'db.php', 'index.php', 'Construction/index.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Remove BOM if present
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
            file_put_contents($file, $content);
            echo "Removed BOM from $file\n";
        } else {
            echo "No BOM found in $file\n";
        }
    }
}
?>