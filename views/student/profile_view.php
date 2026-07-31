<?php require_once '../includes/header.php'; ?>

<div class="space-y-5 animate-fade-in pb-10">
    <!-- Student Member Loyalty Card -->
    <div class="bg-[#0e0f0c] p-6 rounded-[36px] text-white border border-[#0e0f0c]/12 relative overflow-hidden shadow-lg">
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#9fe870]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center gap-4 relative z-10">
            <div class="w-16 h-16 rounded-full bg-[#9fe870] text-[#163300] flex items-center justify-center font-black text-xl flex-shrink-0 shadow-inner">
                <?php echo strtoupper(substr($currentUser['first_name'] ?? 'S', 0, 1)); ?>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-xl wise-heading text-white truncate"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h1>
                    <span class="bg-[#9fe870] text-[#163300] text-[9px] font-bold px-2.5 py-0.5 rounded-full uppercase">
                        DLSU Verified
                    </span>
                </div>
                <p class="text-[#9fe870] font-bold text-xs tracking-wider uppercase mt-1">
                    <?php echo htmlspecialchars($currentUser['program'] ?? 'BS IT Student'); ?>
                </p>
                <p class="text-white/40 text-[10px] font-medium mt-1">
                    ID: <?php echo htmlspecialchars($currentUser['email'] ?? 'student@dlsu.edu.ph'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-2xl bg-[#e2f6d5] text-[#163300] flex items-center justify-center font-black">
                <i data-lucide="ticket" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Bookings</p>
                <p class="text-xl wise-heading text-[#0e0f0c] mt-0.5"><?php echo count($allReservations); ?></p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black">
                <i data-lucide="shield-alert" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Active Strikes</p>
                <p class="text-xl wise-heading text-amber-600 mt-0.5"><?php echo $strikesCount; ?> <span class="text-xs text-gray-400 font-normal">/ 3</span></p>
            </div>
        </div>
    </div>

    <!-- Active Strikes Alert -->
    <?php if ($strikesCount > 0): ?>
        <div class="flex gap-3 bg-amber-50 border border-amber-200 p-4 rounded-[24px] items-start shadow-sm">
            <i data-lucide="shield-alert" class="text-amber-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="text-xs font-bold text-amber-950">Active Strikes: <?php echo $strikesCount; ?> of 3</p>
                <p class="text-[11px] text-amber-800 leading-snug mt-0.5 font-medium">Students with 3 unexcused no-shows will experience temporary booking holds. Ensure you arrive at pickup slots on time!</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Pending Claims Section -->
    <div class="space-y-3">
        <h2 class="text-xs font-bold text-[#0e0f0c] tracking-wider uppercase flex items-center gap-2">
            <i data-lucide="clock" class="w-4 h-4 text-[#163300]"></i> Pending Claim Passes
        </h2>
        <div class="space-y-2">
            <?php if (empty($activeReservations)): ?>
                <div class="p-5 bg-white rounded-[24px] border border-[#0e0f0c]/12 text-center shadow-sm">
                    <p class="text-gray-500 text-xs font-semibold">No pending claim passes active.</p>
                </div>
            <?php else: ?>
                <?php foreach($activeReservations as $claim): ?>
                    <a href="/claim/student/tickets.php" class="flex items-center justify-between p-4 bg-white rounded-[28px] border border-[#0e0f0c]/12 hover:border-[#9fe870] wise-btn cursor-pointer shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#e2f6d5] text-[#163300] flex items-center justify-center">
                                <i data-lucide="ticket" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-xs text-[#0e0f0c]"><?php echo htmlspecialchars($claim['title']); ?></h3>
                                <p class="text-[10px] text-gray-500 font-semibold mt-0.5"><?php echo htmlspecialchars($claim['location']); ?> • <?php echo date('M d, g:i A', strtotime($claim['start_time'])); ?></p>
                            </div>
                        </div>
                        <span class="bg-[#0e0f0c] text-[#9fe870] text-[9px] px-3.5 py-1.5 rounded-full uppercase font-bold">
                            View Pass
                        </span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Booking Logs Section -->
    <div class="space-y-3">
        <h2 class="text-xs font-bold text-[#0e0f0c] tracking-wider uppercase flex items-center gap-2">
            <i data-lucide="history" class="w-4 h-4 text-[#163300]"></i> Reservation History Log
        </h2>
        <div class="bg-[#0e0f0c] rounded-[32px] p-5 text-white border border-white/10 shadow-lg">
            <?php if (empty($pastReservations)): ?>
                <p class="text-white/40 text-center text-xs py-6">Your transaction logs are empty.</p>
            <?php else: ?>
                <div class="divide-y divide-white/10">
                    <?php foreach($pastReservations as $item): ?>
                        <div class="flex justify-between items-center py-3 first:pt-0 last:pb-0">
                            <div>
                                <p class="font-bold text-xs text-white truncate max-w-xs"><?php echo htmlspecialchars($item['title']); ?></p>
                                <p class="text-[10px] text-white/50 mt-0.5 font-medium"><?php echo date('M d, Y h:i A', strtotime($item['start_time'])); ?></p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider <?php echo $item['status'] === 'claimed' ? 'bg-[#9fe870]/20 text-[#9fe870] border border-[#9fe870]/30' : 'bg-red-500/20 text-red-400 border border-red-500/30'; ?>">
                                <?php echo htmlspecialchars($item['status']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sign Out CTA -->
    <a href="/claim/config/logout.php" class="w-full h-14 flex items-center justify-center gap-2 bg-red-500/10 border border-red-500/20 text-red-600 rounded-full font-bold uppercase tracking-wider text-xs wise-btn cursor-pointer mt-4">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        Sign Out Student Account
    </a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>

<?php require_once '../includes/footer.php'; ?>