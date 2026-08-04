<aside class="fixed inset-y-0 left-0 w-72 bg-[#0e0f0c] text-white flex flex-col justify-between z-40 shadow-2xl transition-transform duration-300">
    <div class="p-6">
        <!-- Logo Area -->
        <div class="flex items-center gap-3 mb-10">
            <div class="w-10 h-10 rounded-2xl bg-[#9fe870] flex items-center justify-center text-[#163300] shadow-md">
                <i data-lucide="map-pin" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="font-black text-base text-white tracking-wide wise-heading">AnimoClaim</h2>
                <span class="inline-block px-2 py-0.5 rounded-full bg-white/10 text-[9px] font-mono text-[#9fe870] font-bold uppercase tracking-wider mt-0.5">USG Organizer</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-2">
            <a href="/claim/organizer/dashboard.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 font-black text-xs uppercase tracking-widest <?php echo ($currentPage === 'dashboard') ? 'bg-[#9fe870] text-[#163300] shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <i data-lucide="layout-grid" class="w-5 h-5 <?php echo ($currentPage === 'dashboard') ? 'text-[#163300]' : 'text-gray-400'; ?>"></i>
                Hub
            </a>

            <a href="/claim/organizer/terminal.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 font-black text-xs uppercase tracking-widest <?php echo ($currentPage === 'terminal') ? 'bg-[#9fe870] text-[#163300] shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <i data-lucide="qr-code" class="w-5 h-5 <?php echo ($currentPage === 'terminal') ? 'text-[#163300]' : 'text-gray-400'; ?>"></i>
                Scanner
            </a>

            <a href="/claim/organizer/vision.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 font-black text-xs uppercase tracking-widest <?php echo ($currentPage === 'vision') ? 'bg-[#9fe870] text-[#163300] shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <i data-lucide="eye" class="w-5 h-5 <?php echo ($currentPage === 'vision') ? 'text-[#163300]' : 'text-gray-400'; ?>"></i>
                Vision
            </a>

            <a href="/claim/organizer/audits.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 font-black text-xs uppercase tracking-widest <?php echo ($currentPage === 'audits') ? 'bg-[#9fe870] text-[#163300] shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <i data-lucide="file-text" class="w-5 h-5 <?php echo ($currentPage === 'audits') ? 'text-[#163300]' : 'text-gray-400'; ?>"></i>
                Audits
            </a>

            <a href="/claim/organizer/inventory.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 font-black text-xs uppercase tracking-widest <?php echo ($currentPage === 'inventory') ? 'bg-[#9fe870] text-[#163300] shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <i data-lucide="package" class="w-5 h-5 <?php echo ($currentPage === 'inventory') ? 'text-[#163300]' : 'text-gray-400'; ?>"></i>
                Inventory
            </a>
        </nav>
    </div>

    <!-- Bottom Profile/Logout Area -->
    <div class="p-6 border-t border-white/10">
        <a href="/claim/config/logout.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-red-500/10 text-red-400 hover:bg-red-500/20 font-black text-xs uppercase tracking-wider transition-all">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            Logout Portal
        </a>
    </div>
</aside>