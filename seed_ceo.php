<?php
require_once 'db.php';

$ceo_name = "Major Haile Gebrselassie";
$ceo_title = "Owner and CEO, Bloom Africa";
$ceo_message = "Welcome and thank you for visiting our website – I believe Bloom Africa has grouped together a remarkable team of devoted managers and staff to administer your needs. We are still at an early stage of our five-year plan, but everything seems to indicate that we are on the track of becoming the best locally-owned restaurant group.";
$ceo_image = "https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=1974&auto=format&fit=crop";

$stmt = $pdo->prepare("UPDATE company_info SET ceo_name=?, ceo_title=?, ceo_message=?, ceo_image=? WHERE id=1");
$stmt->execute([$ceo_name, $ceo_title, $ceo_message, $ceo_image]);

echo "CEO Information Updated successfully.\n";
?>