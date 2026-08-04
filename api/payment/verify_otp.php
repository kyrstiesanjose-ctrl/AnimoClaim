<?php
/* api/payment/verify_otp.php
   POST: reservation_id, otp_code, csrf_token
   Verifies the code against email_otps, then marks the payment
   'completed', the reservation 'reserved', logs it in payment_logs, and
   sends the invoice email. */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/otp_payment.php';
require_once __DIR__ . '/../../config/invoice.php';

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
$otpCode = trim($input['otp_code'] ?? '');
$claimerId = $_SESSION['user_id'];

if (!$reservationId || !preg_match('/^\d{6}$/', $otpCode)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter the 6-digit code.']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT payment_id, status FROM payments
        WHERE reservation_id = ? AND claimer_id = ? AND status = 'pending'
        ORDER BY payment_id DESC LIMIT 1
    ");
    $stmt->execute([$reservationId, $claimerId]);
    $payment = $stmt->fetch();

    if (!$payment) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No pending payment found for this reservation. Please request a new OTP.']);
        exit();
    }

    $paymentId = (int) $payment['payment_id'];

    if (!verifyPaymentOtp($pdo, $claimerId, $paymentId, $otpCode)) {
        echo json_encode(['success' => false, 'message' => 'That code is incorrect or has expired. Please try again or resend a new code.']);
        exit();
    }

    $pdo->beginTransaction();

    $updPay = $pdo->prepare("UPDATE payments SET status = 'completed', verified_at = NOW() WHERE payment_id = ?");
    $updPay->execute([$paymentId]);

    $updRes = $pdo->prepare("UPDATE reservations SET status = 'reserved' WHERE reservation_id = ? AND claimer_id = ?");
    $updRes->execute([$reservationId, $claimerId]);

    $log = $pdo->prepare("
        INSERT INTO payment_logs (payment_id, claimer_id, previous_status, new_status, action_type, log_message)
        VALUES (?, ?, 'pending', 'completed', 'OTP_VERIFIED', 'Payment confirmed via email OTP')
    ");
    $log->execute([$paymentId, $claimerId]);

    $pdo->commit();

    sendPaymentInvoice($pdo, $paymentId);

    echo json_encode([
        'success' => true,
        'message' => 'Payment confirmed! Your ticket is now reserved and a receipt has been emailed to you.',
    ]);

} catch (\PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    error_log('[AnimoClaim verify_otp] ' . $e->getMessage());
}
