<?php 
require_once '../config/database.php';
requireLogin('organizer');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_campaign'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token Validation Failed");
    }

    $title = trim($_POST['title']);
    $category = trim($_POST['category']); // Must map to Food, Beverage, Merchandise, Academic Kit, Ticket, or Others
    $location = trim($_POST['location']);
    $target_audience = trim($_POST['target_audience']); // Captures beneficiary scope dynamically
    $capacity = (int)$_POST['capacity'];
    $description = trim($_POST['description']);
    $claimer_id = $_SESSION['user_id'];

    // Ticket events sell three fixed-price seat tiers at once (per the
    // university-wide standard: GA ₱100 / Upper Box ₱200 / Lower Box ₱300),
    // not one organizer-chosen price. Every other category stays a single
    // free item, same as before.
    $TICKET_TIER_PRICES = [
        'General Admission' => 100.00,
        'Upper Box'         => 200.00,
        'Lower Box'         => 300.00,
    ];
    $ticketTiers = []; // [ ['section' => ..., 'price' => ..., 'qty' => ...], ... ]
    if ($category === 'Ticket') {
        $qtyGeneral = max(0, (int) ($_POST['qty_general'] ?? 0));
        $qtyUpper   = max(0, (int) ($_POST['qty_upper'] ?? 0));
        $qtyLower   = max(0, (int) ($_POST['qty_lower'] ?? 0));

        if ($qtyGeneral > 0) $ticketTiers[] = ['section' => 'General Admission', 'price' => $TICKET_TIER_PRICES['General Admission'], 'qty' => $qtyGeneral];
        if ($qtyUpper > 0)   $ticketTiers[] = ['section' => 'Upper Box',         'price' => $TICKET_TIER_PRICES['Upper Box'],         'qty' => $qtyUpper];
        if ($qtyLower > 0)   $ticketTiers[] = ['section' => 'Lower Box',         'price' => $TICKET_TIER_PRICES['Lower Box'],         'qty' => $qtyLower];

        if (empty($ticketTiers)) {
            $error_msg = "Please set at least one ticket tier's seat count above 0.";
        } else {
            // Pickup capacity per slot is derived from total seats sold, not a
            // separate manually-entered number — any ticket holder can claim
            // at any of the 3 pickup windows, so each window just needs to be
            // able to hold everyone.
            $capacity = array_sum(array_column($ticketTiers, 'qty'));
        }
    }

    if (empty($error_msg)) {
    try {
        $pdo->beginTransaction();

        // 1. Fetch the org_id for this organizer
        $orgStmt = $pdo->prepare("SELECT org_id FROM organizers WHERE claimer_id = ? LIMIT 1");
        $orgStmt->execute([$claimer_id]);
        $org_id = $orgStmt->fetchColumn();

        if (!$org_id) {
            throw new Exception("You are not assigned to an organization.");
        }

        // 2. Handle the Image Upload Routing
        $image_url = 'Event_Poster.png'; // Fallback
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
                throw new Exception("Failed to route and save the uploaded image.");
            }
        }

        // 3. Insert into events using the selected form date, location, target audience, and image
        $raw_date = $_POST['event_date'];
        $formatted_date = date('Y-m-d H:i:s', strtotime($raw_date));
        $baseDate = date('Y-m-d', strtotime($raw_date));

        $stmt = $pdo->prepare("
            INSERT INTO events (org_id, event_title, event_date, distribution_location, target_audience, image_url) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$org_id, $title, $formatted_date, $location, $target_audience, $image_url]);
        $newEventId = $pdo->lastInsertId();

        // 4. Insert into items.
        //    Ticket events get one row per seat tier (fixed prices, tier-specific
        //    inventory). Every other category keeps the original single free item.
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

        // 5. Insert into time_slots
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
        $success_msg = "Successfully launched campaign '$title' with target audience '$target_audience'.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_msg = "Error creating campaign: " . $e->getMessage();
    }
    } // end if (empty($error_msg))
}

// Stats Queries
$activeCampaigns = $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= NOW()")->fetchColumn();
$totalReserved = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'reserved'")->fetchColumn();
$itemsClaimed = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'claimed'")->fetchColumn();
$totalExpired = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'expired'")->fetchColumn();

$totalReservationsEver = $totalReserved + $itemsClaimed + $totalExpired;
$noShowRate = $totalReservationsEver > 0 ? round(($totalExpired / $totalReservationsEver) * 100) : 0;

// Fetch Campaigns for the Dashboard Table with proper category join
$campaignsStmt = $pdo->query("
    SELECT e.event_id AS id, 
           e.event_title AS title, 
           e.event_date AS created_at, 
           e.distribution_location AS location,
           i.category AS category,
        COALESCE((SELECT SUM(capacity) FROM time_slots WHERE event_id = e.event_id), 0) as total_capacity,
        COALESCE((SELECT SUM(current_reservations) FROM time_slots WHERE event_id = e.event_id), 0) as total_reservations,
        (SELECT COUNT(*) FROM time_slots WHERE event_id = e.event_id) as slot_count
    FROM events e
    LEFT JOIN items i ON e.event_id = i.event_id
    WHERE e.event_date >= NOW()
    ORDER BY e.event_date ASC
");
$campaigns = $campaignsStmt->fetchAll();

$currentPage = 'dashboard';

require_once '../views/organizer/dashboard_view.php';
?>