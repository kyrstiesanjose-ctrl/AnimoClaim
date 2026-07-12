<?php 
require_once('../config/auth.php'); 
$current_page = 'events'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>AnimoClaim - Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #F4FCE3; /* Pale green from mockup */
            font-family: 'Montserrat', sans-serif;
            color: #1A2419;
        }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="md:pl-72 min-h-screen selection:bg-[#c6f135] selection:text-[#1A2419] overflow-x-hidden pb-32">
    
    <?php include('../components/sidebar_student.php'); ?>

    <!-- Dark Top Header Block -->
    <header class="w-full bg-[#1A2419] px-6 py-6 flex justify-between items-center rounded-b-[24px] md:rounded-bl-none md:rounded-br-[32px] shadow-md z-40 relative md:fixed md:top-0 md:left-72 md:w-[calc(100%-18rem)]">
        <h1 class="text-[24px] font-black text-[#c6f135] tracking-tight">Events</h1>
        <button class="relative w-10 h-10 flex items-center justify-center rounded-full text-[#c6f135] hover:bg-white/10 transition-all">
            <span class="material-symbols-outlined">notifications</span>
        </button>
    </header>

    <main class="md:pt-28 max-w-5xl mx-auto px-5 relative z-10">
        
        <!-- Search & Filter Bar -->
        <section class="flex items-center gap-3 mt-6">
            <div class="relative flex-1 group">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                <input class="w-full pl-12 pr-4 py-3.5 rounded-[16px] bg-white border-none focus:ring-2 focus:ring-[#c6f135]/50 shadow-sm outline-none text-gray-800 font-semibold placeholder:text-gray-400 placeholder:font-medium text-sm" placeholder="Find upcoming events..." type="text">
            </div>
            <button class="w-[48px] h-[48px] flex items-center justify-center rounded-[16px] bg-white shadow-sm hover:shadow-md transition-all text-gray-700 flex-shrink-0">
                <span class="material-symbols-outlined text-[20px]">tune</span>
            </button>
        </section>

        <!-- Giveaways Carousel -->
        <section class="mt-8">
            <h2 class="text-[20px] font-extrabold text-[#1A2419] mb-4 tracking-tight">Giveaways</h2>
            
            <div id="featured_container" class="flex overflow-x-auto gap-4 pb-4 hide-scrollbar snap-x snap-mandatory -mx-5 px-5 md:mx-0 md:px-0">
                <!-- Skeletons -->
                <p class="text-gray-500 font-medium">Loading events...</p>
            </div>
        </section>

        <!-- Upcoming Events Vertical List -->
        <section class="mt-6 mb-8">
            <h2 class="text-[20px] font-extrabold text-[#1A2419] tracking-tight mb-4">Upcoming Events</h2>
            
            <div id="events_container" class="space-y-4">
                 <!-- Skeletons -->
                 <p class="text-gray-500 font-medium">Loading events...</p>
            </div>
        </section>
    </main>

    <?php include('../components/bottom_nav_student.php'); ?>

    <script defer>
        document.addEventListener('DOMContentLoaded', () => {
            fetch('../api/get_events.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('events_container');
                    const featuredContainer = document.getElementById('featured_container');
                    
                    container.innerHTML = ''; 
                    featuredContainer.innerHTML = '';
                    
                    if (data.length === 0) {
                        container.innerHTML = '<p class="text-[#1A2419]/60 font-medium py-8">No active events found.</p>';
                        featuredContainer.innerHTML = '';
                        return;
                    }

                    // Temporary Organizer Mapping (Will be replaced when you join the organizer table later)
                    const organizerMap = { "1": "LCSG x DLSU Puso", "2": "Career Services Center", "3": "Cultural Affairs Office" };

                    data.forEach((event, index) => {
                        const organizerName = organizerMap[event.organizer] || 'De La Salle University';
                        const imgSrc = event.image_url ? `../assets/images/${event.image_url}` : 'https://images.unsplash.com/photo-1540317580384-e5d43867caa6?auto=format&fit=crop&w=600&q=80';
                        
                        // First 2 items go to Featured (Big Cards)
                        if (index < 2) {
                            featuredContainer.innerHTML += `
                                <a href="event_details.php?id=${event.id}" class="snap-start flex-shrink-0 w-[280px] bg-[#1A2419] rounded-[24px] overflow-hidden shadow-md flex flex-col group cursor-pointer relative hover:-translate-y-1 transition-transform">
                                    <!-- Image Block -->
                                    <div class="h-[180px] w-full bg-gray-200 relative overflow-hidden">
                                        <!-- Date Badge Overlay -->
                                        <div class="absolute top-4 left-4 bg-white rounded-[12px] px-3 py-1.5 flex flex-col items-center justify-center shadow-md z-20">
                                            <span class="text-[9px] font-black text-[#1A2419] uppercase tracking-widest">${event.month}</span>
                                            <span class="text-[18px] font-black leading-none text-[#1A2419]">${event.day}</span>
                                        </div>
                                        <img src="${imgSrc}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" alt="Event">
                                    </div> 
                                    <!-- Content Block -->
                                    <div class="p-5 flex-1 flex flex-col relative z-10 bg-[#1A2419]">
                                        <h3 class="font-bold text-[16px] text-white leading-snug mb-1 truncate">${event.title}</h3>
                                        <div class="flex items-center gap-1.5 text-[#c6f135] mb-5">
                                            <span class="material-symbols-outlined text-[14px]">groups</span>
                                            <p class="text-[11px] font-bold truncate">${organizerName}</p>
                                        </div>
                                        
                                        <!-- Bottom Row -->
                                        <div class="flex items-center justify-between mt-auto">
                                            <div class="flex items-center gap-1 text-gray-400 max-w-[50%]">
                                                <span class="material-symbols-outlined text-[14px]">location_on</span>
                                                <p class="text-[11px] font-semibold truncate">${event.location}</p>
                                            </div>
                                            <button class="bg-[#c6f135] text-[#1A2419] font-black text-[11px] px-4 py-2 rounded-full uppercase tracking-wider">
                                                Claim
                                            </button>
                                        </div>
                                    </div>
                                </a>
                            `;
                        } else {
                            // The rest go to Upcoming Events (Horizontal List)
                            container.innerHTML += `
                                <a href="event_details.php?id=${event.id}" class="flex items-center gap-4 p-3 bg-[#1A2419] rounded-[20px] shadow-sm cursor-pointer hover:-translate-y-0.5 transition-transform group">
                                    <div class="w-[80px] h-[80px] rounded-[14px] overflow-hidden flex-shrink-0 relative">
                                        <!-- Mini Date Badge -->
                                        <div class="absolute top-1.5 left-1.5 bg-white rounded-[8px] px-2 py-0.5 flex flex-col items-center justify-center shadow-sm z-20">
                                            <span class="text-[7px] font-black text-[#1A2419] uppercase tracking-widest">${event.month}</span>
                                            <span class="text-[12px] font-black leading-none text-[#1A2419]">${event.day}</span>
                                        </div>
                                        <img src="${imgSrc}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    </div>
                                    <div class="flex-1 min-w-0 py-1">
                                        <h3 class="text-white font-bold text-[14px] leading-tight mb-1 truncate">${event.title}</h3>
                                        <p class="text-[#c6f135] text-[11px] font-bold truncate mb-1">${organizerName}</p>
                                        <div class="flex items-center gap-3 text-gray-400">
                                            <div class="flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[12px]">schedule</span>
                                                <p class="text-[10px] font-medium">${event.time_formatted}</p>
                                            </div>
                                            <div class="flex items-center gap-1 truncate">
                                                <span class="material-symbols-outlined text-[12px]">location_on</span>
                                                <p class="text-[10px] font-medium truncate">${event.location}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-[36px] h-[36px] rounded-full bg-[#c6f135] flex items-center justify-center flex-shrink-0 mr-2 text-[#1A2419]">
                                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                    </div>
                                </a>
                            `;
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    document.getElementById('events_container').innerHTML = '<p class="text-red-500 font-medium">Failed to load events.</p>';
                    document.getElementById('featured_container').innerHTML = '';
                });
        });
    </script>
</body>
</html>