<?php
require_once '../config/database.php';
requireLogin('organizer');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_campaign'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token Validation Failed");
    }

    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? ''); 
    $location = trim($_POST['location'] ?? '');
    $target_audience = trim($_POST['target_audience'] ?? ''); 
    $description = trim($_POST['description'] ?? '');
    $claimer_id = $_SESSION['user_id'] ?? null;
    $raw_date = $_POST['event_date'] ?? '';

    // Ticket tier fixed prices
    $TICKET_TIER_PRICES = [
        'General Admission' => 100.00,
        'Upper Box'         => 200.00,
        'Lower Box'         => 300.00,
    ];
    $ticketTiers = []; 
    $capacity = 0;

    if ($category === 'Ticket') {
        $qtyGeneral = max(0, (int) ($_POST['qty_general'] ?? 0));
        $qtyUpper   = max(0, (int) ($_POST['qty_upper'] ?? 0));
        $qtyLower   = max(0, (int) ($_POST['qty_lower'] ?? 0));

        if ($qtyGeneral > 0) $ticketTiers[] = ['section' => 'General Admission', 'price' => $TICKET_TIER_PRICES['General Admission'], 'qty' => $qtyGeneral];
        if ($qtyUpper > 0)   $ticketTiers[] = ['section' => 'Upper Box',         'price' => $TICKET_TIER_PRICES['Upper Box'],         'qty' => $qtyUpper];
        if ($qtyLower > 0)   $ticketTiers[] = ['section' => 'Lower Box',         'price' => $TICKET_TIER_PRICES['Lower Box'],         'qty' => $qtyLower];

        if (empty($ticketTiers)) {
            $_SESSION['error_msg'] = "Please set at least one ticket tier's seat count above 0.";
            header("Location: dashboard.php");
            exit();
        } else {
            $capacity = array_sum(array_column($ticketTiers, 'qty'));
        }
    } else {
        $capacity = (int)($_POST['capacity'] ?? 0);
    }

    if (empty($title) || empty($category) || empty($location) || empty($target_audience) || empty($raw_date) || $capacity <= 0) {
        $_SESSION['error_msg'] = "All fields are required, and capacity/slots must be greater than 0.";
        header("Location: dashboard.php");
        exit();
    }

    try {
        // 1. Handle Image Upload FIRST
        $image_url = 'Event_Poster.png';
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileExtension = strtolower(pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid('poster_') . '.' . $fileExtension;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $uploadPath)) {
                $image_url = $newFileName;
            } else {
                throw new Exception("Failed to save the uploaded image.");
            }
        }

        $formatted_date = date('Y-m-d H:i:s', strtotime($raw_date));
        $baseDate = date('Y-m-d', strtotime($raw_date));

        // Reconnect if cloud connection went stale
        try {
            $pdo->query('SELECT 1');
        } catch (\PDOException $pingEx) {
            require '../config/database.php';
        }

        $pdo->beginTransaction();

        $orgStmt = $pdo->prepare("SELECT org_id FROM organizers WHERE claimer_id = ? LIMIT 1");
        $orgStmt->execute([$claimer_id]);
        $org_id = $orgStmt->fetchColumn();

        if (!$org_id) {
            throw new Exception("You are not assigned to an organization.");
        }

        $stmt = $pdo->prepare("
            INSERT INTO events (org_id, event_title, event_date, distribution_location, target_audience, image_url) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$org_id, $title, $formatted_date, $location, $target_audience, $image_url]);
        $newEventId = $pdo->lastInsertId();

        if ($category === 'Ticket') {
            $tierStmt = $pdo->prepare("
                INSERT INTO items (event_id, category, description, price, venue_section, total_inventory, remaining_balance, max_claim_quantity) 
                VALUES (?, 'Ticket', ?, ?, ?, ?, ?, 2)
            ");
            foreach ($ticketTiers as $tier) {
                $tierDescription = $tier['section'] . ' Seat';
                $tierStmt->execute([$newEventId, $tierDescription, $tier['price'], $tier['section'], $tier['qty'], $tier['qty']]);
            }
        } else {
            $total_inv = $capacity * 3;
            $invStmt = $pdo->prepare("
                INSERT INTO items (event_id, category, description, price, venue_section, total_inventory, remaining_balance, max_claim_quantity) 
                VALUES (?, ?, ?, 0.00, NULL, ?, ?, 1)
            ");
            $invStmt->execute([$newEventId, $category, $description, $total_inv, $total_inv]);
        }

        $slots = [
            ['start' => "$baseDate 09:00:00", 'end' => "$baseDate 10:30:00"],
            ['start' => "$baseDate 11:00:00", 'end' => "$baseDate 12:30:00"],
            ['start' => "$baseDate 13:30:00", 'end' => "$baseDate 15:00:00"]
        ];

        $slotStmt = $pdo->prepare("
            INSERT INTO time_slots (event_id, start_time, end_time, capacity, current_reservations) 
            VALUES (?, ?, ?, ?, 0)
        ");
        foreach ($slots as $slot) {
            $slotStmt->execute([$newEventId, $slot['start'], $slot['end'], $capacity]);
        }

        $pdo->commit();
        $_SESSION['success_msg'] = "Successfully launched campaign '$title'!";
        header("Location: dashboard.php");
        exit();

    } catch (Exception $e) {
        try {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
        } catch (Exception $ex) {}
        $_SESSION['error_msg'] = "Error creating campaign: " . $e->getMessage();
        header("Location: dashboard.php");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}