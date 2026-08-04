<?php
/* api/payment/request_otp.php
   POST: reservation_id, payment_method (gcash|bank_transfer), account_number, csrf_token

    Users select a method, enter account details, and verify via OTP. 
    AnimoClaim then generates a unique reference number from payment_id. 
    Payment totals are calculated strictly on the server.

   Amount is always computed server-side from items.price * quantity —
   never trusted from the client. */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/otp_payment.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['role'] ?? '') !== 'student') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$csrfToken = $input['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid session token. Please refresh and try again.']);
    exit();
}

$reservationId = filter_var($input['reservation_id'] ?? null, FILTER_VALIDATE_INT);
$paymentMethod = $input['payment_method'] ?? '';
$accountNumber = trim($input['account_number'] ?? '');
$claimerId = $_SESSION['user_id'];

if (!$reservationId || !in_array($paymentMethod, ['gcash', 'bank_transfer'], true) || $accountNumber === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please select a payment method and enter the account you\'re paying from.']);
    exit();
}

if ($paymentMethod === 'gcash' && !preg_match('/^(09|\+639)\d{9}$/', preg_replace('/\s+/', '', $accountNumber))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid GCash mobile number (e.g. 09171234567).']);
    exit();
}

try {
    // 1. Confirm the reservation belongs to this student and is awaiting payment.
    $stmt = $pdo->prepare("
        SELECT r.reservation_id, r.quantity, i.price, c.first_name, c.email
        FROM reservations r
        JOIN items i ON r.item_id = i.item_id
        JOIN claimers c ON r.claimer_id = c.claimer_id
        WHERE r.reservation_id = ? AND r.claimer_id = ? AND r.status = 'pending_payment'
        LIMIT 1
    ");
    $stmt->execute([$reservationId, $claimerId]);
    $res = $stmt->fetch();

    if (!$res) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Reservation not found or already paid.']);
        exit();
    }

    if (!isDlsuEmail($res['email'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'OTP can only be sent to a verified @dlsu.edu.ph email address.']);
        exit();
    }

    $amount = round(((float) $res['price']) * ((int) $res['quantity']), 2);

    // The account/mobile number the student is paying from — kept for the
    // organizer's manual cross-check only. Stored in the existing unused
    // gateway_response TEXT column, so no schema change is needed.
    $gatewayResponse = json_encode(['sender_account' => $accountNumber]);

    // 2. Reuse an existing pending payment row for this reservation, or create one.
    $existing = $pdo->prepare("SELECT payment_id FROM payments WHERE reservation_id = ? AND status = 'pending' LIMIT 1");
    $existing->execute([$reservationId]);
    $existingRow = $existing->fetch();

    if ($existingRow) {
        $paymentId = (int) $existingRow['payment_id'];
        $upd = $pdo->prepare("UPDATE payments SET amount = ?, payment_method = ?, gateway_response = ? WHERE payment_id = ?");
        $upd->execute([$amount, $paymentMethod, $gatewayResponse, $paymentId]);
    } else {
        $ins = $pdo->prepare("
            INSERT INTO payments (reservation_id, claimer_id, amount, payment_method, status, gateway_response)
            VALUES (?, ?, ?, ?, 'pending', ?)
        ");
        $ins->execute([$reservationId, $claimerId, $amount, $paymentMethod, $gatewayResponse]);
        $paymentId = (int) $pdo->lastInsertId();
    }

    // 3. Generate the reference number now that we have a payment_id to
    //    base it on — guaranteed unique since payment_id is unique.
    $prefix = $paymentMethod === 'gcash' ? 'GCASH' : 'BANK';
    $referenceNumber = $prefix . '-' . str_pad((string) $paymentId, 6, '0', STR_PAD_LEFT) . '-' . strtoupper(bin2hex(random_bytes(2)));
    $updRef = $pdo->prepare("UPDATE payments SET reference_number = ? WHERE payment_id = ?");
    $updRef->execute([$referenceNumber, $paymentId]);

    // 4. Generate + send the OTP.
    $result = generateAndSendPaymentOtp($pdo, $claimerId, $paymentId, $res['email'], $res['first_name']);

    if ($result !== true) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $result]);
        exit();
    }

    // Mask the email for display (e.g. ky***@dlsu.edu.ph)
    $maskedEmail = preg_replace('/^(.{2}).+(@.+)$/', '$1***$2', $res['email']);

    echo json_encode([
        'success' => true,
        'message' => 'A 6-digit code was sent to ' . $maskedEmail . '. It expires in ' . PAYMENT_OTP_TTL_MINUTES . ' minutes.',
        'payment_id' => $paymentId,
        'amount' => number_format($amount, 2),
    ]);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    error_log('[AnimoClaim request_otp] ' . $e->getMessage());
}
