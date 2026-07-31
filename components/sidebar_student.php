<aside class="hidden md:flex flex-col fixed top-0 left-0 h-screen w-72 bg-[#0e0f0c] border-r border-white/10 z-40 text-white">
    <div class="h-28 flex items-center px-8 border-b border-white/5">
        <div class="flex items-center gap-3">
            <img src="../assets/pictures/AnimoClaim_Logo.png" alt="Logo" class="w-10 h-10 object-contain">
            <div>
                <h1 class="text-xl font-black text-[#9fe870] tracking-tight">AnimoClaim</h1>
                <p class="text-[10px] text-white/50 uppercase tracking-widest font-mono">Student Portal</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 py-6 flex flex-col gap-2">
        <?php 
        $links = [
            ['index.php', 'storefront', 'Campaigns'], 
            ['tickets.php', 'confirmation_number', 'My Tickets'], 
            ['map.php', 'map', 'Campus Map']
        ];
        
        foreach($links as $link) {
            $isActive = ($currentPage === str_replace('.php', '', $link[0]));
            
            // Updated to the Wise color scheme
            $activeClasses = 'bg-[#9fe870] text-[#163300] shadow-md';
            $inactiveClasses = 'text-white/60 hover:bg-white/5 hover:text-white transition-colors';
            
            echo "<a href='../student/{$link[0]}' class='flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold transition-all wise-btn " . ($isActive ? $activeClasses : $inactiveClasses) . "'>
                    <span class='material-symbols-outlined text-[22px]' style='font-variation-settings: \"FILL\" " . ($isActive ? 1 : 0) . ";'>{$link[1]}</span>
                    <span class='text-xs uppercase tracking-wider font-extrabold font-mono'>{$link[2]}</span>
                  </a>";
        } 
        ?>
    </nav>

    <div class="p-4 border-t border-white/5">
        <a href="../config/logout.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-red-400 hover:bg-red-500/10 transition-colors font-bold text-xs uppercase tracking-wider wise-btn cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span>Sign Out</span>
        </a>
    </div>
</aside>