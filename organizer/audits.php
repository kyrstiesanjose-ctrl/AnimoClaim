<?php 
require_once '../config/database.php';
requireLogin('organizer');

$logStmt = $pdo->query("
    SELECT r.*, u.first_name, u.last_name, u.dlsu_id, e.title, t.start_time
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN event_time_slots t ON r.time_slot_id = t.id
    JOIN events e ON t.event_id = e.id
    ORDER BY r.created_at DESC
");
$reservations = $logStmt->fetchAll();

$studentStmt = $pdo->query("
    SELECT u.*, (SELECT COUNT(*) FROM strike_logs s WHERE s.user_id = u.id) as strikes 
    FROM users u 
    WHERE role = 'student' 
    ORDER BY u.first_name ASC
");
$students = $studentStmt->fetchAll();

$currentPage = 'audits';

require_once '../views/organizer/audits_view.php';
?>