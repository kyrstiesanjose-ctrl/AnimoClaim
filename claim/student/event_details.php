<?php 
require_once('../config/auth.php'); 
require_once('../config/database.php'); 

// Get the event ID from the URL (default to 1 if not provided for testing)
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// 1. Fetch Event Details
$stmt = $conn->prepare("SELECT title, description, location, image_url FROM events WHERE id = ? AND is_active = 1");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    // Redirect back to events if the ID doesn't exist
    header("Location: events.php");
    exit();
}

// 2. Fetch Inventory Details
$stmt_inv = $conn->prepare("SELECT total_quantity, remaining_quantity FROM inventory WHERE event_id = ?");
$stmt_inv->bind_param("i", $event_id);
$stmt_inv->execute();
$inventory = $stmt_inv->get_result()->fetch_assoc();

$total_qty = $inventory['total_quantity'] ?? 100; // Fallback to avoid division by zero
$remain_qty = $inventory['remaining_quantity'] ?? 0;
$capacity_percent = $total_qty > 0 ? round(($remain_qty / $total_qty) * 100) : 0;

// 3. Fetch Time Slots
$stmt_slots = $conn->prepare("SELECT id, start_time, end_time, max_capacity, current_reservations FROM event_time_slots WHERE event_id = ? ORDER BY start_time ASC");
$stmt_slots->bind_param("i", $event_id);
$stmt_slots->execute();
$slots_result = $stmt_slots->get_result();
$time_slots = [];
while ($row = $slots_result->fetch_assoc()) {
    $time_slots[] = $row;
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>AnimoClaim - <?php echo htmlspecialchars($event['title']); ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body {
            background-color: #f2fed9;
            color: #1c261b;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .custom-scrollbar::-webkit-scrollbar { height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5e9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #c6f135; border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen pb-24 overflow-x-hidden relative">

    <?php
        $header_back_url = 'events.php';
        $header_title = 'Event Details';
        $header_sidebar = false;
        include('../components/header_student.php');
    ?>

    <main class="max-w-md mx-auto px-4 pt-24 space-y-6">
        <!-- Hero Section -->
        <section class="relative rounded-xl overflow-hidden border border-[#e5e7eb]">
            <div class="h-64 w-full bg-cover bg-center" style="background-image: url('../assets/images/<?php echo htmlspecialchars($event['image_url'] ?? 'default.jpg'); ?>')"></div>
            <div class="absolute top-4 left-4 bg-[#c6f135] text-[#1c261b] px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-lg">
                Active
            </div>
            <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/90 via-black/50 to-transparent">
                <h2 class="text-3xl font-extrabold text-white leading-tight drop-shadow-md">
                    <?php echo htmlspecialchars($event['title']); ?>
                </h2>
            </div>
        </section>

        <!-- Metadata Grid -->
        <section class="grid grid-cols-2 gap-4">
            <?php 
                $display_date = "TBA";
                $display_time = "TBA";
                if (!empty($time_slots)) {
                    $first_slot = strtotime($time_slots[0]['start_time']);
                    $display_date = date('M d, Y', $first_slot);
                    $display_time = date('H:i', $first_slot) . " onwards";
                }
            ?>
            <div class="bg-white p-4 rounded-xl border border-[#e5e7eb]">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-[#7ba82a] text-sm">calendar_month</span>
                    <span class="text-[10px] uppercase text-[#1c261b]/60 tracking-wider">Date</span>
                </div>
                <p class="font-bold text-[#1c261b] text-sm"><?php echo $display_date; ?></p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-[#e5e7eb]">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-[#7ba82a] text-sm">schedule</span>
                    <span class="text-[10px] uppercase text-[#1c261b]/60 tracking-wider">Time</span>
                </div>
                <p class="font-bold text-[#1c261b] text-sm"><?php echo $display_time; ?></p>
            </div>
            <div class="col-span-2 bg-white p-4 rounded-xl border border-[#e5e7eb] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-[#f2fed9] p-2 rounded-lg">
                        <span class="material-symbols-outlined text-[#7ba82a]">location_on</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase text-[#1c261b]/60 tracking-wider">Location</span>
                        <p class="font-bold text-[#1c261b]"><?php echo htmlspecialchars($event['location']); ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Description -->
        <section class="space-y-3">
            <h3 class="text-[10px] uppercase text-[#7ba82a] tracking-[0.2em] font-bold">Mission Intel</h3>
            <div class="bg-white p-5 rounded-xl border border-[#e5e7eb] relative overflow-hidden">
                <p class="text-[#1c261b]/70 text-sm leading-relaxed relative z-10">
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </p>
                <div class="absolute bottom-0 right-0 p-2 opacity-5 z-0">
                    <span class="material-symbols-outlined text-6xl text-[#1c261b]">terminal</span>
                </div>
            </div>
        </section>

        <!-- Inventory Summary -->
        <section class="bg-white p-5 rounded-xl border border-[#e5e7eb] space-y-4">
            <div class="flex justify-between items-end">
                <div>
                    <h3 class="text-[10px] uppercase text-[#1c261b]/60 tracking-wider">Remaining Units</h3>
                    <p class="text-2xl font-bold text-[#1c261b] mt-1">
                        <?php echo $remain_qty; ?>
                        <span class="text-[#1c261b]/40 text-sm">/<?php echo $total_qty; ?></span>
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-2 py-1 bg-[#c6f135]/20 rounded-lg text-[10px] font-bold text-[#7ba82a] uppercase tracking-tighter">
                        <?php echo $capacity_percent; ?>% Capacity
                    </span>
                </div>
            </div>
            <div class="w-full bg-[#f2fed9] h-3 rounded-full overflow-hidden flex">
                <div class="bg-[#c6f135] h-full transition-all duration-1000" style="width: <?php echo $capacity_percent; ?>%"></div>
            </div>
        </section>

        <!-- Scheduling Module -->
        <section class="space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-[10px] uppercase text-[#7ba82a] tracking-[0.2em] font-bold">Claiming Slot Selection</h3>
                <span class="text-[10px] text-[#1c261b]/60 italic">Tap to select</span>
            </div>

            <!-- Time Grid (Dynamic from DB) -->
            <div class="grid grid-cols-3 gap-3" id="time-slot-container">
                <?php if (empty($time_slots)): ?>
                    <div class="col-span-3 text-center text-sm text-[#1c261b]/60 py-4">No time slots available yet.</div>
                <?php else: ?>
                    <?php foreach ($time_slots as $index => $slot): 
                        $time_str = date('h:i A', strtotime($slot['start_time']));
                        // Disable if full
                        $is_full = $slot['current_reservations'] >= $slot['max_capacity'];
                        $button_class = $is_full ? 
                            "opacity-40 cursor-not-allowed bg-white border border-[#e5e7eb] rounded-lg text-[#1c261b]/60" : 
                            "bg-white border border-[#e5e7eb] rounded-lg text-[#1c261b]/70 hover:border-[#c6f135] transition-colors time-slot-btn";
                    ?>
                        <button class="p-3 text-xs text-center <?php echo $button_class; ?>" 
                                data-slot-id="<?php echo $slot['id']; ?>"
                                <?php echo $is_full ? 'disabled' : ''; ?>>
                            <?php echo $time_str; ?>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <input type="hidden" id="selected_slot_id" name="slot_id" value="">
        </section>

        <!-- CTA Action -->
        <section class="pt-4 pb-8">
            <button id="claim-btn" class="w-full bg-[#c6f135] text-[#1c261b] h-16 rounded-full font-black text-lg uppercase tracking-wider flex items-center justify-center gap-3 shadow-lg active:scale-[0.98] transition-transform duration-75 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">confirmation_number</span>
                Claim Now
            </button>
            <p class="text-center text-[10px] text-[#1c261b]/60 mt-4 uppercase tracking-widest opacity-60">
                Single Use Token • Non-Transferable
            </p>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const timeSlots = document.querySelectorAll('.time-slot-btn');
            const hiddenInput = document.getElementById('selected_slot_id');
            const claimBtn = document.getElementById('claim-btn');

            claimBtn.disabled = true;

            timeSlots.forEach(slot => {
                slot.addEventListener('click', () => {
                    timeSlots.forEach(s => {
                        s.classList.remove('bg-[#c6f135]/20', 'border-2', 'border-[#c6f135]', 'text-[#7ba82a]', 'font-bold');
                        s.classList.add('bg-white', 'border', 'border-[#e5e7eb]', 'text-[#1c261b]/70');
                    });
                    
                    slot.classList.remove('bg-white', 'border', 'border-[#e5e7eb]', 'text-[#1c261b]/70');
                    slot.classList.add('bg-[#c6f135]/20', 'border-2', 'border-[#c6f135]', 'text-[#7ba82a]', 'font-bold');
                    
                    hiddenInput.value = slot.getAttribute('data-slot-id');
                    claimBtn.disabled = false;
                });
            });

            claimBtn.addEventListener('click', () => {
                const slotId = hiddenInput.value;
                if(slotId) {
                    alert("Ready to submit reservation for Slot ID: " + slotId + ". We will build the backend submission script next!");
                }
            });
        });
    </script>
</body>
</html>