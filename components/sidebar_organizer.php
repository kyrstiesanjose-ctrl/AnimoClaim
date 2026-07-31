<aside class="hidden md:flex flex-col fixed top-0 left-0 h-screen w-72 bg-[#0e0f0c] border-r border-white/10 z-40 text-white">
    
    <!-- Sidebar Branding Area -->
    <div class="h-28 flex items-center px-6 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-[#9fe870] flex items-center justify-center overflow-hidden flex-shrink-0">
                <img src="../assets/pictures/AnimoClaim_Logo.png" alt="AnimoClaim Logo" class="w-full h-full object-cover">
            </div>
            <div class="flex flex-col items-start gap-1.5">
                <h1 class="text-2xl font-black text-[#9fe870] tracking-tight leading-none wise-heading">AnimoClaim</h1>
                <span class="bg-white/10 text-white/90 text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full">
                    USG Organizer
                </span>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 flex flex-col gap-2">
        <?php 
        $links = [
            ['dashboard.php', 'dashboard', 'Hub'], 
            ['terminal.php', 'qr_code_scanner', 'Scanner'], 
            ['vision.php', 'visibility', 'Vision'], 
            ['audits.php', 'gavel', 'Audits']
        ];
        
        foreach($links as $link) {
            $active = ($currentPage == str_replace('.php', '', $link[0]));
            
            $activeClasses = 'bg-[#9fe870] text-[#163300] shadow-sm';
            $inactiveClasses = 'text-white/60 hover:bg-white/5 hover:text-white transition-colors';
            
            echo "<a href='../organizer/{$link[0]}' class='flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold transition-all wise-btn " . ($active ? $activeClasses : $inactiveClasses) . "'>
                    <span class='material-symbols-outlined text-[22px]' style='font-variation-settings: \"FILL\" " . ($active ? 1 : 0) . ";'>{$link[1]}</span>
                    <span class='text-xs uppercase tracking-wider font-extrabold font-mono mt-px'>{$link[2]}</span>
                  </a>";
        } 
        ?>
    </nav>

    <!-- Logout Area -->
    <div class="p-4 border-t border-white/5">
        <a href="../config/logout.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-red-400 hover:bg-red-500/10 transition-colors font-bold text-xs uppercase tracking-wider wise-btn cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="mt-px">Sign Out</span>
        </a>
    </div>
</aside>