</main>
</div> <!-- CLOSES MASTER WRAPPER FROM HEADER -->

<?php 
$base_url = "/claim"; 
if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): 
    $current_page = basename($_SERVER['PHP_SELF'], ".php");
?>
<!-- Mobile Navigation Bottom Bar -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 px-6 pb-6 pt-4 bg-[#1c261b] backdrop-blur-lg rounded-t-[32px] flex justify-around items-center border-t border-white/5 shadow-2xl">
    <a href="<?php echo $base_url; ?>/student/index.php" class="flex flex-col items-center flex-grow cursor-pointer <?php echo $current_page == 'index' ? 'text-[#c6f135]' : 'text-white/40 hover:text-[#c6f135]'; ?>">
        <div class="rounded-2xl w-14 h-10 flex items-center justify-center transition-all <?php echo $current_page == 'index' ? 'bg-[#c6f135] text-[#1c261b]' : 'hover:bg-white/5'; ?>">
            <i data-lucide="calendar" class="w-5 h-5"></i>
        </div>
        <span class="font-['Montserrat'] text-[9px] font-black uppercase tracking-widest mt-1">Events</span>
    </a>
    <a href="<?php echo $base_url; ?>/student/map.php" class="flex flex-col items-center flex-grow cursor-pointer <?php echo $current_page == 'map' ? 'text-[#c6f135]' : 'text-white/40 hover:text-[#c6f135]'; ?>">
        <div class="rounded-2xl w-14 h-10 flex items-center justify-center transition-all <?php echo $current_page == 'map' ? 'bg-[#c6f135] text-[#1c261b]' : 'hover:bg-white/5'; ?>">
            <i data-lucide="map-pin" class="w-5 h-5"></i>
        </div>
        <span class="font-['Montserrat'] text-[9px] font-black uppercase tracking-widest mt-1">Map</span>
    </a>
    <a href="<?php echo $base_url; ?>/student/tickets.php" class="flex flex-col items-center flex-grow cursor-pointer <?php echo $current_page == 'tickets' ? 'text-[#c6f135]' : 'text-white/40 hover:text-[#c6f135]'; ?>">
        <div class="rounded-2xl w-14 h-10 flex items-center justify-center transition-all <?php echo $current_page == 'tickets' ? 'bg-[#c6f135] text-[#1c261b]' : 'hover:bg-white/5'; ?>">
            <i data-lucide="ticket" class="w-5 h-5"></i>
        </div>
        <span class="font-['Montserrat'] text-[9px] font-black uppercase tracking-widest mt-1">Claims</span>
    </a>
    <a href="<?php echo $base_url; ?>/student/profile.php" class="flex flex-col items-center flex-grow cursor-pointer <?php echo $current_page == 'profile' ? 'text-[#c6f135]' : 'text-white/40 hover:text-[#c6f135]'; ?>">
        <div class="rounded-2xl w-14 h-10 flex items-center justify-center transition-all <?php echo $current_page == 'profile' ? 'bg-[#c6f135] text-[#1c261b]' : 'hover:bg-white/5'; ?>">
            <i data-lucide="user" class="w-5 h-5"></i>
        </div>
        <span class="font-['Montserrat'] text-[9px] font-black uppercase tracking-widest mt-1">Profile</span>
    </a>
</nav>
<?php endif; ?>

<script>
    // Initialize standard icons
    lucide.createIcons();
    
    // Global Constants for fetch requests
    const csrfToken = "<?php echo $_SESSION['csrf_token'] ?? ''; ?>";
    const baseUrl = "<?php echo $base_url; ?>";
</script>
</body>
</html>