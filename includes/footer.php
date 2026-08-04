</main> <!-- CLOSES MASTER MAIN FROM HEADER -->

    <?php 
    $role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
    
    // DYNAMICALLY LOAD THE CORRECT BOTTOM NAVIGATION
    if ($role === 'organizer' || $role === 'admin') {
        include '../components/bottom_nav_organizer.php';
    } else {
        include '../components/bottom_nav_student.php';
    }
    ?>

    <script>
        if (window.lucide) {
            lucide.createIcons();
        }
        const csrfToken = "<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>";
    </script>
</body>
</html>