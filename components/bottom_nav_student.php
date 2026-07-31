<!-- Mobile Bottom Navigation Bar -->
<div class="md:hidden fixed bottom-4 left-4 right-4 z-40 bg-[#0e0f0c] rounded-[28px] p-2 shadow-2xl border border-white/10 flex justify-around items-center">
    
    <!-- Events / Campaigns -->
    <a href="../student/index.php" class="flex flex-col items-center gap-1 py-2 px-4 rounded-2xl <?php echo $currentPage === 'index' ? 'bg-[#9fe870] text-[#163300]' : 'text-white/60 hover:text-white'; ?> transition-all">
        <span class="material-symbols-outlined text-[20px]">storefront</span>
        <span class="text-[9px] font-black uppercase tracking-wider font-mono">Events</span>
    </a>

    <!-- Campus Map -->
    <a href="../student/map.php" class="flex flex-col items-center gap-1 py-2 px-4 rounded-2xl <?php echo $currentPage === 'map' ? 'bg-[#9fe870] text-[#163300]' : 'text-white/60 hover:text-white'; ?> transition-all">
        <span class="material-symbols-outlined text-[20px]">map</span>
        <span class="text-[9px] font-black uppercase tracking-wider font-mono">Map</span>
    </a>

    <!-- Tickets / Claims Pass -->
    <a href="../student/tickets.php" class="flex flex-col items-center gap-1 py-2 px-4 rounded-2xl <?php echo $currentPage === 'tickets' ? 'bg-[#9fe870] text-[#163300]' : 'text-white/60 hover:text-white'; ?> transition-all">
        <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
        <span class="text-[9px] font-black uppercase tracking-wider font-mono">Tickets</span>
    </a>

</div>