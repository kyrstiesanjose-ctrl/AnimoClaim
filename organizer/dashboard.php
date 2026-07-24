<?php 
require_once '../config/database.php';
requireLogin('organizer');

// ==========================================
// HANDLE CAMPAIGN CREATION (FORM SUBMISSION)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_campaign'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token Validation Failed");
    }

    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $capacity = (int)$_POST['capacity'];
    $description = trim($_POST['description']);
    $organizer_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // 1. Insert Event
        $stmt = $pdo->prepare("INSERT INTO events (organizer_id, title, description, location, image_url, category, is_active) VALUES (?, ?, ?, ?, 'school_supplies.jpg', ?, 1)");
        $stmt->execute([$organizer_id, $title, $description, $location, $category]);
        $newEventId = $pdo->lastInsertId();

        // 2. Insert Inventory (Total capacity = capacity per slot * 3 slots)
        $total_inv = $capacity * 3;
        $invStmt = $pdo->prepare("INSERT INTO inventory (event_id, item_name, total_quantity, remaining_quantity) VALUES (?, ?, ?, ?)");
        $invStmt->execute([$newEventId, $title . ' Package', $total_inv, $total_inv]);

        // 3. Insert 3 Default Time Slots (9AM, 11AM, 1PM)
        $baseDate = date('Y-m-d', strtotime('+2 days')); // Schedule for 2 days from now
        $slots = [
            ['start' => "$baseDate 09:00:00", 'end' => "$baseDate 10:30:00"],
            ['start' => "$baseDate 11:00:00", 'end' => "$baseDate 12:30:00"],
            ['start' => "$baseDate 13:30:00", 'end' => "$baseDate 15:00:00"]
        ];

        $slotStmt = $pdo->prepare("INSERT INTO event_time_slots (event_id, start_time, end_time, max_capacity, current_reservations) VALUES (?, ?, ?, ?, 0)");
        foreach ($slots as $slot) {
            $slotStmt->execute([$newEventId, $slot['start'], $slot['end'], $capacity]);
        }

        $pdo->commit();
        $success_msg = "Successfully launched campaign '$title' with 3 schedule slots.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_msg = "Error creating campaign: " . $e->getMessage();
    }
}

// ==========================================
// FETCH DASHBOARD METRICS & DATA
// ==========================================

// 1. KPI Queries
$activeCampaigns = $pdo->query("SELECT COUNT(*) FROM events WHERE is_active = 1")->fetchColumn();
$totalReserved = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'reserved'")->fetchColumn();
$itemsClaimed = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'claimed'")->fetchColumn();
$totalExpired = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'expired'")->fetchColumn();

$totalReservationsEver = $totalReserved + $itemsClaimed + $totalExpired;
$noShowRate = $totalReservationsEver > 0 ? round(($totalExpired / $totalReservationsEver) * 100) : 0;

