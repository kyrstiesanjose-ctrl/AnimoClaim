<?php
require_once '../config/database.php';
requireLogin('organizer');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch();

$currentPage = 'profile';

require_once '../views/organizer/profile_view.php';
?>