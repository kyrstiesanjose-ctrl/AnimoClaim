<?php
require_once '../config/database.php';
requireLogin('student');
require_once '../includes/header.php';

// Fetch events with remaining inventory math and their earliest slot
$stmt = $pdo->query("
    SELECT e.*,
           COALESCE((SELECT SUM(remaining_quantity) FROM inventory WHERE event_id = e.id), 0) as remaining_qty,
           (SELECT start_time FROM event_time_slots WHERE event_id = e.id ORDER BY start_time ASC LIMIT 1) as first_slot_time
    FROM events e
    WHERE e.is_active = 1
");
$events = $stmt->fetchAll();
?>

<div class="space-y-4">

    <!-- Search & Category Filters -->
    <div class="space-y-2">
        <div class="relative">
            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"></i>
            <input
                type="text"
                id="eventSearch"
                placeholder="Search DLSU giveaway events..."
                class="w-full pl-11 pr-9 h-11 rounded-full bg-white border border-[#0e0f0c]/12 shadow-2xs outline-none text-[#0e0f0c] font-semibold placeholder:text-gray-400 text-xs sm:text-sm focus:ring-2 focus:ring-[#9fe870] focus:border-[#9fe870] transition-all"
            />
            <button id="clearSearch" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Filter Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto hide-scrollbar px-0.5 py-0.5">
            <button class="filter-pill h-8 px-4 rounded-full text-[11px] font-bold uppercase tracking-wider transition-all wise-btn bg-[#0e0f0c] text-[#9fe870] flex-shrink-0" data-cat="all">
                All Events
            </button>
            <button class="filter-pill h-8 px-4 rounded-full text-[11px] font-bold uppercase tracking-wider transition-all wise-btn bg-white text-[#0e0f0c] border border-[#0e0f0c]/12 hover:bg-gray-50 flex-shrink-0" data-cat="Giveaway">
                Giveaways
            </button>
            <button class="filter-pill h-8 px-4 rounded-full text-[11px] font-bold uppercase tracking-wider transition-all wise-btn bg-white text-[#0e0f0c] border border-[#0e0f0c]/12 hover:bg-gray-50 flex-shrink-0" data-cat="Wellness">
                Wellness
            </button>
            <button class="filter-pill h-8 px-4 rounded-full text-[11px] font-bold uppercase tracking-wider transition-all wise-btn bg-white text-[#0e0f0c] border border-[#0e0f0c]/12 hover:bg-gray-50 flex-shrink-0" data-cat="Academic">
                Academic
            </button>
            <button class="filter-pill h-8 px-4 rounded-full text-[11px] font-bold uppercase tracking-wider transition-all wise-btn bg-white text-[#0e0f0c] border border-[#0e0f0c]/12 hover:bg-gray-50 flex-shrink-0" data-cat="Assembly">
                Assembly
            </button>
        </div>
    </div>

    <!-- Giveaways Featured Carousel -->
    <div class="space-y-2">
        <div class="flex justify-between items-center px-0.5">
            <h2 class="text-base wise-heading text-[#0e0f0c] uppercase">
                Featured Hot Events
            </h2>
            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider"><?php echo count($events); ?> Releases</span>
        </div>

        <div class="flex overflow-x-auto gap-3 pb-2 hide-scrollbar px-0.5 snap-x snap-mandatory">
            <?php foreach ($events as $event): ?>
                <a
                    href="/claim/student/event_details.php?id=<?php echo $event['id']; ?>"
                    class="event-card snap-start flex-shrink-0 w-[210px] sm:w-[240px] bg-white rounded-2xl overflow-hidden border border-[#0e0f0c]/12 flex flex-col cursor-pointer relative wise-btn shadow-2xs"
                    data-title="<?php echo htmlspecialchars($event['title']); ?>"
                    data-location="<?php echo htmlspecialchars($event['location']); ?>"
                    data-category="<?php echo htmlspecialchars($event['category']); ?>"
                >
                    <div class="h-28 w-full relative overflow-hidden bg-[#e2f6d5]">
                        <span class="absolute top-2 left-2 bg-[#0e0f0c] text-[#9fe870] font-extrabold text-[8px] px-2 py-0.5 rounded-full uppercase tracking-wider z-10">
                            <?php echo htmlspecialchars($event['category']); ?>
                        </span>

                        <span class="absolute top-2 right-2 bg-white text-[#0e0f0c] font-extrabold text-[8px] px-2 py-0.5 rounded-full uppercase tracking-wider z-10 shadow-2xs flex items-center gap-1 border border-[#0e0f0c]/10">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-ping"></span> <?php echo $event['remaining_qty']; ?> left
                        </span>

                        <img src="/claim/assets/pictures/<?php echo htmlspecialchars($event['image_url'] ?: 'Event_Poster.png'); ?>" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" alt="<?php echo htmlspecialchars($event['title']); ?>" onerror="this.src='/claim/assets/pictures/Event_Poster.png'" />
                    </div>

                    <div class="p-3 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-extrabold text-xs sm:text-sm text-[#0e0f0c] leading-snug mb-1 truncate" title="<?php echo htmlspecialchars($event['title']); ?>">
                                <?php echo htmlspecialchars($event['title']); ?>
                            </h3>
                            <div class="flex flex-col gap-0.5 text-[10px] text-gray-500 font-medium mb-2">
                                <div class="flex items-center gap-1.5 text-[#163300] font-bold">
                                    <i data-lucide="calendar" class="w-3 h-3 text-[#163300] flex-shrink-0"></i>
                                    <span><?php echo $event['first_slot_time'] ? date('M j, g:i A', strtotime($event['first_slot_time'])) : 'TBA'; ?></span>
                                </div>
                                <div class="flex items-center gap-1.5 truncate">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-gray-400 flex-shrink-0"></i>
                                    <span class="truncate"><?php echo htmlspecialchars($event['location']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-gray-100 flex items-center justify-between mt-auto">
                            <span class="text-[9px] font-extrabold text-[#163300] bg-[#e2f6d5] px-2 py-0.5 rounded-full">FREE CLAIM</span>
                            <span class="bg-[#9fe870] text-[#163300] font-black text-[9px] px-2.5 py-1 rounded-full uppercase tracking-wider">
                                Claim Slot
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- All Campaigns Vertical List -->
    <div class="space-y-2">
        <h2 class="text-base wise-heading text-[#0e0f0c] uppercase px-0.5">All Available Campaign Events</h2>

        <div class="space-y-2" id="vertical-event-list">
            <?php foreach ($events as $event):
                $formattedTime = $event['first_slot_time'] ? date('M j, g:i A', strtotime($event['first_slot_time'])) : "TBA";
            ?>
                <a
                    href="/claim/student/event_details.php?id=<?php echo $event['id']; ?>"
                    class="event-card flex items-center gap-3 p-3 bg-white rounded-2xl border border-[#0e0f0c]/12 cursor-pointer transition-all wise-btn shadow-2xs"
                    data-title="<?php echo htmlspecialchars($event['title']); ?>"
                    data-location="<?php echo htmlspecialchars($event['location']); ?>"
                    data-category="<?php echo htmlspecialchars($event['category']); ?>"
                >
                    <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 relative bg-[#e2f6d5]">
                        <img src="/claim/assets/pictures/<?php echo htmlspecialchars($event['image_url'] ?: 'Event_Poster.png'); ?>" loading="lazy" class="w-full h-full object-cover" alt="" onerror="this.src='/claim/assets/pictures/Event_Poster.png'" />
                    </div>

                    <div class="flex-1 min-w-0 pr-1">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="text-[8px] font-extrabold uppercase tracking-wider text-[#0e0f0c] bg-gray-100 px-2 py-0.5 rounded-full">
                                <?php echo htmlspecialchars($event['category']); ?>
                            </span>
                            <span class="text-[9px] font-bold text-green-700">
                                <?php echo $event['remaining_qty']; ?> available
                            </span>
                        </div>
                        <h3 class="text-[#0e0f0c] font-extrabold text-xs sm:text-sm truncate leading-tight mb-0.5">
                            <?php echo htmlspecialchars($event['title']); ?>
                        </h3>
                        <div class="flex items-center gap-2 text-gray-500 text-[10px] font-medium">
                            <div class="flex items-center gap-1 text-[#163300] font-bold">
                                <i data-lucide="calendar" class="w-3 h-3 text-[#163300] flex-shrink-0"></i>
                                <span><?php echo $formattedTime; ?></span>
                            </div>
                            <span class="text-gray-300">&bull;</span>
                            <div class="flex items-center gap-1 truncate">
                                <i data-lucide="map-pin" class="w-3 h-3 text-gray-400 flex-shrink-0"></i>
                                <span class="truncate"><?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="w-8 h-8 rounded-full bg-[#9fe870] flex items-center justify-center flex-shrink-0 text-[#163300] font-black">
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('eventSearch');
        const clearBtn = document.getElementById('clearSearch');
        const filterPills = document.querySelectorAll('.filter-pill');
        let activeCategory = 'all';

        function filterEvents() {
            const searchTerm = (searchInput.value || '').toLowerCase();
            const cards = document.querySelectorAll('.event-card');

            cards.forEach(card => {
                const title = (card.getAttribute('data-title') || '').toLowerCase();
                const location = (card.getAttribute('data-location') || '').toLowerCase();
                const category = (card.getAttribute('data-category') || '');

                const matchesSearch = title.includes(searchTerm) || location.includes(searchTerm) || category.toLowerCase().includes(searchTerm);
                const matchesCategory = activeCategory === 'all' || category.toLowerCase() === activeCategory.toLowerCase();

                if (matchesSearch && matchesCategory) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput?.addEventListener('input', (e) => {
            if (e.target.value) {
                clearBtn?.classList.remove('hidden');
            } else {
                clearBtn?.classList.add('hidden');
            }
            filterEvents();
        });

        clearBtn?.addEventListener('click', () => {
            searchInput.value = '';
            clearBtn.classList.add('hidden');
            filterEvents();
        });

        filterPills.forEach(pill => {
            pill.addEventListener('click', () => {
                filterPills.forEach(p => {
                    p.classList.remove('bg-[#0e0f0c]', 'text-[#9fe870]');
                    p.classList.add('bg-white', 'text-[#0e0f0c]', 'border', 'border-[#0e0f0c]/12');
                });
                pill.classList.remove('bg-white', 'text-[#0e0f0c]', 'border', 'border-[#0e0f0c]/12');
                pill.classList.add('bg-[#0e0f0c]', 'text-[#9fe870]');

                activeCategory = pill.getAttribute('data-cat');
                filterEvents();
            });
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>