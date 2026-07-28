</main>
</div> <!-- CLOSES MASTER WRAPPER FROM HEADER -->

<?php
$base_url = "/claim";
$role = $_SESSION['role'] ?? '';
$current_page = basename($_SERVER['PHP_SELF'], ".php");
if ($role):
?>
<!-- Mobile Navigation Bottom Dock -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#0e0f0c] border-t border-white/10 shadow-[0_-8px_20px_rgba(0,0,0,0.35)] px-3 py-1 flex justify-around items-center">
    <?php if ($role === 'student'): ?>
        <a href="<?php echo $base_url; ?>/student/index.php" class="flex flex-col items-center justify-center transition-all wise-btn py-0.5">
            <div class="<?php echo ($current_page == 'index' || $current_page == 'event_details') ? 'w-8 h-6 bg-[#9fe870] text-[#163300] rounded-full flex items-center justify-center shadow-xs' : 'w-8 h-6 text-white/60 hover:text-white flex items-center justify-center'; ?>">
                <i data-lucide="calendar" class="w-4 h-4"></i>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 <?php echo ($current_page == 'index' || $current_page == 'event_details') ? 'text-[#9fe870]' : 'text-white/60'; ?>">Events</span>
        </a>
        <a href="<?php echo $base_url; ?>/student/map.php" class="flex flex-col items-center justify-center transition-all wise-btn py-0.5">
            <div class="<?php echo $current_page == 'map' ? 'w-8 h-6 bg-[#9fe870] text-[#163300] rounded-full flex items-center justify-center shadow-xs' : 'w-8 h-6 text-white/60 hover:text-white flex items-center justify-center'; ?>">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 <?php echo $current_page == 'map' ? 'text-[#9fe870]' : 'text-white/60'; ?>">Map</span>
        </a>
        <a href="<?php echo $base_url; ?>/student/tickets.php" class="flex flex-col items-center justify-center transition-all wise-btn py-0.5">
            <div class="<?php echo ($current_page == 'tickets' || $current_page == 'claim') ? 'w-8 h-6 bg-[#9fe870] text-[#163300] rounded-full flex items-center justify-center shadow-xs' : 'w-8 h-6 text-white/60 hover:text-white flex items-center justify-center'; ?>">
                <i data-lucide="ticket" class="w-4 h-4"></i>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 <?php echo ($current_page == 'tickets' || $current_page == 'claim') ? 'text-[#9fe870]' : 'text-white/60'; ?>">Claims</span>
        </a>
        <a href="<?php echo $base_url; ?>/student/profile.php" class="flex flex-col items-center justify-center transition-all wise-btn py-0.5">
            <div class="<?php echo $current_page == 'profile' ? 'w-8 h-6 bg-[#9fe870] text-[#163300] rounded-full flex items-center justify-center shadow-xs' : 'w-8 h-6 text-white/60 hover:text-white flex items-center justify-center'; ?>">
                <i data-lucide="user" class="w-4 h-4"></i>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 <?php echo $current_page == 'profile' ? 'text-[#9fe870]' : 'text-white/60'; ?>">Profile</span>
        </a>
    <?php else: ?>
        <a href="<?php echo $base_url; ?>/organizer/dashboard.php" class="flex flex-col items-center justify-center transition-all wise-btn py-0.5">
            <div class="<?php echo $current_page == 'dashboard' ? 'w-8 h-6 bg-[#9fe870] text-[#163300] rounded-full flex items-center justify-center shadow-xs' : 'w-8 h-6 text-white/60 hover:text-white flex items-center justify-center'; ?>">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 <?php echo $current_page == 'dashboard' ? 'text-[#9fe870]' : 'text-white/60'; ?>">Hub</span>
        </a>
        <a href="<?php echo $base_url; ?>/organizer/terminal.php" class="flex flex-col items-center justify-center transition-all wise-btn py-0.5">
            <div class="<?php echo $current_page == 'terminal' ? 'w-8 h-6 bg-[#9fe870] text-[#163300] rounded-full flex items-center justify-center shadow-xs' : 'w-8 h-6 text-white/60 hover:text-white flex items-center justify-center'; ?>">
                <i data-lucide="qr-code" class="w-4 h-4"></i>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 <?php echo $current_page == 'terminal' ? 'text-[#9fe870]' : 'text-white/60'; ?>">Scanner</span>
        </a>
        <a href="<?php echo $base_url; ?>/organizer/vision.php" class="flex flex-col items-center justify-center transition-all wise-btn py-0.5">
            <div class="<?php echo $current_page == 'vision' ? 'w-8 h-6 bg-[#9fe870] text-[#163300] rounded-full flex items-center justify-center shadow-xs' : 'w-8 h-6 text-white/60 hover:text-white flex items-center justify-center'; ?>">
                <i data-lucide="eye" class="w-4 h-4"></i>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 <?php echo $current_page == 'vision' ? 'text-[#9fe870]' : 'text-white/60'; ?>">Vision</span>
        </a>
        <a href="<?php echo $base_url; ?>/organizer/audits.php" class="flex flex-col items-center justify-center transition-all wise-btn py-0.5">
            <div class="<?php echo $current_page == 'audits' ? 'w-8 h-6 bg-[#9fe870] text-[#163300] rounded-full flex items-center justify-center shadow-xs' : 'w-8 h-6 text-white/60 hover:text-white flex items-center justify-center'; ?>">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5 <?php echo $current_page == 'audits' ? 'text-[#9fe870]' : 'text-white/60'; ?>">Audits</span>
        </a>
    <?php endif; ?>
</nav>
<?php endif; ?>

<script>
    if (window.lucide) {
        lucide.createIcons();
    }
    const csrfToken = "<?php echo $_SESSION['csrf_token'] ?? ''; ?>";
    const baseUrl = "<?php echo $base_url; ?>";
</script>
</body>
</html>