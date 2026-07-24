<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 px-6 pb-6 pt-4 bg-[#1c261b] backdrop-blur-lg rounded-t-[32px] flex justify-around items-center border-t border-white/10">
    <?php 
    // This array defines your mobile navigation items
    $links = [
        ['events.php', 'calendar_today', 'Events'], 
        ['map.php', 'map', 'Map'], 
        ['claim.php', 'confirmation_number', 'To Claim'], 
        ['profile.php', 'person', 'Profile']
    ];

    foreach($links as $link) {
        $active = ($current_page == str_replace('.php', '', $link[0]));
        // The ternary operator below checks if the page is active to apply the lime-green color
        echo "<a href='../student/{$link[0]}' class='flex flex-col items-center justify-center gap-1 flex-1 ".($active?'':'text-white/50')."'>
                <div class='".($active?'bg-[#c6f135] text-[#1c261b]':'')." rounded-2xl w-14 h-10 flex items-center justify-center transition-all'>
                    <span class='material-symbols-outlined text-[24px]' style='font-variation-settings: \"FILL\" ".($active?1:0).";'>{$link[1]}</span>
                </div>
                <span class='font-['Montserrat'] text-[10px] uppercase tracking-widest font-bold ".($active?'text-[#c6f135]':'')."'>{$link[2]}</span>
              </a>";
    }
    ?>
</nav>