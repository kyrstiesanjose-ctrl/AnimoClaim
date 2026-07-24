<?php 
require_once '../config/database.php';
requireLogin('student');

// Get the requested event ID from the URL
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// 1. Fetch Event Details from Database (Title, Location, Description, etc.)
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND is_active = 1");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    header("Location: /claim/student/index.php");
    exit;
}

// 2. Fetch Inventory Details from Database
$invStmt = $pdo->prepare("SELECT SUM(total_quantity) as total_qty, SUM(remaining_quantity) as remaining_qty FROM inventory WHERE event_id = ?");
$invStmt->execute([$event_id]);
$inventory = $invStmt->fetch();

$total_qty = (int)($inventory['total_qty'] ?? 0);
$remain_qty = (int)($inventory['remaining_qty'] ?? 0);
$capacity_percent = $total_qty > 0 ? round(($remain_qty / $total_qty) * 100) : 0;

// 3. Fetch Time Slots from Database
$slotStmt = $pdo->prepare("SELECT * FROM event_time_slots WHERE event_id = ? ORDER BY start_time ASC");
$slotStmt->execute([$event_id]);
$time_slots = $slotStmt->fetchAll();

// 4. Check if the user already has a reservation for THIS event
$resStmt = $pdo->prepare("
    SELECT t.id 
    FROM reservations r
    JOIN event_time_slots t ON r.time_slot_id = t.id
    WHERE r.user_id = ? AND t.event_id = ? AND r.status = 'reserved'
");
$resStmt->execute([$_SESSION['user_id'], $event_id]);
$userReservedSlots = $resStmt->fetchAll(PDO::FETCH_COLUMN);
$hasReservedEvent = count($userReservedSlots) > 0;

// 5. Check Strike Suspension
$strikeStmt = $pdo->prepare("SELECT COUNT(*) FROM strike_logs WHERE user_id = ?");
$strikeStmt->execute([$_SESSION['user_id']]);
$strikes = $strikeStmt->fetchColumn();
$isSuspended = $strikes >= 3;
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
    </style>
</head>
<body class="min-h-screen pb-24 overflow-x-hidden relative">

    <header class="fixed top-0 left-0 right-0 h-16 bg-[#1A2419] flex items-center px-4 z-50">
        <a href="/claim/student/index.php" class="text-white hover:text-[#c6f135] transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined">arrow_back</span>
            <span class="text-sm font-bold tracking-wider uppercase">Back</span>
        </a>
    </header>

    <main class="max-w-md mx-auto px-4 pt-24 space-y-6">
        <!-- Hero Section -->
        <section class="relative rounded-xl overflow-hidden border border-[#e5e7eb]">
            <div class="h-64 w-full bg-cover bg-center" style="background-image: url('/claim/assets/pictures/Event_Poster.png')"></div>
            
            <div class="absolute top-4 left-4 bg-[#c6f135] text-[#1c261b] px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-lg">
                Active
            </div>
            <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/90 via-black/50 to-transparent">
                <!-- Title is still fetched from database! -->
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
                    $display_time = date('h:i A', $first_slot) . " onwards";
                }
            ?>
            <div class="bg-white p-4 rounded-xl border border-[#e5e7eb]">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-[#7ba82a] text-sm">calendar_month</span>
                    <span class="text-[10px] uppercase text-[#1c261b]/60 tracking-wider">Date</span>
                </div>
                <!-- Time is fetched from DB -->
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
                        <!-- Location is fetched from DB -->
                        <p class="font-bold text-[#1c261b]"><?php echo htmlspecialchars($event['location']); ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Description -->
        <section class="space-y-3">
            <h3 class="text-[10px] uppercase text-[#7ba82a] tracking-[0.2em] font-bold">Mission Intel</h3>
            <div class="bg-white p-5 rounded-xl border border-[#e5e7eb] relative overflow-hidden">
                <!-- Description is fetched from DB -->
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
                    <!-- Math is calculated from DB -->
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
                    <?php foreach ($time_slots as $slot): 
                        $time_str = date('h:i A', strtotime($slot['start_time']));
                        
                        $is_full = $slot['current_reservations'] >= $slot['max_capacity'];
                        $is_already_reserved = in_array($slot['id'], $userReservedSlots);
                        $spots_left = $slot['max_capacity'] - $slot['current_reservations'];

                        $button_class = "bg-white border border-[#e5e7eb] text-[#1c261b]/70 hover:border-[#c6f135] transition-colors time-slot-btn";
                        $status_text = $spots_left . " spots";

                        if ($is_already_reserved) {
                            $button_class = "bg-green-500/10 border-green-500/30 text-green-700 cursor-not-allowed opacity-80 ring-2 ring-green-500/20";
                            $status_text = "Locked In";
                        } elseif ($is_full) {
                            $button_class = "opacity-40 cursor-not-allowed bg-gray-100 border border-[#e5e7eb] text-[#1c261b]/60";
                            $status_text = "Sold Out";
                        } elseif ($hasReservedEvent) {
                            $button_class = "opacity-50 cursor-not-allowed bg-gray-50 border border-[#e5e7eb] text-[#1c261b]/60";
                        }
                    ?>
                        <button class="p-3 text-xs text-center rounded-lg flex flex-col items-center gap-1 <?php echo $button_class; ?>" 
                                data-slot-id="<?php echo $slot['id']; ?>"
                                <?php echo ($is_full || $hasReservedEvent) ? 'disabled' : ''; ?>>
                            <span class="font-bold"><?php echo $time_str; ?></span>
                            <span class="text-[9px] opacity-70"><?php echo $status_text; ?></span>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <input type="hidden" id="selected_slot_id" name="slot_id" value="">
        </section>

        <!-- CTA Action -->
        <section class="pt-4 pb-8">
            <button id="claim-btn" class="w-full bg-[#c6f135] text-[#1c261b] h-16 rounded-full font-black text-lg uppercase tracking-wider flex items-center justify-center gap-3 shadow-lg active:scale-[0.98] transition-all duration-75 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">confirmation_number</span>
                <span id="claim-text">Claim Now</span>
            </button>
            <p class="text-center text-[10px] text-[#1c261b]/60 mt-4 uppercase tracking-widest opacity-60">
                Single Use Token • Non-Transferable
            </p>
        </section>
    </main>

    <script>
        const csrfToken = "<?php echo $_SESSION['csrf_token'] ?? ''; ?>";
        const isSuspended = <?php echo $isSuspended ? 'true' : 'false'; ?>;

        document.addEventListener('DOMContentLoaded', () => {
            const timeSlots = document.querySelectorAll('.time-slot-btn');
            const hiddenInput = document.getElementById('selected_slot_id');
            const claimBtn = document.getElementById('claim-btn');
            const claimText = document.getElementById('claim-text');

            claimBtn.disabled = true;

            // Handle Slot Selection
            timeSlots.forEach(slot => {
                slot.addEventListener('click', () => {
                    // Reset all available slots
                    timeSlots.forEach(s => {
                        if (!s.disabled) {
                            s.classList.remove('bg-[#c6f135]/20', 'border-2', 'border-[#c6f135]', 'text-[#7ba82a]', 'font-bold');
                            s.classList.add('bg-white', 'border', 'border-[#e5e7eb]', 'text-[#1c261b]/70');
                        }
                    });
                    
                    // Highlight selected slot
                    slot.classList.remove('bg-white', 'border', 'border-[#e5e7eb]', 'text-[#1c261b]/70');
                    slot.classList.add('bg-[#c6f135]/20', 'border-2', 'border-[#c6f135]', 'text-[#7ba82a]', 'font-bold');
                    
                    hiddenInput.value = slot.getAttribute('data-slot-id');
                    claimBtn.disabled = false;
                });
            });

            // Handle Booking Submission
            claimBtn.addEventListener('click', async () => {
                const slotId = hiddenInput.value;
                if(!slotId) return;

                if (isSuspended) {
                    alert("Reservation Blocked: You have 3 or more unexcused no-show strikes.");
                    return;
                }

                claimBtn.disabled = true;
                claimText.innerText = "Processing...";

                try {
                    const res = await fetch('/claim/api/book_slot.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ time_slot_id: slotId, csrf_token: csrfToken })
                    });
                    
                    const data = await res.json();
                    
                    if(data.success) {
                        claimBtn.classList.replace('bg-[#c6f135]', 'bg-green-500');
                        claimBtn.classList.replace('text-[#1c261b]', 'text-white');
                        claimText.innerText = "Success! Redirecting...";
                        
                        setTimeout(() => {
                            window.location.href = '/claim/student/tickets.php';
                        }, 1000);
                    } else {
                        alert("Failed to book slot: " + data.message);
                        claimBtn.disabled = false;
                        claimText.innerText = "Claim Now";
                    }
                } catch (err) {
                    alert("Network error occurred.");
                    claimBtn.disabled = false;
                    claimText.innerText = "Claim Now";
                }
            });
        });
    </script>
</body>
</html>