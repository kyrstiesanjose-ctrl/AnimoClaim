<?php 
require_once '../config/database.php';
requireLogin('student');
require_once '../includes/header.php';

// FIXED: Changed 'time_slots' to 'event_time_slots'
$stmt = $pdo->prepare("
    SELECT r.*, e.title, e.location, t.start_time 
    FROM reservations r 
    JOIN event_time_slots t ON r.time_slot_id = t.id 
    JOIN events e ON t.event_id = e.id 
    WHERE r.user_id = ? AND r.status = 'reserved'
");
$stmt->execute([$_SESSION['user_id']]);
$claims = $stmt->fetchAll();
?>
<div class="space-y-6">
    <h2 class="text-xl font-black text-[#1c261b] tracking-tight">Active Reservations</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach($claims as $claim): ?>
            <div class="bg-[#1c261b] border border-white/10 rounded-3xl overflow-hidden shadow-lg flex flex-col relative">
                <div class="p-5 flex-1">
                    <h3 class="text-md font-bold text-white mb-3"><?php echo htmlspecialchars($claim['title']); ?></h3>
                    <p class="text-white/70 text-xs"><i data-lucide="map-pin" class="w-3 h-3 inline"></i> <?php echo htmlspecialchars($claim['location']); ?></p>
                </div>
                <div class="ticket-dashed p-6 bg-[#0f2419] flex flex-col items-center justify-center relative">
                    <div class="bg-[#c6f135] p-2 rounded-2xl mb-2.5">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($claim['qr_code_hash']); ?>&color=1c261b&bgcolor=c6f135" class="w-32 h-32 rounded-xl" />
                    </div>
                    <p class="text-[10px] text-[#c6f135]/70 font-mono font-bold"><?php echo htmlspecialchars($claim['qr_code_hash']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>