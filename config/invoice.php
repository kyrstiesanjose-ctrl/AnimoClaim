<?php
/* config/invoice.php
   Builds and sends the "payment confirmed" invoice email once a payment's
   OTP has been verified. Pulls everything from the payment's reservation:
   claimer, item, event, and (if it's a ticket) the seat section. */

require_once __DIR__ . '/mailer.php';

/** A full-width section label row (e.g. "Ticket", "Payment", "Claimer") inside the invoice table. */
function _emailSectionHeader(string $label): string {
    return '<tr><td colspan="2" style="padding:14px 0 4px;border-bottom:1px solid #e2f6d5">'
         . '<span style="font-size:10px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:#4d8a2e">' . htmlspecialchars($label) . '</span>'
         . '</td></tr>';
}

function fetchInvoiceContext(PDO $pdo, int $paymentId): ?array {
    $stmt = $pdo->prepare("
        SELECT
            p.payment_id, p.reference_number, p.amount, p.payment_method, p.gateway_response,
            p.status AS payment_status, p.verified_at, p.created_at AS payment_created_at,
            r.reservation_id, r.quantity, r.status AS reservation_status,
            c.claimer_id, c.first_name, c.last_name, c.email,
            i.item_id, i.category, i.description AS item_description, i.price,
            e.event_id, e.org_id, e.event_title, e.event_date, e.distribution_location,
            ts.start_time AS slot_start_time, ts.end_time AS slot_end_time,
            COALESCE(td.venue_section, i.venue_section) AS venue_section
        FROM payments p
        JOIN reservations r ON p.reservation_id = r.reservation_id
        JOIN claimers c ON p.claimer_id = c.claimer_id
        JOIN items i ON r.item_id = i.item_id
        JOIN events e ON i.event_id = e.event_id
        JOIN time_slots ts ON r.slot_id = ts.slot_id
        LEFT JOIN ticket_details td ON td.reservation_id = r.reservation_id
        WHERE p.payment_id = ?
        LIMIT 1
    ");
    $stmt->execute([$paymentId]);
    return $stmt->fetch() ?: null;
}

/**
 * Sends the invoice email for a completed payment. Returns true if the
 * email context was found and a send was attempted, false if the
 * payment/reservation couldn't be resolved.
 */
function sendPaymentInvoice(PDO $pdo, int $paymentId): bool {
    $ctx = fetchInvoiceContext($pdo, $paymentId);
    if (!$ctx) {
        error_log('[AnimoClaim invoice] No context found for payment #' . $paymentId);
        return false;
    }

    if (!isDlsuEmail($ctx['email'])) {
        // dlsu only
        return false;
    }

    $claimerName = trim($ctx['first_name'] . ' ' . $ctx['last_name']);
    $unitPrice = (float) $ctx['price'];
    $qty = (int) $ctx['quantity'];
    $total = (float) $ctx['amount'];

    $methodLabel = $ctx['payment_method'] === 'gcash' ? 'GCash' : 'Bank Transfer';
    $verifiedAt = $ctx['verified_at'] ? date('M d, Y \a\t h:i A', strtotime($ctx['verified_at'])) : date('M d, Y \a\t h:i A');
    $eventDateLabel = date('M d, Y \a\t h:i A', strtotime($ctx['event_date']));
    $slotLabel = date('M d, Y', strtotime($ctx['slot_start_time'])) . ', '
               . date('h:i A', strtotime($ctx['slot_start_time'])) . ' - ' . date('h:i A', strtotime($ctx['slot_end_time']));

    $paidFromAccount = null;
    if (!empty($ctx['gateway_response'])) {
        $decoded = json_decode($ctx['gateway_response'], true);
        $paidFromAccount = $decoded['sender_account'] ?? null;
    }
    $paidFromLabel = $ctx['payment_method'] === 'gcash' ? 'GCash Number Used' : 'Bank Account Used';

    // --- CLAIMER section ---
    $rows = _emailSectionHeader('Claimer')
          . _emailRow('Name', htmlspecialchars($claimerName))
          . _emailRow('DLSU ID', htmlspecialchars($ctx['claimer_id']));

    // --- TICKET section ---
    $rows .= _emailSectionHeader('Ticket')
           . _emailRow('Event', htmlspecialchars($ctx['event_title']))
           . _emailRow('Event Date & Time', $eventDateLabel)
           . _emailRow('Item', htmlspecialchars($ctx['item_description'] ?: $ctx['category']));

    if (!empty($ctx['venue_section'])) {
        $rows .= _emailRow('Seat Section', htmlspecialchars($ctx['venue_section']));
    }

    $rows .= _emailRow('Claim Time Slot', $slotLabel)
           . _emailRow('Claim Location', htmlspecialchars($ctx['distribution_location']))
           . _emailRow('Quantity', (string) $qty)
           . _emailRow('Reservation ID', '#' . htmlspecialchars($ctx['reservation_id']));

    // --- PAYMENT section ---
    $rows .= _emailSectionHeader('Payment')
           . _emailRow('Invoice / Reference #', htmlspecialchars($ctx['reference_number']))
           . _emailRow('Payment Method', htmlspecialchars($methodLabel));

    if ($paidFromAccount) {
        $rows .= _emailRow($paidFromLabel, htmlspecialchars($paidFromAccount));
    }

    $rows .= _emailRow('Unit Price', '₱' . number_format($unitPrice, 2))
           . _emailRow('Total Amount Paid', '<span style="font-size:15px">₱' . number_format($total, 2) . '</span>')
           . _emailRow('Verified On', $verifiedAt);

    $subject = 'Payment Confirmed — AnimoClaim Invoice #' . $ctx['payment_id'];
    $body = _emailWrapper(
        'Payment Confirmed',
        'Hi ' . htmlspecialchars($ctx['first_name']) . ', we\'ve confirmed your payment for the ticket below. Keep this email as your receipt.',
        $rows,
        'Your ticket is now marked <strong>Reserved</strong> and ready to claim. Just tap your DLSU ID at the distribution counter during your scheduled time slot — no screenshots or codes needed to claim, this email is for your records only.'
    );

    sendMail($ctx['email'], $ctx['first_name'], $subject, $body);
    sendOrganizerPaymentNotice($pdo, $ctx);
    return true;
}

/**
 * Notifies every organizer/admin attached to the event's organization that
 * a payment came in — separate content from the student's receipt, since
 * they need buyer identity and a reconciliation note, not claim instructions.
 */
function sendOrganizerPaymentNotice(PDO $pdo, array $ctx): void {
    $stmt = $pdo->prepare("
        SELECT c.first_name, c.email
        FROM organizers o
        JOIN claimers c ON o.claimer_id = c.claimer_id
        WHERE o.org_id = ? AND o.dashboard_access = 1
    ");
    $stmt->execute([$ctx['org_id']]);
    $organizers = $stmt->fetchAll();
    if (!$organizers) return;

    $claimerName = trim($ctx['first_name'] . ' ' . $ctx['last_name']);
    $methodLabel = $ctx['payment_method'] === 'gcash' ? 'GCash' : 'Bank Transfer';
    $verifiedAt = $ctx['verified_at'] ? date('M d, Y \a\t h:i A', strtotime($ctx['verified_at'])) : date('M d, Y \a\t h:i A');
    $eventDateLabel = date('M d, Y \a\t h:i A', strtotime($ctx['event_date']));
    $slotLabel = date('M d, Y', strtotime($ctx['slot_start_time'])) . ', '
               . date('h:i A', strtotime($ctx['slot_start_time'])) . ' - ' . date('h:i A', strtotime($ctx['slot_end_time']));
    $total = (float) $ctx['amount'];

    $paidFromAccount = null;
    if (!empty($ctx['gateway_response'])) {
        $decoded = json_decode($ctx['gateway_response'], true);
        $paidFromAccount = $decoded['sender_account'] ?? null;
    }
    $paidFromLabel = $ctx['payment_method'] === 'gcash' ? 'GCash Number Used' : 'Bank Account Used';

    // --- BUYER section ---
    $rows = _emailSectionHeader('Buyer')
          . _emailRow('Name', htmlspecialchars($claimerName))
          . _emailRow('DLSU ID', htmlspecialchars($ctx['claimer_id']))
          . _emailRow('Email', htmlspecialchars($ctx['email']));

    // --- TICKET section ---
    $rows .= _emailSectionHeader('Ticket')
           . _emailRow('Event', htmlspecialchars($ctx['event_title']))
           . _emailRow('Event Date & Time', $eventDateLabel)
           . _emailRow('Item', htmlspecialchars($ctx['item_description'] ?: $ctx['category']));

    if (!empty($ctx['venue_section'])) {
        $rows .= _emailRow('Seat Section', htmlspecialchars($ctx['venue_section']));
    }

    $rows .= _emailRow('Claim Time Slot', $slotLabel)
           . _emailRow('Quantity', (string) $ctx['quantity'])
           . _emailRow('Reservation ID', '#' . htmlspecialchars($ctx['reservation_id']));

    // --- PAYMENT section ---
    $rows .= _emailSectionHeader('Payment')
           . _emailRow('Reference #', htmlspecialchars($ctx['reference_number']))
           . _emailRow('Payment Method', htmlspecialchars($methodLabel));

    if ($paidFromAccount) {
        $rows .= _emailRow($paidFromLabel, htmlspecialchars($paidFromAccount));
    }

    $rows .= _emailRow('Amount', '<span style="font-size:15px">₱' . number_format($total, 2) . '</span>')
           . _emailRow('Verified On', $verifiedAt);

    $subject = 'New Ticket Payment — ' . $ctx['event_title'] . ' (Ref #' . $ctx['reference_number'] . ')';

    foreach ($organizers as $org) {
        if (!isDlsuEmail($org['email'])) continue; // dlsu only

        $body = _emailWrapper(
            'New Payment Received',
            'Hi ' . htmlspecialchars($org['first_name']) . ', a payment just came in for one of your events.',
            $rows,
            '<strong>Note:</strong> AnimoClaim doesn\'t connect to GCash or your bank directly — this payment was confirmed by the student via a one-time email code, not verified against an actual transaction. Please cross-check the reference number and account above against your real GCash/bank records before treating it as final, especially for higher-value tickets.'
        );
        sendMail($org['email'], $org['first_name'], $subject, $body);
    }
}