// 2. Active Campaigns List (with slot and capacity math)
$campaignsStmt = $pdo->query("
    SELECT e.*, 
        COALESCE((SELECT SUM(max_capacity) FROM event_time_slots WHERE event_id = e.id), 0) as total_capacity,
        COALESCE((SELECT SUM(current_reservations) FROM event_time_slots WHERE event_id = e.id), 0) as total_reservations,
        (SELECT COUNT(*) FROM event_time_slots WHERE event_id = e.id) as slot_count
    FROM events e
    WHERE e.is_active = 1
    ORDER BY e.created_at DESC
");
$campaigns = $campaignsStmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="space-y-6 relative">
    
    <?php if (isset($success_msg)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i data-lucide="check-circle-2" class="w-5 h-5"></i> <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>

    <!-- Stats KPI Widgets -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Active Campaigns</span>
            <span class="text-3xl font-black text-gray-900 font-mono mt-2"><?php echo $activeCampaigns; ?></span>
            <span class="text-[10px] text-green-600 font-bold mt-1">Live distribution</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Reserved</span>
            <span class="text-3xl font-black text-blue-600 font-mono mt-2"><?php echo $totalReserved; ?></span>
            <span class="text-[10px] text-gray-500 font-medium mt-1">Awaiting onsite claim</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Items Claimed</span>
            <span class="text-3xl font-black text-green-600 font-mono mt-2"><?php echo $itemsClaimed; ?></span>
            <span class="text-[10px] text-green-600 font-bold mt-1">100% Verified onsite</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">No-Show Rate</span>
            <span class="text-3xl font-black text-red-500 font-mono mt-2"><?php echo $noShowRate; ?>%</span>
            <span class="text-[10px] text-red-500 font-bold mt-1">Strike policy active</span>
        </div>
    </div>

    <!-- Campaign Header with Launch Button -->
    <div class="flex justify-between items-center bg-white p-4 rounded-2xl border border-gray-200/80">
        <div>
            <h3 class="font-black text-sm text-[#1A2419] uppercase tracking-wider">Active Distribution Campaigns</h3>
            <p class="text-[11px] text-gray-500">Configure capacities and monitor participant metrics</p>
        </div>
        <button onclick="toggleModal(true)" class="flex items-center gap-1.5 px-4 h-11 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all cursor-pointer shadow-md">
            <i data-lucide="plus" class="w-4 h-4"></i>
            New Campaign
        </button>
    </div>

    <!-- Campaigns List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($campaigns as $event): 
            $percent = $event['total_capacity'] > 0 ? ($event['total_reservations'] / $event['total_capacity']) * 100 : 0;
        ?>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col justify-between">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <span class="bg-blue-100 text-blue-800 text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-lg">
                            <?php echo htmlspecialchars($event['category']); ?>
                        </span>
                        <span class="text-[10px] font-mono text-gray-400 font-bold">Event ID: #<?php echo $event['id']; ?></span>
                    </div>
                    <h4 class="font-bold text-sm text-gray-900 leading-snug"><?php echo htmlspecialchars($event['title']); ?></h4>
                    <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                        <?php echo htmlspecialchars($event['location']); ?>
                    </p>
                    
                    <div class="mt-4 space-y-1.5">
                        <div class="flex justify-between text-[10px] font-bold">
                            <span class="text-gray-500">Booked Slots Headcount</span>
                            <span class="text-gray-900 font-mono"><?php echo $event['total_reservations']; ?> / <?php echo $event['total_capacity']; ?></span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-blue-600 h-full transition-all duration-1000" style="width: <?php echo $percent; ?>%"></div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-[10px] text-gray-500 font-mono"><?php echo $event['slot_count']; ?> claiming windows</span>
                    <a href="/claim/organizer/terminal.php?event_id=<?php echo $event['id']; ?>" class="px-3 py-1.5 bg-blue-100 text-blue-700 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-blue-200 transition-colors cursor-pointer">
                        Scan Claims
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Strike Policy Reminder -->
    <div class="bg-[#1c261b] text-white p-5 rounded-3xl border border-white/5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="space-y-1">
            <h4 class="font-bold text-sm text-[#c6f135] uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="flame" class="w-5 h-5 text-amber-500"></i>
                AnimoClaim Strike Penalty Configuration
            </h4>
            <p class="text-xs text-white/70 leading-relaxed max-w-2xl">
                The automated strike restriction mechanism tracks student attendance compliance. Students with 3 unexcused no-shows are temporarily restricted from booking upcoming drops to eliminate inventory waste.
            </p>
        </div>
        <a href="/claim/organizer/audits.php" class="h-10 px-4 bg-[#c6f135] text-[#1c261b] font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-[#b8e02d] transition-all cursor-pointer flex items-center justify-center">
            Manage Strikes
        </a>
    </div>

</div>

<!-- Create Campaign Modal Overlay -->
<div id="createModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl border border-gray-200 p-6 space-y-4 text-[#1A2419]">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-black uppercase tracking-wider">Launch New Campaign</h3>
            <button onclick="toggleModal(false)" class="p-1 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
            </button>
        </div>
        <form method="POST" class="space-y-3">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="create_campaign" value="1">
            
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Campaign Title</label>
                <input type="text" name="title" required placeholder="e.g., Blood Drive Snack Package" class="w-full px-4 h-11 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:outline-none text-xs font-bold" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Category</label>
                    <select name="category" class="w-full px-4 h-11 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:outline-none text-xs font-bold">
                        <option value="Giveaway">Giveaway</option>
                        <option value="Wellness">Wellness</option>
                        <option value="Assembly">Assembly</option>
                        <option value="Academic">Academic</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Fulfillment Spot</label>
                    <input type="text" name="location" required placeholder="e.g., Gokongwei Hall" class="w-full px-4 h-11 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:outline-none text-xs font-bold" />
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Max Capacity Per Slot</label>
                <input type="number" name="capacity" required min="5" max="500" value="50" class="w-full px-4 h-11 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:outline-none text-xs font-bold" />
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Campaign Description</label>
                <textarea name="description" rows="3" placeholder="Specify distribution mechanics and items included..." class="w-full p-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:outline-none text-xs font-medium"></textarea>
            </div>
            <div class="pt-4 flex gap-2">
                <button type="button" onclick="toggleModal(false)" class="flex-1 h-11 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-black uppercase tracking-widest rounded-xl transition-all cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="flex-1 h-11 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md cursor-pointer">
                    Launch Now
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(show) {
        const modal = document.getElementById('createModal');
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>