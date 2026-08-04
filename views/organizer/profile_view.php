<?php require_once '../includes/header.php'; ?>

<div class="space-y-6 max-w-xl mx-auto pb-12">
    <!-- Organizer Executive Loyalty Pass Card -->
    <div class="bg-[#0e0f0c] p-8 rounded-[36px] text-white border border-white/10 relative overflow-hidden shadow-xl">
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-[#9fe870]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center gap-5 relative z-10">
            <div class="w-16 h-16 rounded-full bg-[#9fe870] text-[#163300] flex items-center justify-center font-black text-xl flex-shrink-0 shadow-inner">
                <?php echo strtoupper(substr($currentUser['first_name'] ?? 'O', 0, 1)); ?>
            </div>
            <div class="flex flex-col justify-center min-w-0 space-y-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="text-xl font-bold text-white tracking-tight truncate"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h2>
                    <span class="bg-[#9fe870] text-[#163300] text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">
                        USG Executive Admin
                    </span>
                </div>
                <p class="text-[#9fe870] font-black text-xs tracking-widest uppercase font-mono">
                    Event Organizer / Administrator
                </p>
                <p class="text-white/40 text-xs font-mono pt-1">
                    <?php echo htmlspecialchars($currentUser['email']); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white p-7 rounded-[32px] border border-black/5 shadow-sm space-y-5">
        <div class="border-b border-gray-100 pb-3">
            <h3 class="font-black text-xs uppercase tracking-wider text-[#0e0f0c] font-mono">Account Credentials</h3>
        </div>
        <div class="space-y-4 text-xs font-mono">
            <div class="flex justify-between items-center py-1">
                <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Email Address</span>
                <span class="font-bold text-[#0e0f0c]"><?php echo htmlspecialchars($currentUser['email']); ?></span>
            </div>
            <div class="flex justify-between items-center py-1 border-t border-gray-50">
                <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Assigned Role</span>
                <span class="font-black text-[#163300] bg-[#e2f6d5] px-3 py-1 rounded-full uppercase text-[10px] tracking-wider"><?php echo htmlspecialchars($currentUser['role_level']); ?></span>
            </div>
        </div>
    </div>

    <a href="/claim/config/logout.php" class="w-full h-14 flex items-center justify-center gap-2 bg-red-500 text-white rounded-full font-black uppercase tracking-wider text-xs shadow-lg hover:bg-red-600 transition-colors cursor-pointer">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        Sign Out Organizer Session
    </a>
</div>

<?php require_once '../includes/footer.php'; ?>