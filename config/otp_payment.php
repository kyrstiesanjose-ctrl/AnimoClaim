<?php
/* config/otp_payment.php
   Generates, emails, and verifies the 6-digit OTP used to confirm a
   ticket payment, backed by the `email_otps` table. Tied to a specific
   `payments` row (email_otps.payment_id -> payments.payment_id).

   Purpose string used throughout: 'payment_verification'
   OTP lifetime: 5 minutes */

require_once __DIR__ . '/mailer.php';

const PAYMENT_OTP_TTL_MINUTES = 5;
const PAYMENT_OTP_PURPOSE = 'payment_verification';

/**
 *  accepts dlsu acc only
 */
function isDlsuEmail(string $email): bool {
    return (bool) preg_match('/@dlsu\.edu\.ph$/i', trim($email));
}

/**
 * Generates and emails a new OTP while invalidating existing unverified codes for the payment. 
 * Returns true on success or an error string on failure..
 */
function generateAndSendPaymentOtp(PDO $pdo, string $claimerId, int $paymentId, string $email, string $firstName): string|true {
    if (!isDlsuEmail($email)) {
        return 'OTP codes can only be sent to a verified @dlsu.edu.ph email address.';
    }

    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+' . PAYMENT_OTP_TTL_MINUTES . ' minutes'));

    try {
        $pdo->beginTransaction();

        // Invalidate any older, unverified OTPs for this same payment so
        // only the most recently requested code is ever valid.
        $inv = $pdo->prepare("UPDATE email_otps SET is_verified = 1 WHERE payment_id = ? AND purpose = ? AND is_verified = 0");
        $inv->execute([$paymentId, PAYMENT_OTP_PURPOSE]);

        $ins = $pdo->prepare("
            INSERT INTO email_otps (claimer_id, payment_id, email, otp_code, purpose, is_verified, expires_at)
            VALUES (?, ?, ?, ?, ?, 0, ?)
        ");
        $ins->execute([$claimerId, $paymentId, $email, $code, PAYMENT_OTP_PURPOSE, $expiresAt]);

        $pdo->commit();
    } catch (\Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('[AnimoClaim otp_payment] Could not persist OTP for payment #' . $paymentId . ': ' . $e->getMessage());
        return 'Something went wrong generating your OTP. Please try again.';
    }

    $subject = 'Your AnimoClaim Payment Confirmation Code';
    $rows = _emailRow('Code expires in', PAYMENT_OTP_TTL_MINUTES . ' minutes');
    $body = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:520px;margin:0 auto;color:#163300">'
          . '<div style="background:#0e0f0c;border-radius:20px;padding:18px 22px;margin-bottom:18px">'
          . '<h1 style="color:#9fe870;margin:0;font-size:20px;letter-spacing:-0.5px">AnimoClaim</h1>'
          . '<p style="color:#9aa89a;margin:2px 0 0;font-size:11px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase">DLSU Official Giveaway Portal</p>'
          . '</div>'
          . '<p style="font-size:14px">Hi ' . htmlspecialchars($firstName) . ',</p>'
          . '<p style="font-size:14px;line-height:1.5">Use the code below to confirm your ticket payment. Entering this code authorizes AnimoClaim to mark your payment as verified.</p>'
          . '<p style="font-size:36px;font-weight:800;letter-spacing:8px;color:#0e0f0c;background:#e2f6d5;border-radius:14px;text-align:center;padding:16px 0;margin:18px 0">' . $code . '</p>'
          . '<table style="border-collapse:collapse;width:100%;margin:8px 0">' . $rows . '</table>'
          . '<p style="color:#6b7a63;font-size:12px;line-height:1.5">If you didn\'t request this, you can safely ignore this email — no payment will be confirmed without this code.</p>'
          . '<p style="color:#a3ada0;font-size:11px;margin-top:24px">This is an automated message from AnimoClaim. Please do not reply to this email.</p>'
          . '</div>';

    sendMail($email, $firstName, $subject, $body);
    return true;
}

/**
 * Verifies a submitted code for a given payment. On success, marks the
 * OTP row used  and returns true. Returns false for
 * wrong/expired/already-used codes.
 */
function verifyPaymentOtp(PDO $pdo, string $claimerId, int $paymentId, string $inputCode): bool {
    $stmt = $pdo->prepare("
        SELECT * FROM email_otps
        WHERE claimer_id = ? AND payment_id = ? AND purpose = ? AND is_verified = 0
        ORDER BY otp_id DESC LIMIT 1
    ");
    $stmt->execute([$claimerId, $paymentId, PAYMENT_OTP_PURPOSE]);
    $row = $stmt->fetch();

    if (!$row) return false;
    if (strtotime($row['expires_at']) < time()) return false;
    if (!hash_equals($row['otp_code'], $inputCode)) return false;

    $upd = $pdo->prepare("UPDATE email_otps SET is_verified = 1 WHERE otp_id = ?");
    $upd->execute([$row['otp_id']]);
    return true;
}
