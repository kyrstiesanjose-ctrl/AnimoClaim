<?php 
require_once '../config/database.php';
requireLogin('organizer');

$claimer_id = $_SESSION['user_id'];

// 1. Fetch the org_id for this organizer
$orgStmt = $pdo->prepare("SELECT org_id FROM organizers WHERE claimer_id = ? LIMIT 1");
$orgStmt->execute([$claimer_id]);
$org_id = $orgStmt->fetchColumn();

if (!$org_id) {
    die("You are not assigned to an organization.");
}

// 2. Handle Manual Inventory Adjustments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adjust_inventory'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token Validation Failed");
    }

    $item_id = (int)$_POST['item_id'];
    $adjustment_qty = (int)$_POST['adjustment_qty']; // Can be positive or negative
    
    try {
        $pdo->beginTransaction();

        // Update the master items table
        $updateStmt = $pdo->prepare("
            UPDATE items 
            SET total_inventory = total_inventory + ?, 
                remaining_balance = remaining_balance + ? 
            WHERE item_id = ?
        ");
        $updateStmt->execute([$adjustment_qty, $adjustment_qty, $item_id]);

        // Log the exact movement into the granular inventory table
        $logStmt = $pdo->prepare("
            INSERT INTO inventory (item_id, quantity_adjusted, adjustment_type, timestamp) 
            VALUES (?, ?, 'Manual Adjustment', NOW())
        ");
        $logStmt->execute([$item_id, $adjustment_qty]);

        $pdo->commit();
        $success_msg = "Successfully recorded stock movement.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_msg = "Error updating inventory: " . $e->getMessage();
    }
}

// 3. Fetch Master Inventory Data for Dashboard
$itemsStmt = $pdo->prepare("
    SELECT i.*, e.event_title 
    FROM items i
    JOIN events e ON i.event_id = e.event_id
    WHERE e.org_id = ?
    ORDER BY e.event_date DESC, i.item_id DESC
");
$itemsStmt->execute([$org_id]);
$inventory_items = $itemsStmt->fetchAll();

// 4. Fetch the Historical Movement Logs
$logsStmt = $pdo->prepare("
    SELECT inv.*, i.description, i.category, e.event_title
    FROM inventory inv
    JOIN items i ON inv.item_id = i.item_id
    JOIN events e ON i.event_id = e.event_id
    WHERE e.org_id = ?
    ORDER BY inv.timestamp DESC
    LIMIT 50
");
$logsStmt->execute([$org_id]);
$inventory_logs = $logsStmt->fetchAll();

// 5. Calculate Quick Analytics
$totalItems = count($inventory_items);
$totalStock = array_sum(array_column($inventory_items, 'remaining_balance'));
$lowStockCount = count(array_filter($inventory_items, function($item) {
    return ($item['total_inventory'] > 0 && ($item['remaining_balance'] / $item['total_inventory']) <= 0.2);
}));

$currentPage = 'inventory';

require_once '../views/organizer/inventory_view.php';
?>