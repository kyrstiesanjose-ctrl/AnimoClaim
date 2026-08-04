<?php require_once '../includes/header.php'; ?>

<div class="max-w-xl mx-auto space-y-5">

    <!-- Back link -->
    <a href="/claim/student/tickets.php" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-[#163300]">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to My Tickets
    </a>

    <div class="flex items-center justify-between px-1">
        <h2 class="text-2xl wise-heading text-[#0e0f0c]">Confirm Payment</h2>
    </div>

    <!-- Order Summary Card -->
    <div class="bg-[#0e0f0c] rounded-[28px] p-6 text-white relative overflow-hidden border border-[#0e0f0c]/12">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-[#9fe870]/20 rounded-full blur-3xl pointer-events-none"></div>
        <span class="text-xs font-bold text-[#9fe870] uppercase tracking-wider">Ticket</span>
        <h3 class="text-xl font-black mt-1"><?php echo htmlspecialchars($reservation['title']); ?></h3>
        <p class="text-xs text-white/60 font-semibold mt-1"><?php echo htmlspecialchars($reservation['description'] ?: $reservation['category']); ?></p>

        <div class="mt-4 pt-4 border-t border-white/10 space-y-1.5 text-xs text-white/70">
            <div class="flex items-center gap-2">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-[#9fe870]"></i>
                <?php echo htmlspecialchars($reservation['location']); ?>
            </div>
            <div class="flex items-center gap-2">
                <i data-lucide="clock" class="w-3.5 h-3.5 text-[#9fe870]"></i>
                <?php echo date('M d, Y', strtotime($reservation['start_time'])); ?> ·
                <?php echo date('h:i A', strtotime($reservation['start_time'])) . ' - ' . date('h:i A', strtotime($reservation['end_time'])); ?>
            </div>
            <div class="flex items-center gap-2">
                <i data-lucide="ticket" class="w-3.5 h-3.5 text-[#9fe870]"></i>
                Quantity: <?php echo (int) $reservation['quantity']; ?>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between">
            <span class="text-xs font-bold text-white/70 uppercase tracking-wider">Total Amount</span>
            <span class="text-2xl font-black text-[#9fe870]">₱<?php echo number_format($total_amount, 2); ?></span>
        </div>
    </div>

    <!-- STEP 1: Payment details form -->
    <div id="step-details" class="bg-white rounded-[28px] p-6 border border-[#0e0f0c]/12 space-y-4">
        <h3 class="text-sm font-extrabold text-[#0e0f0c] uppercase tracking-wide">Payment Details</h3>
        <p class="text-xs text-gray-500 font-semibold -mt-2">
            Choose how you're paying and confirm with an OTP sent to your DLSU email.
        </p>

        <div>
            <label class="text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Payment Method</label>
            <select id="payment_method" onchange="updateAccountLabel()" class="mt-1.5 w-full h-12 px-4 rounded-2xl border border-[#0e0f0c]/12 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#9fe870]">
                <option value="gcash">GCash</option>
                <option value="bank_transfer">Bank Transfer</option>
            </select>
        </div>

        <div>
            <label id="account_label" class="text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">GCash Number</label>
            <input type="text" id="account_number" placeholder="09XXXXXXXXX"
                class="mt-1.5 w-full h-12 px-4 rounded-2xl border border-[#0e0f0c]/12 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#9fe870]">
            <p id="account_hint" class="text-[10px] text-gray-400 font-semibold mt-1">The GCash number you'll pay from. We'll send an OTP to authorize the payment.</p>
        </div>

        <p id="details-error" class="text-xs font-bold text-red-500 hidden"></p>

        <button id="btn-send-otp" onclick="requestOtp()"
            class="w-full h-12 bg-[#9fe870] hover:bg-[#8edb5f] text-[#163300] font-black text-xs uppercase tracking-wider rounded-full shadow-md transition-all wise-btn flex items-center justify-center gap-2">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            <span>Confirm Payment</span>
        </button>
    </div>

    <!-- STEP 2: OTP entry (hidden until code is sent) -->
    <div id="step-otp" class="hidden bg-white rounded-[28px] p-6 border border-[#0e0f0c]/12 space-y-4">
        <h3 class="text-sm font-extrabold text-[#0e0f0c] uppercase tracking-wide">Enter OTP</h3>
        <p id="otp-sent-message" class="text-xs text-gray-500 font-semibold -mt-2"></p>

        <input type="text" id="otp_code" maxlength="6" inputmode="numeric" placeholder="6-digit code"
            class="w-full h-14 px-4 rounded-2xl border border-[#0e0f0c]/12 text-center text-2xl font-black tracking-[0.5em] focus:outline-none focus:ring-2 focus:ring-[#9fe870]">

        <p id="otp-error" class="text-xs font-bold text-red-500 hidden"></p>

        <button id="btn-verify-otp" onclick="verifyOtp()"
            class="w-full h-12 bg-[#9fe870] hover:bg-[#8edb5f] text-[#163300] font-black text-xs uppercase tracking-wider rounded-full shadow-md transition-all wise-btn flex items-center justify-center gap-2">
            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
            <span>Verify & Confirm Payment</span>
        </button>

        <button onclick="requestOtp(true)" class="w-full text-center text-xs font-bold text-gray-500 hover:text-[#163300] py-1">
            Didn't get it? Resend code
        </button>
    </div>

    <!-- STEP 3: Success -->
    <div id="step-success" class="hidden bg-[#e2f6d5] rounded-[28px] p-8 border border-[#9fe870] text-center space-y-2">
        <div class="w-14 h-14 mx-auto rounded-full bg-[#9fe870] flex items-center justify-center">
            <i data-lucide="check" class="w-7 h-7 text-[#163300]"></i>
        </div>
        <h3 class="text-lg font-black text-[#163300]">Payment Confirmed!</h3>
        <p id="success-message" class="text-xs text-[#163300]/70 font-semibold"></p>
        <p class="text-[11px] text-[#163300]/50 font-medium">Redirecting to My Tickets…</p>
    </div>

</div>

<script>
const RESERVATION_ID = <?php echo (int) $reservation['reservation_id']; ?>;
let sendingOtp = false;

function setBtnLoading(btn, loading, label) {
    btn.disabled = loading;
    btn.querySelector('span').textContent = loading ? 'Please wait…' : label;
    btn.classList.toggle('opacity-60', loading);
}

async function requestOtp(isResend = false) {
    if (sendingOtp) return;
    sendingOtp = true;

    const errEl = document.getElementById('details-error');
    errEl.classList.add('hidden');

    const payment_method = document.getElementById('payment_method').value;
    const account_number = document.getElementById('account_number').value.trim();

    if (!isResend && !account_number) {
        errEl.textContent = payment_method === 'gcash'
            ? 'Please enter the GCash number you\'ll pay from.'
            : 'Please enter your bank account number.';
        errEl.classList.remove('hidden');
        sendingOtp = false;
        return;
    }

    const btn = document.getElementById('btn-send-otp');
    setBtnLoading(btn, true, 'Confirm Payment');

    try {
        const res = await fetch('/claim/api/payment/request_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                reservation_id: RESERVATION_ID,
                payment_method,
                account_number,
                csrf_token: csrfToken
            })
        });
        const data = await res.json();

        if (!data.success) {
            errEl.textContent = data.message || 'Could not send OTP. Please try again.';
            errEl.classList.remove('hidden');
            document.getElementById('otp-error').classList.add('hidden');
            return;
        }

        document.getElementById('step-details').classList.add('hidden');
        document.getElementById('step-otp').classList.remove('hidden');
        document.getElementById('otp-sent-message').textContent = data.message;
        document.getElementById('otp_code').focus();

    } catch (e) {
        errEl.textContent = 'Network error. Please try again.';
        errEl.classList.remove('hidden');
    } finally {
        setBtnLoading(btn, false, 'Confirm Payment');
        sendingOtp = false;
    }
}

