<?php
echo "RENDER: " . (getenv('RENDER') ? 'Yes' : 'No') . "\n";
echo "BLOOM_DB_HOST: " . getenv('BLOOM_DB_HOST') . "\n";
echo "BLOOM_DB_NAME: " . getenv('BLOOM_DB_NAME') . "\n";
echo "BLOOM_DB_USER: " . getenv('BLOOM_DB_USER') . "\n";
echo "BLOOM_DB_PORT: " . getenv('BLOOM_DB_PORT') . "\n";
echo "Resolving 'mysql'...\n";
$ip = gethostbyname('mysql');
echo "Resolved 'mysql' to: " . ($ip === 'mysql' ? 'FAILED' : $ip) . "\n";
?>