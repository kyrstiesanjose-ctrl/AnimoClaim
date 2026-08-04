<?php
require_once '../config/database.php';
requireLogin('organizer');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT c.*, o.role_level, org.org_name 
    FROM claimers c
    JOIN organizers o ON c.claimer_id = o.claimer_id
    JOIN organizations org ON o.org_id = org.org_id
    WHERE c.claimer_id = ?
");
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch();

$currentPage = 'profile';

require_once '../views/organizer/profile_view.php';
?>