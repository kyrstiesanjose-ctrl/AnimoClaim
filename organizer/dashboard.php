<?php 
require_once '../config/database.php';
requireLogin('organizer');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_campaign'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token Validation Failed");
    }

    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $capacity = (int)$_POST['capacity'];
    $description = trim($_POST['description']);
    $organizer_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO events (organizer_id, title, description, location, image_url, category, is_active) VALUES (?, ?, ?, ?, 'school_supplies.jpg', ?, 1)");
        $stmt->execute([$organizer_id, $title, $description, $location, $category]);
        $newEventId = $pdo->lastInsertId();

        $total_inv = $capacity * 3;
        $invStmt = $pdo->prepare("INSERT INTO inventory (event_id, item_name, total_quantity, remaining_quantity) VALUES (?, ?, ?, ?)");
        $invStmt->execute([$newEventId, $title . ' Package', $total_inv, $total_inv]);

        $baseDate = date('Y-m-d', strtotime('+2 days'));
        $slots = [
            ['start' => "$baseDate 09:00:00", 'end' => "$baseDate 10:30:00"],
            ['start' => "$baseDate 11:00:00", 'end' => "$baseDate 12:30:00"],
            ['start' => "$baseDate 13:30:00", 'end' => "$baseDate 15:00:00"]
        ];

        $slotStmt = $pdo->prepare("INSERT INTO event_time_slots (event_id, start_time, end_time, max_capacity, current_reservations) VALUES (?, ?, ?, ?, 0)");
        foreach ($slots as $slot) {
            $slotStmt->execute([$newEventId, $slot['start'], $slot['end'], $capacity]);
        }

        $pdo->commit();
        $success_msg = "Successfully launched campaign '$title' with 3 schedule slots.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_msg = "Error creating campaign: " . $e->getMessage();
    }
}

$activeCampaigns = $pdo->query("SELECT COUNT(*) FROM events WHERE is_active = 1")->fetchColumn();
$totalReserved = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'reserved'")->fetchColumn();
$itemsClaimed = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'claimed'")->fetchColumn();
$totalExpired = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'expired'")->fetchColumn();

$totalReservationsEver = $totalReserved + $itemsClaimed + $totalExpired;
$noShowRate = $totalReservationsEver > 0 ? round(($totalExpired / $totalReservationsEver) * 100) : 0;

$campaignsStmt = $pdo->query("
    SELECT e.*, 
        COALESCE((SELECT SUM(max_capacity) FROM event_time_slots WHERE event_id = e.id), 0) as total_capacity,
        COALESCE((SELECT SUM(current_reservations) FROM event_time_slots WHERE event_id = e.id), 0) as total_reservations,
        (SELECT COUNT(*) FROM event_time_slots WHERE event_id = e.id) as slot_count
    FROM events e
    WHERE e.is_active = 1
    ORDER BY e.created_at DESC
");
$campaigns = $campaignsStmt->fetchAll();

$currentPage = 'dashboard';

require_once '../views/organizer/dashboard_view.php';
?>