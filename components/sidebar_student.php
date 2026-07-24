<aside class="hidden md:flex flex-col fixed top-0 left-0 h-screen w-72 bg-[#1c261b] border-r border-white/10 z-40">
        <!-- Inside sidebar_student.php -->
    <div class="h-28 flex items-center px-8 border-b border-white/5">
        <div class="flex items-center gap-2">
            <!-- Logo Image -->
            <img src="../assets/pictures/AnimoClaim_Logo.png" alt="AnimoClaim Logo" class="w-10 h-10 object-contain">
            <!-- Optional: Keep text if you want, or remove if the logo already has it -->
            <h1 class="text-2xl font-extrabold text-[#c6f135] tracking-tighter">AnimoClaim</h1>
        </div>
    </div>

    <nav class="flex-1 px-4 py-8 flex flex-col gap-2">
        <?php 
        $links = [
            ['events.php', 'event_upcoming', 'Events'], 
            ['map.php', 'map', 'Map'], 
            ['claim.php', 'confirmation_number', 'To Claim'], 
            ['profile.php', 'person', 'Profile']
        ];
        foreach($links as $link) {
            $active = ($current_page == str_replace('.php', '', $link[0]));
            // Ensure the span contains ONLY the icon or the text, not both
            echo "<a href='../student/{$link[0]}' class='flex items-center gap-4 px-4 py-3 rounded " . ($active ? 'bg-[#c6f135] text-[#1c261b]' : 'text-white/50 hover:bg-white/10 transition-colors') . "'>
                    <span class='material-symbols-outlined' style='font-variation-settings: \"FILL\" ".($active?1:0).";'>{$link[1]}</span>
                    <span class='font-['Jetbrains_Mono'] text-[14px] font-bold uppercase tracking-widest'>{$link[2]}</span>
                  </a>";
        } 
        ?>
    </nav>
</aside>