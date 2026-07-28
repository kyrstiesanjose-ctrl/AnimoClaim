<?php
$base_url = "/claim";
$current_page = basename($_SERVER['PHP_SELF'], ".php");
$role = $_SESSION['role'] ?? '';
$first_name = $_SESSION['first_name'] ?? '';
$last_name = $_SESSION['last_name'] ?? '';
$dlsu_id = $_SESSION['dlsu_id'] ?? '';
$initial = $first_name ? strtoupper(substr($first_name, 0, 1)) : 'U';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <title>AnimoClaim</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <style type="text/tailwindcss">
        @theme {
            --font-sans: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --color-wise-green: #9fe870;
            --color-dark-green: #163300;
            --color-near-black: #0e0f0c;
            --color-light-mint: #e2f6d5;
        }
        * { font-feature-settings: "calt" 1; }
        body {
            margin: 0; padding: 0;
            background-color: #e8f5e1;
            color: #0e0f0c;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-weight: 600;
        }
        .ticket-dashed { border-top: 2px dashed rgba(255, 255, 255, 0.2); }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .wise-btn { transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.15s ease; }
        .wise-btn:hover { transform: scale(1.05); }
        .wise-btn:active { transform: scale(0.95); }
        .wise-heading { font-weight: 900; line-height: 0.88; letter-spacing: -0.03em; }

        @keyframes rfidCardTap {
            0% { transform: translateY(-28px) scale(0.92) rotate(-3deg); filter: drop-shadow(0 20px 15px rgba(0,0,0,0.4)); }
            35% { transform: translateY(-8px) scale(0.98) rotate(-1deg); filter: drop-shadow(0 12px 10px rgba(0,0,0,0.5)); }
            50% { transform: translateY(16px) scale(1.04) rotate(0deg); filter: drop-shadow(0 2px 4px rgba(159, 232, 112, 0.8)); }
            62% { transform: translateY(14px) scale(1.03) rotate(0deg); filter: drop-shadow(0 3px 6px rgba(159, 232, 112, 0.6)); }
            82% { transform: translateY(-14px) scale(0.95) rotate(-2deg); filter: drop-shadow(0 16px 12px rgba(0,0,0,0.45)); }
            100% { transform: translateY(-28px) scale(0.92) rotate(-3deg); filter: drop-shadow(0 20px 15px rgba(0,0,0,0.4)); }
        }
        @keyframes rfidPulse {
            0% { transform: scale(0.5); opacity: 0.1; }
            45% { transform: scale(0.7); opacity: 0.2; }
            50% { transform: scale(1.3); opacity: 0.95; }
            70% { transform: scale(1.6); opacity: 0; }
            100% { transform: scale(0.5); opacity: 0.1; }
        }
        @keyframes rfidWifi {
            0%, 100% { opacity: 0.3; transform: rotate(45deg) scale(0.9); }
            50% { opacity: 1; transform: rotate(45deg) scale(1.1); }
        }
        @keyframes rfidScanBeam {
            0% { top: -5%; opacity: 0; }
            15% { opacity: 0.9; }
            85% { opacity: 0.9; }
            100% { top: 105%; opacity: 0; }
        }
        .rfid-card-anim { animation: rfidCardTap 2.8s ease-in-out infinite; }
        .rfid-pulse-ring-1 { animation: rfidPulse 2.2s cubic-bezier(0, 0.2, 0.8, 1) infinite; }
        .rfid-pulse-ring-2 { animation: rfidPulse 2.2s cubic-bezier(0, 0.2, 0.8, 1) 1.1s infinite; }
        .rfid-wifi-anim { animation: rfidWifi 1.5s ease-in-out infinite; }
        .rfid-scan-line { animation: rfidScanBeam 2.2s ease-in-out infinite; }
    </style>
</head>
<body class="bg-[#e8f5e1] text-[#0e0f0c] font-sans antialiased min-h-screen selection:bg-[#9fe870] selection:text-[#163300]">