function updateAccountLabel() {
    const method = document.getElementById('payment_method').value;
    const label = document.getElementById('account_label');
    const input = document.getElementById('account_number');
    const hint = document.getElementById('account_hint');

    if (method === 'gcash') {
        label.textContent = 'GCash Number';
        input.placeholder = '09XXXXXXXXX';
        hint.textContent = "The GCash number you'll pay from. We'll send an OTP to authorize the payment.";
    } else {
        label.textContent = 'Bank Account Number';
        input.placeholder = 'e.g. 001234567890';
        hint.textContent = "The bank account you'll transfer from. We'll send an OTP to authorize the payment.";
    }
}

async function verifyOtp() {
    const errEl = document.getElementById('otp-error');
    errEl.classList.add('hidden');

    const otp_code = document.getElementById('otp_code').value.trim();
    if (!/^\d{6}$/.test(otp_code)) {
        errEl.textContent = 'Enter the 6-digit code sent to your email.';
        errEl.classList.remove('hidden');
        return;
    }

    const btn = document.getElementById('btn-verify-otp');
    setBtnLoading(btn, true, 'Verify & Confirm Payment');

    try {
        const res = await fetch('/claim/api/payment/verify_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                reservation_id: RESERVATION_ID,
                otp_code,
                csrf_token: csrfToken
            })
        });
        const data = await res.json();

        if (!data.success) {
            errEl.textContent = data.message || 'Verification failed. Please try again.';
            errEl.classList.remove('hidden');
            return;
        }

        document.getElementById('step-otp').classList.add('hidden');
        document.getElementById('step-success').classList.remove('hidden');
        document.getElementById('success-message').textContent = data.message;

        setTimeout(() => { window.location.href = '/claim/student/tickets.php'; }, 2500);

    } catch (e) {
        errEl.textContent = 'Network error. Please try again.';
        errEl.classList.remove('hidden');
    } finally {
        setBtnLoading(btn, false, 'Verify & Confirm Payment');
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
