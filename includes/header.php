<?php 
$base_url = "/claim"; 
$current_page = basename($_SERVER['PHP_SELF'], ".php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>AnimoClaim</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&family=Montserrat:wght@500;600;700;800;900&family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <style type="text/tailwindcss">
        @theme {
            --font-sans: "Inter", ui-sans-serif, system-ui, sans-serif;
            --font-mono: "JetBrains Mono", ui-monospace, SFMono-Regular, monospace;
        }
        body { margin: 0; padding: 0; background-color: #f2fed9; }
        .ticket-dashed { border-top: 2px dashed rgba(255, 255, 255, 0.1); }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#f2fed9] text-[#1A2419] font-sans antialiased">

<div class="min-h-screen text-[#1A2419] relative md:pl-72 pb-24 md:pb-6 bg-[#f2fed9]">

    <!-- Sidebar -->
    <aside class="hidden md:flex flex-col fixed top-0 left-0 h-screen w-72 bg-[#1c261b] border-r border-white/10 z-40">
        <div class="h-28 flex items-center px-8 border-b border-white/5 gap-3">
           <div class="w-10 h-10 flex items-center justify-center bg-[#c6f135]/10 rounded-full border border-[#c6f135]/20 overflow-hidden">
                <img src="<?php echo $base_url; ?>/assets/pictures/AnimoClaim_Logo.png" alt="Logo" class="w-full h-full object-cover">
            </div>
            <div>
                <h1 class="text-2xl font-black text-[#c6f135] tracking-tighter leading-none font-['Montserrat']">AnimoClaim</h1>
                <span class="text-[9px] text-[#c6f135]/50 tracking-widest font-mono font-bold mt-1 inline-block uppercase">
                    <?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?> Portal
                </span>
            </div>
        </div>
        
        <nav class="flex-1 px-4 py-8 flex flex-col gap-2">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
                <a href="<?php echo $base_url; ?>/student/index.php" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all <?php echo $current_page == 'index' || $current_page == 'event_details' ? 'bg-[#c6f135] text-[#1c261b] shadow-md shadow-[#c6f135]/10' : 'text-white/60 hover:bg-white/5 hover:text-white'; ?>">
                    <i data-lucide="calendar" class="w-5 h-5"></i><span class="text-xs uppercase tracking-wider font-mono">Events</span>
                </a>
                <a href="<?php echo $base_url; ?>/student/map.php" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all <?php echo $current_page == 'map' ? 'bg-[#c6f135] text-[#1c261b] shadow-md shadow-[#c6f135]/10' : 'text-white/60 hover:bg-white/5 hover:text-white'; ?>">
                    <i data-lucide="map-pin" class="w-5 h-5"></i><span class="text-xs uppercase tracking-wider font-mono">Live Maps</span>
                </a>
                <a href="<?php echo $base_url; ?>/student/tickets.php" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all <?php echo $current_page == 'tickets' ? 'bg-[#c6f135] text-[#1c261b] shadow-md shadow-[#c6f135]/10' : 'text-white/60 hover:bg-white/5 hover:text-white'; ?>">
                    <i data-lucide="ticket" class="w-5 h-5"></i><span class="text-xs uppercase tracking-wider font-mono">To Claim</span>
                </a>
                <a href="<?php echo $base_url; ?>/student/profile.php" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all <?php echo $current_page == 'profile' ? 'bg-[#c6f135] text-[#1c261b] shadow-md shadow-[#c6f135]/10' : 'text-white/60 hover:bg-white/5 hover:text-white'; ?>">
                    <i data-lucide="user" class="w-5 h-5"></i><span class="text-xs uppercase tracking-wider font-mono">My Profile</span>
                </a>
            <?php else: ?>
                <a href="<?php echo $base_url; ?>/organizer/dashboard.php" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all <?php echo $current_page == 'dashboard' ? 'bg-[#c6f135] text-[#1c261b] shadow-md shadow-[#c6f135]/10' : 'text-white/60 hover:bg-white/5 hover:text-white'; ?>">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i><span class="text-xs uppercase tracking-wider font-mono">Dashboard</span>
                </a>
                <a href="<?php echo $base_url; ?>/organizer/terminal.php" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all <?php echo $current_page == 'terminal' ? 'bg-[#c6f135] text-[#1c261b] shadow-md shadow-[#c6f135]/10' : 'text-white/60 hover:bg-white/5 hover:text-white'; ?>">
                    <i data-lucide="terminal" class="w-5 h-5"></i><span class="text-xs uppercase tracking-wider font-mono">Onsite Terminal</span>
                </a>
                <a href="<?php echo $base_url; ?>/organizer/vision.php" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all <?php echo $current_page == 'vision' ? 'bg-[#c6f135] text-[#1c261b] shadow-md shadow-[#c6f135]/10' : 'text-white/60 hover:bg-white/5 hover:text-white'; ?>">
                    <i data-lucide="eye" class="w-5 h-5"></i><span class="text-xs uppercase tracking-wider font-mono">Overhead Vision</span>
                </a>
                <a href="<?php echo $base_url; ?>/organizer/audits.php" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all <?php echo $current_page == 'audits' ? 'bg-[#c6f135] text-[#1c261b] shadow-md shadow-[#c6f135]/10' : 'text-white/60 hover:bg-white/5 hover:text-white'; ?>">
                    <i data-lucide="shield-check" class="w-5 h-5"></i><span class="text-xs uppercase tracking-wider font-mono">Audits & Penalties</span>
                </a>
            <?php endif; ?>
        </nav>
        <div class="p-4 border-t border-white/5 flex flex-col gap-2">
            <div class="px-4 py-2 bg-white/5 rounded-xl text-[10px] text-white/50 leading-tight">
                <span class="font-bold text-[#c6f135] block mb-0.5">Session Account:</span>
                <?php echo htmlspecialchars($_SESSION['first_name'] ?? ''); ?> (<?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?>)
            </div>
            <a href="<?php echo $base_url; ?>/config/logout.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-red-400 hover:bg-red-500/10 transition-colors">
                <i data-lucide="log-out" class="w-5 h-5"></i><span class="text-xs uppercase tracking-wider font-mono">Sign Out</span>
            </a>
        </div>
    </aside>
    
    <!-- Header -->
        <header class="fixed top-0 right-0 left-0 md:left-72 bg-[#1A2419] h-20 px-6 md:px-8 flex justify-between items-center rounded-b-[20px] md:rounded-bl-none md:rounded-br-[24px] shadow-lg z-30 border-b border-white/5">
            <div class="flex items-center gap-3">
                <h1 class="text-xl md:text-2xl font-black text-white tracking-tight">Portal Gateway</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-white text-xs font-bold leading-tight"><?php echo htmlspecialchars($_SESSION['first_name'] ?? ''); ?> <?php echo htmlspecialchars($_SESSION['last_name'] ?? ''); ?></p>
                    <p class="text-[#c6f135] text-[10px] font-mono tracking-wider"><?php echo htmlspecialchars($_SESSION['dlsu_id'] ?? ''); ?></p>
                </div>
                
                <!-- Mobile Escape Hatch for Logout -->
                <a href="<?php echo $base_url; ?>/config/logout.php" class="md:hidden w-10 h-10 flex items-center justify-center rounded-full text-red-400 bg-red-500/10 hover:bg-red-500/20 transition-colors">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </a>
            </div>
        </header>

    <main class="pt-28 px-4 md:px-8 max-w-5xl mx-auto pb-12">