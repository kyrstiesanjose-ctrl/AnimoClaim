<?php require_once '../includes/header.php'; ?>

<div class="space-y-6 max-w-xl mx-auto pb-12">
    <!-- Organizer Executive Loyalty Pass Card -->
    <div class="bg-[#0e0f0c] p-7 rounded-[36px] text-white border border-[#0e0f0c]/12 relative overflow-hidden shadow-xl">
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#9fe870]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center gap-4 relative z-10">
            <div class="w-16 h-16 rounded-full bg-[#9fe870] text-[#163300] flex items-center justify-center font-black text-xl flex-shrink-0 shadow-inner">
                <?php echo strtoupper(substr($currentUser['first_name'] ?? 'O', 0, 1)); ?>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-xl wise-heading text-white truncate"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h2>
                    <span class="bg-[#9fe870] text-[#163300] text-[9px] font-bold px-2.5 py-0.5 rounded-full uppercase">
                        USG Executive Admin
                    </span>
                </div>
                <p class="text-[#9fe870] font-bold text-xs tracking-wider uppercase mt-1">
                    Event Organizer / Administrator
                </p>
                <p class="text-white/40 text-[10px] font-medium mt-1">
                    Email: <?php echo htmlspecialchars($currentUser['email']); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white p-6 rounded-[32px] border border-[#0e0f0c]/12 shadow-sm space-y-4">
        <div class="border-b border-gray-100 pb-3">
            <h3 class="font-black text-xs uppercase tracking-wider text-[#0e0f0c]">Account Credentials</h3>
        </div>
        <div class="space-y-3 text-xs">
            <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Email Address:</span>
                <span class="font-bold text-[#0e0f0c]"><?php echo htmlspecialchars($currentUser['email']); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 font-medium">Assigned Role:</span>
                <span class="font-bold text-[#163300] bg-[#e2f6d5] px-2.5 py-0.5 rounded-full uppercase text-[10px]"><?php echo htmlspecialchars($currentUser['role']); ?></span>
            </div>
        </div>
    </div>

    <a href="/claim/config/logout.php" class="w-full h-14 flex items-center justify-center gap-2 bg-red-500/10 border border-red-500/20 text-red-600 rounded-full font-bold uppercase tracking-wider text-xs wise-btn cursor-pointer">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        Sign Out Organizer Session
    </a>
</div>

<?php require_once '../includes/footer.php'; ?>