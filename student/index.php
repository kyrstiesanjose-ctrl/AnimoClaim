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

<div class="space-y-6">
    <!-- Live Search Bar -->
    <div class="flex gap-3">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5"></i>
            <input 
                type="text" 
                id="eventSearch"
                placeholder="Search DLSU giveaway campaigns..." 
                class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-white border border-gray-200/80 shadow-sm outline-none text-gray-800 font-semibold placeholder:text-gray-400 text-sm focus:ring-2 focus:ring-[#c6f135]/50 focus:border-[#c6f135] transition-all"
            />
        </div>
        <button class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-200/80 shadow-sm text-gray-500 hover:text-[#1c261b] transition-colors cursor-pointer">
            <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Giveaways Featured Carousel -->
    <div class="space-y-3">
        <h2 class="text-lg font-black text-[#1A2419] tracking-tight uppercase flex items-center gap-2">
            <i data-lucide="trending-up" class="w-5 h-5 text-[#7ba82a]"></i>
            Featured Giveaways
        </h2>
        <div class="flex overflow-x-auto gap-4 pb-4 hide-scrollbar -mx-4 px-4 md:mx-0 md:px-0 snap-x snap-mandatory">
            <?php foreach ($events as $event): ?>
                <a 
                    href="/claim/student/event_details.php?id=<?php echo $event['id']; ?>" 
                    class="event-card snap-start flex-shrink-0 w-[280px] bg-[#1c261b] rounded-3xl overflow-hidden shadow-lg flex flex-col cursor-pointer relative hover:-translate-y-1.5 transition-all duration-300 border border-white/5"
                    data-title="<?php echo htmlspecialchars($event['title']); ?>"
                    data-location="<?php echo htmlspecialchars($event['location']); ?>"
                    data-category="<?php echo htmlspecialchars($event['category']); ?>"
                >
                    <div class="h-40 w-full relative overflow-hidden">
                        <span class="absolute top-4 left-4 bg-white/95 backdrop-blur-md rounded-xl px-2.5 py-1 text-[9px] font-black text-[#1c261b] uppercase tracking-wider z-10 shadow-sm">
                            <?php echo htmlspecialchars($event['category']); ?>
                        </span>
                        <!-- Updated path to assets/pictures/ -->
                        <img src="/claim/assets/pictures/<?php echo htmlspecialchars($event['image_url']); ?>" loading="lazy" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($event['title']); ?>" />
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between bg-[#1c261b] text-white">
                        <div>
                            <h3 class="font-bold text-sm text-white leading-tight mb-1 truncate" title="<?php echo htmlspecialchars($event['title']); ?>">
                                <?php echo htmlspecialchars($event['title']); ?>
                            </h3>
                            <div class="flex items-center gap-1.5 text-[#c6f135] mb-4">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                <p class="text-[10px] font-bold truncate"><?php echo htmlspecialchars($event['location']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-auto pt-2 border-t border-white/5">
                            <p class="text-[10px] font-bold text-white/50"><?php echo $event['remaining_qty']; ?> units remaining</p>
                            <span class="bg-[#c6f135] text-[#1c261b] font-black text-[10px] px-3 py-1.5 rounded-full uppercase tracking-wider">
                                Claim
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- All Campaigns Vertical List -->
    <div class="space-y-3">
        <h2 class="text-lg font-black text-[#1A2419] tracking-tight uppercase">Upcoming Releases</h2>
        <div class="space-y-3" id="vertical-event-list">
            <?php foreach ($events as $event): 
                $formattedTime = $event['first_slot_time'] ? date('M j, g:i A', strtotime($event['first_slot_time'])) : "TBA";
            ?>
                <a 
                    href="/claim/student/event_details.php?id=<?php echo $event['id']; ?>" 
                    class="event-card flex items-center gap-4 p-3 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md cursor-pointer transition-all hover:-translate-y-0.5"
                    data-title="<?php echo htmlspecialchars($event['title']); ?>"
                    data-location="<?php echo htmlspecialchars($event['location']); ?>"
                    data-category="<?php echo htmlspecialchars($event['category']); ?>"
                >
                    <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 relative">
                        <img src="/claim/assets/pictures/Event_Poster.png" loading="lazy" class="w-full h-full object-cover" alt="" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[#1c261b] font-bold text-xs md:text-sm truncate leading-tight mb-0.5">
                            <?php echo htmlspecialchars($event['title']); ?>
                        </h3>
                        <p class="text-[#7ba82a] text-[10px] font-bold mb-1 uppercase tracking-wider">
                            <?php echo htmlspecialchars($event['category']); ?>
                        </p>
                        <div class="flex items-center gap-3 text-gray-500 text-[10px] font-medium">
                            <div class="flex items-center gap-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-gray-400"></i>
                                <span><?php echo $formattedTime; ?></span>
                            </div>
                            <div class="flex items-center gap-1 truncate">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400"></i>
                                <span class="truncate"><?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-[#c6f135] flex items-center justify-center flex-shrink-0 text-[#1c261b] font-bold hover:scale-105 transition-transform">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    // Live Search Filtering Logic
    document.getElementById('eventSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.event-card');

        cards.forEach(card => {
            const title = card.getAttribute('data-title').toLowerCase();
            const location = card.getAttribute('data-location').toLowerCase();
            const category = card.getAttribute('data-category').toLowerCase();

            if (title.includes(searchTerm) || location.includes(searchTerm) || category.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>