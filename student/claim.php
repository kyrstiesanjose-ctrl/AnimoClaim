<?php 
require_once('../config/database.php');
requireLogin('student');
$current_page = 'to_claim'; 
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnimoClaim - To Claim</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f2fed9; color: #191C19; }
        .ticket-dashed { border-top: 2px dashed rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="md:pl-72 min-h-screen">
    
    <!-- Sidebar Desktop -->
    <?php include('../components/sidebar_student.php'); ?>

    <!-- Header Responsive -->

    
    <!-- Dark Top Header Block -->
    <header class="w-full bg-[#1A2419] px-6 py-6 flex justify-between items-center rounded-b-[24px] md:rounded-bl-none md:rounded-br-[32px] shadow-md z-40 relative md:fixed md:top-0 md:left-72 md:w-[calc(100%-18rem)]">
        <h1 class="text-[24px] font-black text-[#c6f135] tracking-tight">To Claim</h1>
        <button class="relative w-10 h-10 flex items-center justify-center rounded-full text-[#c6f135] hover:bg-white/10 transition-all">
            <span class="material-symbols-outlined">notifications</span>
        </button>
    </header>


    <main class="pt-24 pb-28 px-4 md:px-8 max-w-7xl mx-auto space-y-6">
        
        <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-[#c6f135] text-3xl">confirmation_number</span>
            <h2 class="text-2xl font-bold text-[#1c261b] uppercase tracking-wide">My Active Claims</h2>
        </div>

        <!-- Claims Grid Container -->
        <div id="claims-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <p class="text-[#1c261b]/50 text-center py-12 col-span-full">Loading your tickets...</p>
        </div>

    </main>

    <!-- Mobile Nav -->
    <?php include('../components/bottom_nav_student.php'); ?>
    
    <script defer>
        document.addEventListener('DOMContentLoaded', () => {
            fetch('../api/get_claims.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('claims-container');
                    container.innerHTML = ''; // Clear loading text
                    
                    if (data.length === 0) {
                        container.innerHTML = `
                            <div class="col-span-full bg-[#1c261b] border border-white/5 rounded-2xl p-12 text-center shadow-md">
                                <span class="material-symbols-outlined text-white/20 text-6xl mb-4">sentiment_dissatisfied</span>
                                <h3 class="text-xl font-bold text-white mb-2">No active claims found</h3>
                                <p class="text-white/50">Head over to the events page to reserve your kits!</p>
                            </div>
                        `;
                        return;
                    }

                    data.forEach(claim => {
                        // Generate a live QR code using an external API based on the database hash
                        const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${claim.qr_code_hash}&color=1c261b&bgcolor=c6f135`;
                        
                        container.innerHTML += `
                            <div class="bg-[#1c261b] border border-white/10 rounded-2xl overflow-hidden shadow-lg flex flex-col group hover:border-[#c6f135] transition-colors relative">
                                <!-- Top: Event Details -->
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-800 flex-shrink-0">
                                            <img src="${claim.image_url || 'https://lh3.googleusercontent.com/aida-public/AB6AXuA4IRi4f9WcLk_Yv0KE7a4AXqVAzOzHNxM2ZCY2fi4TgLvTrjUDgoJ8ZjFuIr75TMkF8w0oeuBFeUuN4phVCbasr0Jen8i9_U6LrzqXNM60r-ImnGPxaMdHkMfr6i2VxS1_H3PdpqzQAh4aHEsuB-ssblaf_i0k-E52yvO79qqgHjKMj12oWUhvtXeRji-196n2zmgJ3cJxvM5s62ZWe_gESOAfkX8tTfr-WCaaYCk2vXVNfq_j_DCSUL-pw22sy9jzW7lQAbOqtqk'}" class="w-full h-full object-cover">
                                        </div>
                                        <span class="bg-[#c6f135] text-[#1c261b] px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Reserved</span>
                                    </div>
                                    <h3 class="text-[18px] font-bold text-white leading-tight mb-2">${claim.title}</h3>
                                    
                                    <div class="space-y-2 mt-4">
                                        <div class="flex items-center gap-2 text-white/70 text-[12px]">
                                            <span class="material-symbols-outlined text-[16px] text-[#c6f135]">calendar_month</span>
                                            ${claim.formatted_date}
                                        </div>
                                        <div class="flex items-center gap-2 text-white/70 text-[12px]">
                                            <span class="material-symbols-outlined text-[16px] text-[#c6f135]">schedule</span>
                                            ${claim.formatted_time}
                                        </div>
                                        <div class="flex items-center gap-2 text-white/70 text-[12px]">
                                            <span class="material-symbols-outlined text-[16px] text-[#c6f135]">location_on</span>
                                            ${claim.location}
                                        </div>
                                    </div>
                                </div>

                                <!-- Bottom: QR Code Section (Dashed line separator) -->
                                <div class="ticket-dashed p-6 bg-[#0f2419] flex flex-col items-center justify-center mt-auto relative">
                                    <!-- Ticket notches -->
                                    <div class="absolute -top-3 -left-3 w-6 h-6 bg-[#011809] rounded-full border-b border-r border-white/10 rotate-45"></div>
                                    <div class="absolute -top-3 -right-3 w-6 h-6 bg-[#011809] rounded-full border-b border-l border-white/10 -rotate-45"></div>
                                    
                                    <div class="bg-[#c6f135] p-2 rounded-xl mb-3 shadow-md">
                                        <img src="${qrCodeUrl}" alt="QR Code" class="w-32 h-32 rounded-lg">
                                    </div>
                                    <p class="text-[10px] text-white/50 uppercase tracking-widest font-mono">${claim.qr_code_hash}</p>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Error fetching claims:', error);
                    document.getElementById('claims-container').innerHTML = '<p class="text-red-500 text-center py-12 col-span-full">Failed to load claims.</p>';
                });
        });
    </script>
    <script src="../assets/javascript/main.js" defer></script>
</body>
</html>