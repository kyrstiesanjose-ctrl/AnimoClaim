<?php 
require_once('../config/auth.php'); 
$current_page = 'profile'; 
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>AnimoClaim - Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #F4FCE3; /* Matches your Events page background */
            font-family: 'Montserrat', sans-serif;
            color: #1A2419;
        }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="md:pl-72 min-h-screen selection:bg-[#c6f135] selection:text-[#1A2419] overflow-x-hidden pb-32">
    
    <?php include('../components/sidebar_student.php'); ?>

    <!-- Header -->
    <header class="w-full bg-[#1A2419] px-6 py-6 flex justify-between items-center rounded-b-[24px] md:rounded-bl-none md:rounded-br-[32px] shadow-md z-40 relative md:fixed md:top-0 md:left-72 md:w-[calc(100%-18rem)]">
        <h1 class="text-[24px] font-black text-[#c6f135] tracking-tight">Profile</h1>
        <button class="relative w-10 h-10 flex items-center justify-center rounded-full text-[#c6f135] hover:bg-white/10 transition-all">
            <span class="material-symbols-outlined">notifications</span>
        </button>
    </header>

    <main class="md:pt-28 max-w-5xl mx-auto px-5 relative z-10">
        
        <!-- Profile Info Block -->
        <section class="mt-6 flex flex-col md:flex-row gap-6">
            <div class="flex-1 bg-[#1A2419] p-6 rounded-[24px] flex items-center gap-6 text-white">
                <div class="w-20 h-20 rounded-full bg-white/10 flex items-center justify-center border-2 border-[#c6f135]">
                    <span class="material-symbols-outlined text-[40px] text-[#c6f135]">person</span>
                </div>
                <div>
                    <h1 id="profile-name" class="text-2xl font-black">Loading...</h1>
                    <p id="profile-id" class="text-[#c6f135] font-bold text-sm tracking-widest uppercase">ID: ...</p>
                    <p id="profile-dept" class="text-white/60 text-xs font-bold mt-1">...</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="flex gap-4">
                <div class="bg-[#1A2419] p-6 rounded-[24px] flex-1 min-w-[120px] text-center flex flex-col justify-center">
                    <div id="stat-total" class="text-3xl font-black text-white">0</div>
                    <div class="text-[10px] font-bold text-[#c6f135] uppercase tracking-widest mt-1">Total Claims</div>
                </div>
                <div class="bg-[#1A2419] p-6 rounded-[24px] flex-1 min-w-[120px] text-center flex flex-col justify-center">
                    <div id="stat-active" class="text-3xl font-black text-white">0</div>
                    <div class="text-[10px] font-bold text-[#c6f135] uppercase tracking-widest mt-1">Active</div>
                </div>
            </div>
        </section>

        <!-- Claims Section (Same style as Events List) -->
        <section class="mt-8">
            <h2 class="text-[20px] font-extrabold text-[#1A2419] mb-4 tracking-tight">Active Claims</h2>
            <div id="active-claims-container" class="space-y-4">
                <p class="text-[#1A2419]/60 font-medium">Loading claims...</p>
            </div>
        </section>

        <!-- History -->
        <section class="mt-8 mb-8">
            <h2 class="text-[20px] font-extrabold text-[#1A2419] mb-4 tracking-tight">History</h2>
            <div id="history-container" class="bg-[#1A2419] rounded-[24px] p-6 text-white">
                <p class="text-white/60">Loading history...</p>
            </div>
        </section>

        <!-- Logout -->
        <a href="../config/logout.php" class="w-full py-4 flex items-center justify-center gap-3 bg-red-900/20 border border-red-500/20 rounded-[20px] text-red-400 hover:bg-red-900/40 transition-all font-black uppercase tracking-widest text-sm">
            <span class="material-symbols-outlined">logout</span>
            Log Out
        </a>
    </main>

    <?php include('../components/bottom_nav_student.php'); ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetch('../api/get_profile.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('profile-name').textContent = `${data.user.first_name} ${data.user.last_name}`;
                    document.getElementById('profile-id').textContent = `ID: ${data.user.dlsu_id}`;
                    document.getElementById('profile-dept').textContent = `${data.user.program} | ${data.user.college}`;
                    document.getElementById('stat-total').textContent = data.stats.total_claims;
                    document.getElementById('stat-active').textContent = data.stats.active_claims;

                    // Claims List
                    const activeContainer = document.getElementById('active-claims-container');
                    activeContainer.innerHTML = data.active_reservations.length === 0 
                        ? '<p class="text-[#1A2419]/60 font-medium">No active claims.</p>'
                        : data.active_reservations.map(claim => `
                            <div class="flex items-center gap-4 p-4 bg-[#1A2419] rounded-[20px] text-white">
                                <div class="w-12 h-12 rounded-full bg-[#c6f135]/20 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[#c6f135]">confirmation_number</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-sm">${claim.title}</h3>
                                    <p class="text-xs text-white/60">${claim.location} • ${claim.formatted_time}</p>
                                </div>
                                <button class="bg-[#c6f135] text-[#1A2419] font-bold text-xs px-4 py-2 rounded-full uppercase tracking-wider">QR</button>
                            </div>
                        `).join('');

                    // History List
                    const historyContainer = document.getElementById('history-container');
                    historyContainer.innerHTML = data.history.length === 0 
                        ? '<p class="text-white/60">No history yet.</p>'
                        : data.history.map(item => `
                            <div class="flex justify-between items-center py-3 border-b border-white/10 last:border-0">
                                <div>
                                    <p class="font-bold text-sm">${item.title}</p>
                                    <p class="text-xs text-white/50">${item.date}</p>
                                </div>
                                <span class="bg-white/10 text-white/70 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">${item.status}</span>
                            </div>
                        `).join('');
                });
        });
    </script>
</body>
</html>