<div class="min-h-screen text-[#0e0f0c] relative md:pl-72 pb-28 md:pb-8 bg-[#e8f5e1]">

    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex flex-col fixed top-0 left-0 h-screen w-72 bg-[#0e0f0c] border-r border-[#0e0f0c]/12 z-40">
        <div class="h-28 flex items-center px-6 border-b border-white/10 gap-3.5">
            <div class="w-12 h-12 flex items-center justify-center bg-[#9fe870] rounded-2xl overflow-hidden flex-shrink-0 shadow-md">
                <img src="<?php echo $base_url; ?>/assets/pictures/AnimoClaim_Logo.png" alt="AnimoClaim Logo" class="w-full h-full object-cover" />
            </div>
            <div>
                <h1 class="text-2xl wise-heading text-[#9fe870]">AnimoClaim</h1>
                <span class="text-[10px] text-white/80 tracking-widest font-extrabold mt-1 inline-block uppercase bg-white/10 px-3 py-0.5 rounded-full">
                    <?php echo htmlspecialchars($role); ?> Portal
                </span>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 flex flex-col gap-2">
            <?php if ($role === 'student'): ?>
                <a href="<?php echo $base_url; ?>/student/index.php" class="w-full flex items-center gap-3.5 px-5 py-3.5 rounded-full font-bold transition-all wise-btn <?php echo ($current_page == 'index' || $current_page == 'event_details') ? 'bg-[#9fe870] text-[#163300]' : 'text-white/80 hover:bg-white/10 hover:text-white'; ?>">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    <span class="text-xs uppercase tracking-wider">Events & Deals</span>
                </a>
                <a href="<?php echo $base_url; ?>/student/map.php" class="w-full flex items-center gap-3.5 px-5 py-3.5 rounded-full font-bold transition-all wise-btn <?php echo $current_page == 'map' ? 'bg-[#9fe870] text-[#163300]' : 'text-white/80 hover:bg-white/10 hover:text-white'; ?>">
                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                    <span class="text-xs uppercase tracking-wider">Live Traffic Map</span>
                </a>
                <a href="<?php echo $base_url; ?>/student/tickets.php" class="w-full flex items-center gap-3.5 px-5 py-3.5 rounded-full font-bold transition-all wise-btn <?php echo ($current_page == 'tickets' || $current_page == 'claim') ? 'bg-[#9fe870] text-[#163300]' : 'text-white/80 hover:bg-white/10 hover:text-white'; ?>">
                    <i data-lucide="ticket" class="w-5 h-5"></i>
                    <span class="text-xs uppercase tracking-wider">My Claims Pass</span>
                </a>
                <a href="<?php echo $base_url; ?>/student/profile.php" class="w-full flex items-center gap-3.5 px-5 py-3.5 rounded-full font-bold transition-all wise-btn <?php echo $current_page == 'profile' ? 'bg-[#9fe870] text-[#163300]' : 'text-white/80 hover:bg-white/10 hover:text-white'; ?>">
                    <i data-lucide="user" class="w-5 h-5"></i>
                    <span class="text-xs uppercase tracking-wider">Student Profile</span>
                </a>
            <?php else: ?>
                <a href="<?php echo $base_url; ?>/organizer/dashboard.php" class="w-full flex items-center gap-3.5 px-5 py-3.5 rounded-full font-bold transition-all wise-btn <?php echo $current_page == 'dashboard' ? 'bg-[#9fe870] text-[#163300]' : 'text-white/80 hover:bg-white/10 hover:text-white'; ?>">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span class="text-xs uppercase tracking-wider">Campaign Hub</span>
                </a>
                <a href="<?php echo $base_url; ?>/organizer/terminal.php" class="w-full flex items-center gap-3.5 px-5 py-3.5 rounded-full font-bold transition-all wise-btn <?php echo $current_page == 'terminal' ? 'bg-[#9fe870] text-[#163300]' : 'text-white/80 hover:bg-white/10 hover:text-white'; ?>">
                    <i data-lucide="qr-code" class="w-5 h-5"></i>
                    <span class="text-xs uppercase tracking-wider">Claim Scanner</span>
                </a>
                <a href="<?php echo $base_url; ?>/organizer/vision.php" class="w-full flex items-center gap-3.5 px-5 py-3.5 rounded-full font-bold transition-all wise-btn <?php echo $current_page == 'vision' ? 'bg-[#9fe870] text-[#163300]' : 'text-white/80 hover:bg-white/10 hover:text-white'; ?>">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                    <span class="text-xs uppercase tracking-wider">Vision Telemetry</span>
                </a>
                <a href="<?php echo $base_url; ?>/organizer/audits.php" class="w-full flex items-center gap-3.5 px-5 py-3.5 rounded-full font-bold transition-all wise-btn <?php echo $current_page == 'audits' ? 'bg-[#9fe870] text-[#163300]' : 'text-white/80 hover:bg-white/10 hover:text-white'; ?>">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                    <span class="text-xs uppercase tracking-wider">Audits & Penalty</span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="p-4 border-t border-white/10 flex flex-col gap-2 bg-black/30">
            <div class="px-4 py-3 bg-white/5 rounded-2xl border border-white/10 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-[#9fe870] text-[#163300] flex items-center justify-center font-black text-xs">
                    <?php echo htmlspecialchars($initial); ?>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-white truncate"><?php echo htmlspecialchars(trim($first_name . ' ' . $last_name)); ?></p>
                    <p class="text-[10px] text-[#9fe870] font-bold truncate"><?php echo htmlspecialchars($dlsu_id); ?></p>
                </div>
            </div>
            <a href="<?php echo $base_url; ?>/config/logout.php" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-full font-bold text-red-400 hover:bg-red-500/10 transition-colors wise-btn text-xs uppercase tracking-wider">
                <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
            </a>
        </div>
    </aside>

    <!-- Top Mobile-First App Header -->
    <header class="fixed top-0 right-0 left-0 md:left-72 bg-[#0e0f0c] h-14 md:h-16 px-3.5 md:px-8 flex justify-between items-center z-30 border-b border-white/10 shadow-sm">
        <div class="flex items-center gap-2.5">
            <a href="<?php echo $base_url; ?>/<?php echo $role === 'student' ? 'student/index.php' : 'organizer/dashboard.php'; ?>" class="md:hidden w-8 h-8 bg-[#9fe870] rounded-xl flex items-center justify-center wise-btn flex-shrink-0 overflow-hidden shadow-xs">
                <img src="<?php echo $base_url; ?>/assets/pictures/AnimoClaim_Logo.png" alt="AnimoClaim Logo" class="w-full h-full object-cover" />
            </a>
            <div>
                <h1 class="text-base md:text-xl wise-heading text-white tracking-tight flex items-center gap-1.5 leading-tight">
                    AnimoClaim
                </h1>
                <p class="text-[9px] text-white/60 font-semibold tracking-wide leading-none">DLSU Official Giveaway Portal</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <?php if ($role): ?>
                <a href="<?php echo $base_url; ?>/<?php echo $role === 'student' ? 'student/profile.php' : 'organizer/profile.php'; ?>"
                   class="flex items-center gap-1.5 px-2.5 py-1 bg-[#9fe870] text-[#163300] hover:bg-[#8ee05a] rounded-full text-xs font-bold transition-all wise-btn shadow-xs"
                   title="View Profile">
                    <div class="w-5 h-5 rounded-full bg-[#163300] text-[#9fe870] flex items-center justify-center font-black text-[10px]">
                        <?php echo htmlspecialchars($initial); ?>
                    </div>
                    <span class="font-black uppercase text-[9px] tracking-wider">Profile</span>
                </a>
            <?php endif; ?>

            <a href="<?php echo $base_url; ?>/config/logout.php" class="md:hidden w-8 h-8 flex items-center justify-center rounded-full text-red-400 bg-red-500/10 border border-red-500/20 active:scale-95 transition-all cursor-pointer" title="Sign Out">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </header>

    <main class="pt-16 md:pt-20 px-3.5 md:px-8 max-w-4xl mx-auto pb-16 md:pb-12">