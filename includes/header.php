<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF'], ".php");
$role = $_SESSION['role'] ?? 'student';
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnimoClaim — DLSU Portal</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Leaflet CSS & JS for Campus Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..20" />
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .wise-heading { font-family: 'Montserrat', sans-serif; }
        .wise-btn { transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
        .wise-btn:active { transform: scale(0.97); }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#e8f5e1] text-[#163300] min-h-screen antialiased">

    <!-- Include Correct Sidebar Based on Role -->
    <?php if ($role === 'student'): ?>
        <?php include_once __DIR__ . '/../components/sidebar_student.php'; ?>
    <?php else: ?>
        <?php include_once __DIR__ . '/../components/sidebar_organizer.php'; ?>
    <?php endif; ?>

    <!-- Top Header -->
    <header class="fixed top-0 right-0 left-0 md:left-72 bg-[#0e0f0c] h-16 px-4 md:px-8 flex justify-between items-center z-30 shadow-md rounded-b-[24px]">
        <div class="flex flex-col justify-center">
            <h1 class="text-xl font-black text-white tracking-tight leading-none wise-heading">AnimoClaim</h1>
            <p class="text-[10px] font-bold text-gray-400 mt-0.5">DLSU Official Giveaway Portal</p>
        </div>

        <div class="flex items-center gap-2">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $role === 'student' ? '../student/profile.php' : '../organizer/profile.php'; ?>" 
                   class="flex items-center gap-2 pl-1.5 pr-4 py-1.5 bg-[#9fe870] text-[#163300] hover:bg-[#8ee05a] rounded-full text-xs font-bold transition-all shadow-sm wise-btn">
                    <div class="w-6 h-6 rounded-full bg-[#0e0f0c] text-[#9fe870] flex items-center justify-center font-black text-[10px] flex-shrink-0">
                        <?php echo strtoupper(substr($_SESSION['first_name'] ?? 'S', 0, 1)); ?>
                    </div>
                    <span class="font-black uppercase tracking-wider hidden sm:block mt-px">Profile</span>
                </a>
            <?php endif; ?>
            
            <a href="../config/logout.php" class="md:hidden w-8 h-8 flex items-center justify-center rounded-full text-red-400 bg-red-500/10 cursor-pointer hover:bg-red-500/20 transition-colors">
                <span class="material-symbols-outlined text-[18px]">logout</span>
            </a>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <main class="md:ml-72 pt-24 px-4 md:px-8 pb-12 max-w-7xl mx-auto">