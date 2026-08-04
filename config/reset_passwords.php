<?php
require_once __DIR__ . '/database.php';

$plainPassword = "password123";
$hashed = password_hash($plainPassword, PASSWORD_DEFAULT);

$pdo->prepare("UPDATE claimers SET password = ?")->execute([$hashed]);

echo "All passwords reset to: $plainPassword (hashed in DB)";
?>