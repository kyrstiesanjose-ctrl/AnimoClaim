<?php require_once '../includes/header.php'; ?>

<!-- Confetti Animation Library -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>

<style>
    .wise-btn { transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.15s ease; }
    .wise-btn:hover { transform: scale(1.05); }
    .wise-btn:active { transform: scale(0.95); }
    .wise-heading { font-weight: 900; line-height: 0.88; letter-spacing: -0.03em; }
</style>

<!-- Custom Header Navigation -->
<header class="fixed top-0 left-0 right-0 h-14 bg-[#0e0f0c] flex items-center justify-between px-3.5 sm:px-4 z-50 rounded-b-2xl border-b border-white/10 shadow-sm md:left-72">
    <a href="/claim/student/index.php" class="text-white hover:text-[#9fe870] transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 wise-btn text-[11px] font-extrabold uppercase tracking-wider">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-[#9fe870]"></i>
        <span>Back to Events</span>
    </a>
    <span class="text-[10px] font-extrabold text-[#9fe870] uppercase tracking-widest bg-white/5 px-3 py-1 rounded-full border border-white/10">
        <?php echo htmlspecialchars($event['category'] ?? 'Event'); ?>
    </span>
</header>

<main class="max-w-md mx-auto px-4 pt-[72px] pb-10 space-y-5 animate-fade-in">
    
    <?php 
        $display_date = "TBA";
        $display_time = "TBA";
        if (!empty($time_slots)) {
            $first_slot = strtotime($time_slots[0]['start_time']);
            $display_date = date('M d, Y', $first_slot);
            $display_time = date('h:i A', $first_slot) . " onwards";
        }
    ?>

    <!-- Event Poster Hero Card -->
    <section id="event-hero-card" class="bg-white rounded-[32px] overflow-hidden border border-[#0e0f0c]/12 relative transition-all duration-300">
        <div class="h-60 w-full relative bg-[#e2f6d5]">
            <img src="/claim/assets/pictures/<?php echo htmlspecialchars($event['image_url'] ?: 'Event_Poster.png'); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($event['title']); ?>" onerror="this.src='/claim/assets/pictures/Event_Poster.png'" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
            
            <div class="absolute top-3.5 left-3.5 z-10 flex gap-2">
                <span class="bg-[#9fe870] text-[#163300] font-extrabold text-[10px] px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                    <?php echo htmlspecialchars($event['category'] ?? 'Event'); ?>
                </span>
            </div>

            <div class="absolute bottom-3.5 left-3.5 right-3.5 z-10 text-white">
                <h1 class="text-xl sm:text-2xl wise-heading drop-shadow-md"><?php echo htmlspecialchars($event['title']); ?></h1>
            </div>
        </div>

        <!-- Capacity Indicator Bar -->
        <div id="hero-capacity-bar" class="p-4 bg-white border-t border-gray-100 flex items-center justify-between gap-4 transition-colors duration-300">
            <div class="flex items-center gap-2.5">
                <div id="hero-capacity-icon" class="w-10 h-10 rounded-2xl bg-[#e2f6d5] text-[#163300] flex items-center justify-center font-black transition-colors duration-300">
                    <i data-lucide="package-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p id="hero-capacity-label" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 transition-colors duration-300">Available Stock</p>
                    <p id="hero-capacity-val" class="text-base font-extrabold text-[#0e0f0c] transition-colors duration-300"><?php echo $remain_qty; ?> <span id="hero-capacity-sub" class="text-xs text-gray-400 font-normal">/ <?php echo $total_qty; ?> units</span></p>
                </div>
            </div>
            <div class="text-right">
                <span id="hero-capacity-badge" class="inline-block px-3.5 py-1 bg-[#9fe870] text-[#163300] rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm">
                    <?php echo $capacity_percent; ?>% Left
                </span>
            </div>
        </div>
    </section>

    <!-- Metadata Info Grid -->
    <section class="grid grid-cols-2 gap-3">
        <div class="bg-white p-4 rounded-[24px] border border-[#0e0f0c]/12 flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-[#e2f6d5] text-[#163300] flex items-center justify-center flex-shrink-0">
                <i data-lucide="calendar" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400 block">Date</span>
                <p class="font-bold text-[#0e0f0c] text-xs truncate"><?php echo $display_date; ?></p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-[24px] border border-[#0e0f0c]/12 flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-[#e2f6d5] text-[#163300] flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400 block">Time</span>
                <p class="font-bold text-[#0e0f0c] text-xs truncate"><?php echo $display_time; ?></p>
            </div>
        </div>

        <div class="col-span-2 bg-white p-4 rounded-[24px] border border-[#0e0f0c]/12 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-[#0e0f0c] text-[#9fe870] flex items-center justify-center flex-shrink-0">
                <i data-lucide="map-pin" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400 block">Pick Up Spot</span>
                <p class="font-extrabold text-[#0e0f0c] text-xs truncate"><?php echo htmlspecialchars($event['location']); ?></p>
            </div>
        </div>
    </section>

    <!-- Description Box -->
    <section class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 space-y-2">
        <h3 class="text-xs font-bold uppercase tracking-wider text-[#0e0f0c] flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4 text-[#163300]"></i>
            Giveaway Details & Mechanics
        </h3>
        <p class="text-gray-600 text-xs leading-relaxed font-semibold">
            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
        </p>
    </section>

    <!-- Wise Style Time Slot Picker -->
    <section class="space-y-3">
        <div class="flex justify-between items-center px-1">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#0e0f0c] flex items-center gap-1.5">
                <i data-lucide="timer" class="w-4 h-4 text-[#163300]"></i>
                Select Pickup Time Slot
            </h3>
            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Tap to select</span>
        </div>

        <?php if ($hasReservedEvent): ?>
            <div class="bg-[#e2f6d5] border-2 border-[#9fe870] p-4 rounded-2xl flex items-center justify-between gap-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#9fe870] text-[#163300] flex items-center justify-center font-bold flex-shrink-0">
                        <i data-lucide="ticket-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black text-[#163300]">Ticket Already Reserved!</p>
                        <p class="text-[11px] font-semibold text-[#163300]/80">You already locked in your slot for this giveaway.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-2 gap-3" id="time-slot-container">
            <?php if (empty($time_slots)): ?>
                <div class="col-span-2 text-center text-xs text-gray-500 py-6 bg-white rounded-2xl border border-dashed border-gray-300">
                    No time slots available for this release yet.
                </div>
            <?php else: ?>
                <?php foreach ($time_slots as $slot): 
                    $time_str = date('h:i A', strtotime($slot['start_time'])) . ' - ' . date('h:i A', strtotime($slot['end_time']));
                    $isFull = $slot['current_reservations'] >= $slot['max_capacity'];
                    $isAlreadyReserved = in_array($slot['id'], $userReservedSlots);
                    $spotsLeft = $slot['max_capacity'] - $slot['current_reservations'];
                    $capacityPct = $slot['max_capacity'] > 0 ? round(($slot['current_reservations'] / $slot['max_capacity']) * 100) : 0;
                    $isLowStock = !$isFull && !$isAlreadyReserved && ($spotsLeft <= 20 || $capacityPct >= 75);

                    $buttonClass = "bg-white border-2 border-[#0e0f0c]/12 text-[#0e0f0c] hover:border-[#9fe870] time-slot-btn relative overflow-hidden transition-all duration-200";
                    $statusBadge = "";

                    if ($isAlreadyReserved) {
                        $buttonClass = "bg-[#e2f6d5] border-2 border-[#9fe870] text-[#163300] cursor-not-allowed opacity-90";
                        $statusBadge = '<span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-[#163300] bg-[#9fe870]/40 px-2 py-0.5 rounded-full"><i data-lucide="check" class="w-3 h-3"></i> Reserved</span>';
                    } elseif ($isFull) {
                        $buttonClass = "bg-gray-100 border border-gray-200 text-gray-400 cursor-not-allowed opacity-60";
                        $statusBadge = '<span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">Sold Out</span>';
                    } elseif ($hasReservedEvent) {
                        $buttonClass = "bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed opacity-60";
                        $statusBadge = '<span class="text-[10px] font-bold text-gray-400">' . $spotsLeft . ' spots left</span>';
                    } elseif ($isLowStock) {
                        $statusBadge = '<span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full animate-pulse"><i data-lucide="flame" class="w-3 h-3 text-amber-600"></i> ' . $spotsLeft . ' left!</span>';
                    } else {
                        $statusBadge = '<span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full">' . $spotsLeft . ' spots left</span>';
                    }
                ?>
                    <button class="p-3.5 rounded-[22px] flex flex-col items-start gap-1.5 transition-all wise-btn cursor-pointer min-h-[82px] justify-between group <?php echo $buttonClass; ?>" 
                            data-slot-id="<?php echo $slot['id']; ?>"
                            <?php echo ($isFull || $hasReservedEvent || $isAlreadyReserved) ? 'disabled' : ''; ?>>
                        
                        <!-- Top Row: Time & Checkmark Icon -->
                        <div class="w-full flex items-center justify-between gap-1">
                            <span class="slot-time-text font-extrabold text-xs text-[#0e0f0c] transition-colors"><?php echo $time_str; ?></span>
                            <span class="slot-check-icon hidden w-5 h-5 rounded-full bg-[#9fe870] text-[#163300] items-center justify-center font-black flex-shrink-0 shadow-sm">
                                <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                            </span>
                        </div>

                        <!-- Spots Left Badge -->
                        <div class="w-full flex items-center justify-between">
                            <span class="slot-spots-text transition-colors"><?php echo $statusBadge; ?></span>
                        </div>

                        <!-- Scarcity Mini Progress Bar -->
                        <?php if (!$isFull && !$isAlreadyReserved): ?>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden mt-0.5">
                                <div class="h-full <?php echo $isLowStock ? 'bg-amber-500' : 'bg-[#9fe870]'; ?> rounded-full transition-all" style="width: <?php echo $capacityPct; ?>%;"></div>
                            </div>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <input type="hidden" id="selected_slot_id" name="slot_id" value="">
    </section>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="hidden fixed top-16 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-md bg-red-600 text-white p-3.5 rounded-2xl shadow-xl flex items-center justify-between gap-3 transition-all transform duration-300">
        <div class="flex items-center gap-2.5 text-xs font-bold">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
            <span id="toast-message">Error message</span>
        </div>
        <button onclick="hideToast()" class="text-white/80 hover:text-white p-1">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- CTA Action Button -->
    <section class="pt-2 pb-6">
        <?php if ($hasReservedEvent): ?>
            <a href="/claim/student/tickets.php" class="w-full bg-[#9fe870] text-[#163300] py-3.5 px-6 h-14 rounded-2xl font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2.5 wise-btn shadow-[0_4px_20px_rgba(159,232,112,0.5)]">
                <i data-lucide="qr-code" class="w-4 h-4"></i>
                <span>View My Reserved Ticket</span>
            </a>
        <?php else: ?>
            <button id="claim-btn" disabled class="w-full bg-gray-200 text-gray-400 py-3.5 px-6 h-14 rounded-2xl font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2.5 wise-btn disabled:opacity-50 disabled:bg-gray-200 disabled:text-gray-400 disabled:shadow-none disabled:cursor-not-allowed cursor-pointer transition-all duration-300">
                <i data-lucide="ticket" class="w-4 h-4"></i>
                <span id="claim-text">Select a Time Slot First</span>
            </button>
        <?php endif; ?>
        <p class="text-center text-[10px] text-gray-500 font-bold mt-2.5 uppercase tracking-widest">
            DLSU Verified • Non Transferable Ticket
        </p>
    </section>
</main>

<script>
    const csrfToken = "<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>";
    const isSuspended = <?php echo $isSuspended ? 'true' : 'false'; ?>;

    function showToast(msg) {
        const container = document.getElementById('toast-container');
        const messageEl = document.getElementById('toast-message');
        if (container && messageEl) {
            messageEl.innerText = msg;
            container.classList.remove('hidden');
            if (window.lucide) lucide.createIcons();
            setTimeout(() => hideToast(), 4000);
        }
    }

    function hideToast() {
        const container = document.getElementById('toast-container');
        if (container) container.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();

        const timeSlots = document.querySelectorAll('.time-slot-btn');
        const hiddenInput = document.getElementById('selected_slot_id');
        const claimBtn = document.getElementById('claim-btn');
        const claimText = document.getElementById('claim-text');

        timeSlots.forEach(slot => {
            slot.addEventListener('click', () => {
                if (slot.disabled) {
                    showToast("This time slot is unavailable or you already claimed a ticket.");
                    return;
                }
                
                // Reset all available slots
                timeSlots.forEach(s => {
                    if (!s.disabled) {
                        s.classList.remove('bg-[#163300]', 'text-white', 'border-[#9fe870]', 'ring-2', 'ring-[#9fe870]', 'scale-[1.02]');
                        s.classList.add('bg-white', 'border-[#0e0f0c]/12', 'text-[#0e0f0c]', 'opacity-60');
                        
                        const timeText = s.querySelector('.slot-time-text');
                        if (timeText) {
                            timeText.classList.remove('text-white');
                            timeText.classList.add('text-[#0e0f0c]');
                        }
                        const checkIcon = s.querySelector('.slot-check-icon');
                        if (checkIcon) {
                            checkIcon.classList.add('hidden');
                        }
                    }
                });
                
                // Highlight selected slot
                slot.classList.remove('bg-white', 'border-[#0e0f0c]/12', 'text-[#0e0f0c]', 'opacity-60');
                slot.classList.add('bg-[#163300]', 'text-white', 'border-[#9fe870]', 'ring-2', 'ring-[#9fe870]', 'scale-[1.02]', 'opacity-100');
                
                const selectedTimeText = slot.querySelector('.slot-time-text');
                if (selectedTimeText) {
                    selectedTimeText.classList.remove('text-[#0e0f0c]');
                    selectedTimeText.classList.add('text-white');
                }
                const checkIcon = slot.querySelector('.slot-check-icon');
                if (checkIcon) {
                    checkIcon.classList.remove('hidden');
                }

                // Turn Event Header Capacity Bar to dark theme accent
                const heroBar = document.getElementById('hero-capacity-bar');
                const heroVal = document.getElementById('hero-capacity-val');
                const heroIcon = document.getElementById('hero-capacity-icon');
                if (heroBar) {
                    heroBar.classList.remove('bg-white', 'border-gray-100');
                    heroBar.classList.add('bg-[#0e0f0c]', 'border-black/80', 'text-white');
                }
                if (heroVal) {
                    heroVal.classList.remove('text-[#0e0f0c]');
                    heroVal.classList.add('text-white');
                }
                if (heroIcon) {
                    heroIcon.classList.remove('bg-[#e2f6d5]', 'text-[#163300]');
                    heroIcon.classList.add('bg-white/10', 'text-[#9fe870]');
                }
                
                if (hiddenInput) hiddenInput.value = slot.getAttribute('data-slot-id');
                if (claimBtn) {
                    claimBtn.disabled = false;
                    claimBtn.classList.remove('bg-gray-200', 'text-gray-400');
                    claimBtn.classList.add('bg-[#9fe870]', 'text-[#163300]', 'shadow-[0_4px_20px_rgba(159,232,112,0.5)]', 'scale-[1.01]');
                }
                if (claimText) claimText.innerText = "Confirm Slot";
            });
        });

        if (claimBtn) {
            claimBtn.addEventListener('click', async () => {
                const slotId = hiddenInput ? hiddenInput.value : '';
                if(!slotId) return;

                if (isSuspended) {
                    showToast("Reservation Blocked: You have 3 active strikes on your DLSU account.");
                    return;
                }

                claimBtn.disabled = true;
                if (claimText) claimText.innerText = "Reserving Slot...";

                try {
                    const res = await fetch('/claim/api/book_slot.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ time_slot_id: parseInt(slotId, 10), csrf_token: csrfToken })
                    });
                    
                    const data = await res.json();
                    
                    if(data.success) {
                        if (window.confetti) {
                            confetti({
                                particleCount: 100,
                                spread: 70,
                                origin: { y: 0.6 }
                            });
                        }

                        claimBtn.classList.replace('bg-[#9fe870]', 'bg-green-600');
                        claimBtn.classList.replace('text-[#163300]', 'text-white');
                        if (claimText) claimText.innerText = "Claim Successful!";
                        
                        setTimeout(() => {
                            window.location.href = '/claim/student/tickets.php';
                        }, 1200);
                    } else {
                        showToast("Failed to book slot: " + data.message);
                        claimBtn.disabled = false;
                        if (claimText) claimText.innerText = "Confirm Slot";
                    }
                } catch (err) {
                    showToast("Network error occurred. Please try again.");
                    claimBtn.disabled = false;
                    if (claimText) claimText.innerText = "Confirm Slot";
                }
            });
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>