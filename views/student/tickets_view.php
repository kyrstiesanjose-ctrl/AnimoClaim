<?php require_once '../includes/header.php'; ?>

<div class="space-y-5">

    <!-- Top Greeting Header -->
    <div class="flex items-center justify-between px-1">
        <div>
            <h2 class="text-2xl wise-heading text-[#0e0f0c]">
                Hi, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Student'); ?>
            </h2>
            <p class="text-xs text-gray-500 font-semibold mt-1">DLSU Giveaway Wallet & Claims Pass</p>
        </div>
        <div class="w-11 h-11 rounded-full bg-[#0e0f0c] text-[#9fe870] flex items-center justify-center font-black text-sm flex-shrink-0">
            <?php echo strtoupper(substr($_SESSION['first_name'] ?? 'S', 0, 1)); ?>
        </div>
    </div>

    <!-- Green Wallet Balance Hero Card -->
    <div class="bg-[#0e0f0c] rounded-[36px] p-6 text-white relative overflow-hidden border border-[#0e0f0c]/12 space-y-4">
        <!-- Background Glow Deco -->
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-[#9fe870]/20 rounded-full blur-3xl pointer-events-none"></div>

        <div>
            <span class="text-xs font-bold text-[#9fe870] uppercase tracking-wider">Ready to claim</span>
            <div class="text-4xl sm:text-5xl wise-heading text-[#9fe870] mt-2">
                <?php echo count($claims); ?> <?php echo count($claims) === 1 ? 'ticket' : 'tickets'; ?>
            </div>
            <p class="text-xs text-white/70 font-semibold mt-2">
                1 more unlocks this week — check active giveaways
            </p>
        </div>

        <div class="pt-1">
            <button onclick="toggleRfidModal(true)" class="bg-[#9fe870] text-[#163300] text-xs font-bold px-5 py-3 rounded-full inline-flex items-center gap-2 wise-btn cursor-pointer">
                <i data-lucide="help-circle" class="w-4 h-4"></i>
                <span>How claiming works</span>
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar -mx-4 px-4 md:mx-0 md:px-0 py-1">
        <button class="claim-filter-tab h-10 px-6 rounded-full text-xs font-bold uppercase tracking-wider transition-all wise-btn bg-[#0e0f0c] text-[#9fe870] flex-shrink-0 cursor-pointer" data-filter="all">
            All
        </button>
        <button class="claim-filter-tab h-10 px-6 rounded-full text-xs font-bold uppercase tracking-wider transition-all wise-btn bg-white text-[#0e0f0c] border border-[#0e0f0c]/12 hover:bg-gray-50 flex-shrink-0 cursor-pointer" data-filter="ready">
            Ready to claim (<?php echo count($claims); ?>)
        </button>
        <button class="claim-filter-tab h-10 px-6 rounded-full text-xs font-bold uppercase tracking-wider transition-all wise-btn bg-white text-[#0e0f0c] border border-[#0e0f0c]/12 hover:bg-gray-50 flex-shrink-0 cursor-pointer" data-filter="claimed">
            Claimed (2)
        </button>
        <button class="claim-filter-tab h-10 px-6 rounded-full text-xs font-bold uppercase tracking-wider transition-all wise-btn bg-white text-[#0e0f0c] border border-[#0e0f0c]/12 hover:bg-gray-50 flex-shrink-0 cursor-pointer" data-filter="expired">
            Expired (0)
        </button>
    </div>

    <!-- Pending Payment (paid tickets like UAAP that still need payment confirmed) -->
    <?php if (!empty($pendingPayments)): ?>
    <div class="claim-section space-y-3">
        <div class="flex justify-between items-center px-1">
            <h3 class="text-xs font-extrabold text-[#b45309] uppercase tracking-widest">
                Pending Payment
            </h3>
            <span class="text-[10px] text-[#b45309]/70 font-bold"><?php echo count($pendingPayments); ?> Awaiting</span>
        </div>

        <?php foreach ($pendingPayments as $pp): ?>
            <div class="bg-[#fff7ed] rounded-[28px] p-5 border border-[#fdba74] flex items-center justify-between gap-3">
                <div class="space-y-1.5 pr-2 min-w-0">
                    <h4 class="font-extrabold text-base text-[#0e0f0c] leading-tight truncate">
                        <?php echo htmlspecialchars($pp['title']); ?>
                    </h4>
                    <p class="text-xs text-gray-500 font-semibold flex items-center gap-1.5">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400"></i>
                        <span class="truncate"><?php echo htmlspecialchars($pp['location']); ?></span>
                    </p>
                    <p class="text-xs font-bold text-[#b45309]">
                        ₱<?php echo number_format(((float)$pp['price']) * ((int)$pp['quantity']), 2); ?> due
                    </p>
                </div>
                <a href="checkout.php?reservation_id=<?php echo (int)$pp['reservation_id']; ?>"
                   class="flex-shrink-0 h-11 px-5 bg-[#0e0f0c] text-[#9fe870] rounded-full text-[11px] font-black uppercase tracking-wider flex items-center gap-1.5 wise-btn">
                    Pay Now <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Section 1: READY TO CLAIM -->
    <div id="section-ready" class="claim-section space-y-3">
        <div class="flex justify-between items-center px-1">
            <h3 class="text-xs font-extrabold text-gray-500 uppercase tracking-widest">
                READY TO CLAIM
            </h3>
            <span class="text-[10px] text-gray-400 font-bold"><?php echo count($claims); ?> Reserved</span>
        </div>

        <?php if (empty($claims)): ?>
            <div class="bg-white border border-[#0e0f0c]/12 rounded-[28px] p-6 text-center space-y-2">
                <p class="text-xs text-gray-500 font-semibold">No tickets currently ready to claim.</p>
                <a href="/claim/student/index.php" class="inline-block text-xs font-bold text-[#0e0f0c] underline">
                    Browse Active Drops &rarr;
                </a>
            </div>
        <?php else: ?>
            <?php foreach($claims as $claim): ?>
                <div 
                    onclick="toggleRfidModal(true)"
                    class="bg-white rounded-[28px] p-5 border border-[#0e0f0c]/12 transition-all cursor-pointer flex items-center justify-between group wise-btn"
                >
                    <div class="space-y-1.5 pr-3">
                        <h4 class="font-extrabold text-base text-[#0e0f0c] leading-tight">
                            <?php echo htmlspecialchars($claim['title']); ?>
                        </h4>
                        <p class="text-xs text-gray-500 font-semibold flex items-center gap-1.5">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400"></i>
                            <span><?php echo htmlspecialchars($claim['location']); ?></span>
                        </p>
                        <div class="pt-1">
                            <span class="inline-block px-3 py-1 bg-[#e2f6d5] text-[#163300] border border-[#9fe870] rounded-full text-[10px] font-bold uppercase tracking-wider">
                                <?php echo isset($claim['start_time']) ? date('M d, Y h:i A', strtotime($claim['start_time'])) : 'TBA'; ?>
                            </span>
                        </div>
                    </div>

                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-[#0e0f0c] group-hover:text-[#9fe870] transition-all flex-shrink-0">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Section 2: CLAIMED (Mocked for visual layout) -->
    <div id="section-claimed" class="claim-section space-y-3 pt-2">
        <div class="flex justify-between items-center px-1">
            <h3 class="text-xs font-extrabold text-gray-500 uppercase tracking-widest">
                CLAIMED
            </h3>
            <span class="text-[10px] text-gray-400 font-bold">2 Fulfilled</span>
        </div>

        <div class="bg-white rounded-[28px] p-4 border border-gray-200 flex items-center gap-4 opacity-90">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 overflow-hidden flex-shrink-0 relative">
                <img src="/claim/assets/pictures/archers_kitchen.jpg" class="w-full h-full object-cover" onerror="this.src='/claim/assets/pictures/Event_Poster.png'" />
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-bold text-sm text-[#0e0f0c] truncate">Archer's Kitchen</h4>
                <p class="text-xs text-gray-500 font-medium truncate mt-0.5">SJ Walk</p>
                <div class="mt-1">
                    <span class="inline-block px-2.5 py-0.5 bg-gray-100 text-gray-600 rounded-full text-[10px] font-bold">
                        Claimed 2026 07 15
                    </span>
                </div>
            </div>
            <div class="w-8 h-8 rounded-full bg-[#e2f6d5] text-[#163300] flex items-center justify-center flex-shrink-0 font-black">
                <i data-lucide="check" class="w-4 h-4"></i>
            </div>
        </div>

        <div class="bg-white rounded-[28px] p-4 border border-gray-200 flex items-center gap-4 opacity-90">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 overflow-hidden flex-shrink-0 relative">
                <img src="/claim/assets/pictures/Event_Poster.png" class="w-full h-full object-cover" onerror="this.src='/claim/assets/pictures/Event_Poster.png'" />
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-bold text-sm text-[#0e0f0c] truncate">Tomo Coffee Free Upsize</h4>
                <p class="text-xs text-gray-500 font-medium truncate mt-0.5">Gokongwei Hall</p>
                <div class="mt-1">
                    <span class="inline-block px-2.5 py-0.5 bg-gray-100 text-gray-600 rounded-full text-[10px] font-bold">
                        Claimed 2026 07 16
                    </span>
                </div>
            </div>
            <div class="w-8 h-8 rounded-full bg-[#e2f6d5] text-[#163300] flex items-center justify-center flex-shrink-0 font-black">
                <i data-lucide="check" class="w-4 h-4"></i>
            </div>
        </div>
    </div>

    <!-- Section 3: EXPIRED -->
    <div id="section-expired" class="claim-section space-y-3 pt-2">
        <div class="flex justify-between items-center px-1">
            <h3 class="text-xs font-extrabold text-gray-500 uppercase tracking-widest">
                EXPIRED
            </h3>
        </div>
        <div class="bg-white border border-[#0e0f0c]/12 rounded-[28px] p-5 text-center">
            <p class="text-xs text-gray-400 font-bold">No expired items.</p>
        </div>
    </div>

</div>

<style>
    @keyframes modalSlideUp {
        0% {
            transform: translateY(40px) scale(0.96);
            opacity: 0;
        }
        100% {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }
    @keyframes frontCardTapMotion {
        0% {
            transform: translateY(65px) scale(0.85) rotate(-3deg);
            opacity: 0.85;
            filter: drop-shadow(0 20px 15px rgba(0,0,0,0.6));
        }
        28% {
            transform: translateY(20px) scale(0.92) rotate(-1deg);
            opacity: 0.95;
            filter: drop-shadow(0 15px 10px rgba(0,0,0,0.5));
        }
        42% {
            transform: translateY(-56px) scale(1.05) rotate(0deg);
            opacity: 1;
            filter: drop-shadow(0 0 25px rgba(159, 232, 112, 0.95));
        }
        58% {
            transform: translateY(-54px) scale(1.04) rotate(0deg);
            opacity: 1;
            filter: drop-shadow(0 0 20px rgba(159, 232, 112, 0.85));
        }
        80% {
            transform: translateY(25px) scale(0.9) rotate(-2deg);
            opacity: 0.9;
            filter: drop-shadow(0 18px 12px rgba(0,0,0,0.5));
        }
        100% {
            transform: translateY(65px) scale(0.85) rotate(-3deg);
            opacity: 0.85;
            filter: drop-shadow(0 20px 15px rgba(0,0,0,0.6));
        }
    }

    @keyframes targetPulseBurst {
        0%, 38% {
            transform: scale(0.6);
            opacity: 0;
        }
        42% {
            transform: scale(1.1);
            opacity: 0.95;
        }
        60% {
            transform: scale(1.6);
            opacity: 0;
        }
        100% {
            transform: scale(0.6);
            opacity: 0;
        }
    }

    @keyframes checkmarkFlash {
        0%, 40% {
            transform: scale(0.3);
            opacity: 0;
        }
        43%, 62% {
            transform: scale(1);
            opacity: 1;
        }
        68%, 100% {
            transform: scale(0.7);
            opacity: 0;
        }
    }

    @keyframes terminalScreenGlow {
        0%, 38% {
            border-color: rgba(159, 232, 112, 0.25);
            box-shadow: 0 0 0 rgba(0,0,0,0);
        }
        42%, 62% {
            border-color: rgba(159, 232, 112, 1);
            box-shadow: 0 0 25px rgba(159, 232, 112, 0.5);
        }
        70%, 100% {
            border-color: rgba(159, 232, 112, 0.25);
            box-shadow: 0 0 0 rgba(0,0,0,0);
        }
    }

    .modal-card-animate {
        animation: modalSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .rfid-card-front-tap {
        animation: frontCardTapMotion 2.2s cubic-bezier(0.25, 1, 0.5, 1) infinite;
    }
    .rfid-target-pulse {
        animation: targetPulseBurst 2.2s ease-out infinite;
    }
    .rfid-target-pulse-outer {
        animation: targetPulseBurst 2.2s ease-out infinite;
        animation-delay: 0.1s;
    }
    .rfid-checkmark-anim {
        animation: checkmarkFlash 2.2s ease-out infinite;
    }
    .rfid-screen-anim {
        animation: terminalScreenGlow 2.2s ease-in-out infinite;
    }
</style>

<!-- RFID Tap Bottom Sheet Modal -->
<div id="rfidModal" onclick="if(event.target === this) toggleRfidModal(false)" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto transition-all duration-300">
    <div id="modalSheet" class="modal-card-content bg-white rounded-[28px] sm:rounded-[36px] w-full max-w-sm p-5 sm:p-7 text-center shadow-2xl relative space-y-4 border border-[#0e0f0c]/12 max-h-[90vh] overflow-y-auto my-auto">
        
        <!-- Header Bar -->
        <div class="flex items-center justify-between pb-1 -mt-1 border-b border-gray-100">
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-[#9fe870]"></span>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">RFID Instructions</span>
            </div>
            <button onclick="toggleRfidModal(false)" class="text-gray-400 hover:text-gray-700 p-1 rounded-full transition-colors cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- RFID Scanner Wall Terminal -->
        <div class="relative w-full max-w-[310px] h-80 mx-auto bg-gradient-to-b from-[#061209] via-[#091e10] to-[#040c06] rounded-3xl p-3.5 flex flex-col items-center justify-between border border-[#163300]/40 shadow-inner overflow-hidden">
            
            <div class="relative w-full bg-[#0d2615] border border-[#9fe870]/30 rounded-2xl p-2.5 shadow-xl flex flex-col items-center gap-2 rfid-screen-anim transition-all z-10">
                <div class="w-full bg-[#051108] rounded-xl py-1 px-2.5 border border-[#9fe870]/20 flex items-center justify-between font-mono">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#9fe870] animate-pulse"></span>
                        <span class="text-[9px] font-bold text-[#9fe870] tracking-wider uppercase">GATEWAY #04</span>
                    </div>
                    <span class="text-[9px] font-extrabold text-[#9fe870] uppercase tracking-widest">TAP ID CARD</span>
                </div>

                <div class="relative w-24 h-24 rounded-full border-2 border-dashed border-[#9fe870]/50 flex items-center justify-center bg-[#07190d] my-0.5">
                    <div class="absolute inset-0 rounded-full bg-[#9fe870]/30 rfid-target-pulse pointer-events-none"></div>
                    <div class="absolute -inset-4 rounded-full bg-[#9fe870]/15 rfid-target-pulse-outer pointer-events-none"></div>

                    <div class="flex flex-col items-center text-[#9fe870] gap-0.5 z-10">
                        <i data-lucide="wifi" class="w-7 h-7 rotate-45 text-[#9fe870]"></i>
                        <span class="text-[8px] font-mono font-black tracking-widest text-[#9fe870]/90 uppercase">TAP HERE</span>
                    </div>

                    <div class="absolute inset-0 bg-[#9fe870] rounded-full flex items-center justify-center text-[#0e0f0c] rfid-checkmark-anim z-20 shadow-[0_0_25px_#9fe870]">
                        <i data-lucide="check" class="w-10 h-10 stroke-[3]"></i>
                    </div>
                </div>
            </div>

            <!-- DLSU Student ID Card Animation (Updated to image_85c9fa.png) -->
            <div class="absolute bottom-2 z-30 rfid-card-front-tap flex flex-col items-center justify-center pointer-events-none">
                <div class="w-36 sm:w-40 h-[220px] rounded-[20px] shadow-2xl border border-white/30 overflow-hidden flex items-center justify-center bg-[#074f26]">
                    <img 
                        src="/claim/assets/pictures/image_85c9fa.png" 
                        alt="DLSU Student ID" 
                        class="w-full h-full object-contain select-none"
                        onerror="this.src='https://placehold.co/200x300/155d27/c6f135?text=DLSU+ID'"
                    />
                </div>
            </div>

            <div class="relative z-40 w-full bg-[#051108]/90 backdrop-blur-xs rounded-xl p-1.5 px-3 border border-[#9fe870]/20 flex items-center justify-between text-[9px] text-gray-300 font-mono">
                <span class="flex items-center gap-1 font-bold text-[#9fe870]">
                    <i data-lucide="nfc" class="w-3 h-3"></i> NFC Contactless
                </span>
                <span class="text-[#9fe870] font-extrabold uppercase tracking-widest">RFID READY</span>
            </div>
        </div>

        <div class="space-y-1.5 text-center">
            <h3 class="text-xl sm:text-2xl wise-heading text-[#0e0f0c]">
            </h3>
            <p class="text-xs text-gray-600 font-semibold leading-relaxed max-w-xs mx-auto">
                Just tap your DLSU ID at the giveaway counter — the organizer's scanner reads your card and marks your ticket claimed automatically.
            </p>
            <p class="text-[11px] text-gray-400 font-medium leading-snug max-w-xs mx-auto">
                Your reservation stays "Ready to claim" until you tap in. No screenshots, no codes to lose.
            </p>
        </div>

        <div class="pt-1">
            <button onclick="toggleRfidModal(false)" class="w-full h-12 bg-[#9fe870] hover:bg-[#8edb5f] text-[#163300] font-black text-xs uppercase tracking-wider rounded-full shadow-md hover:shadow-lg transition-all wise-btn cursor-pointer flex items-center justify-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                Got it
            </button>
        </div>
    </div>
</div>

<script>
    function toggleRfidModal(show) {
        const modal = document.getElementById('rfidModal');
        const content = document.getElementById('modalSheet');
        if (show) {
            modal.classList.remove('hidden');
            if (content) {
                content.classList.remove('modal-card-animate');
                void content.offsetWidth; 
                content.classList.add('modal-card-animate');
            }
        } else {
            modal.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();

        const filterTabs = document.querySelectorAll('.claim-filter-tab');
        const readySec = document.getElementById('section-ready');
        const claimedSec = document.getElementById('section-claimed');
        const expiredSec = document.getElementById('section-expired');

        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const filter = tab.getAttribute('data-filter');

                filterTabs.forEach(t => {
                    t.classList.remove('bg-[#0e0f0c]', 'text-[#9fe870]');
                    t.classList.add('bg-white', 'text-[#0e0f0c]', 'border', 'border-[#0e0f0c]/12');
                });

                tab.classList.remove('bg-white', 'text-[#0e0f0c]', 'border', 'border-[#0e0f0c]/12');
                tab.classList.add('bg-[#0e0f0c]', 'text-[#9fe870]');

                if (filter === 'all') {
                    if (readySec) readySec.style.display = '';
                    if (claimedSec) claimedSec.style.display = '';
                    if (expiredSec) expiredSec.style.display = '';
                } else if (filter === 'ready') {
                    if (readySec) readySec.style.display = '';
                    if (claimedSec) claimedSec.style.display = 'none';
                    if (expiredSec) expiredSec.style.display = 'none';
                } else if (filter === 'claimed') {
                    if (readySec) readySec.style.display = 'none';
                    if (claimedSec) claimedSec.style.display = '';
                    if (expiredSec) expiredSec.style.display = 'none';
                } else if (filter === 'expired') {
                    if (readySec) readySec.style.display = 'none';
                    if (claimedSec) claimedSec.style.display = 'none';
                    if (expiredSec) expiredSec.style.display = '';
                }
            });
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>