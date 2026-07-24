<?php 
require_once '../config/database.php';
requireLogin('student');
require_once '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Get User Profile & Strike Count
$stmt = $pdo->prepare("
    SELECT u.*, (SELECT COUNT(*) FROM strike_logs WHERE user_id = u.id) as total_strikes 
    FROM users u WHERE u.id = ?
");
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch();

// Get ALL reservations
$resStmt = $pdo->prepare("
    SELECT r.*, e.title, e.location, t.start_time 
    FROM reservations r 
    JOIN event_time_slots t ON r.time_slot_id = t.id 
    JOIN events e ON t.event_id = e.id 
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$resStmt->execute([$user_id]);
$allReservations = $resStmt->fetchAll();

$activeReservations = array_filter($allReservations, function($r) { return $r['status'] === 'reserved'; });
$pastReservations = array_filter($allReservations, function($r) { return $r['status'] !== 'reserved'; });
$strikesCount = (int)$currentUser['total_strikes'];
?>

<div class="space-y-6">
    <!-- Student Badge Card -->
    <div class="flex flex-col md:flex-row gap-6">
        <div class="flex-1 bg-[#1A2419] p-6 rounded-3xl flex items-center gap-5 text-white border border-white/5">
            <div class="w-16 h-16 rounded-full bg-[#c6f135]/15 flex items-center justify-center border-2 border-[#c6f135] flex-shrink-0">
                <i data-lucide="user" class="w-8 h-8 text-[#c6f135]"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-xl font-black truncate"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h1>
                <p class="text-[#c6f135] font-bold text-xs tracking-widest uppercase mt-0.5">DLSU STUDENT</p>
                <p class="text-white/60 text-[10px] font-bold mt-1 font-mono uppercase tracking-wide">
                    <?php echo htmlspecialchars($currentUser['program'] ?? 'BS IT'); ?>
                </p>
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="flex gap-4 flex-shrink-0 md:w-80">
            <div class="bg-[#1A2419] p-5 rounded-3xl flex-1 text-center flex flex-col justify-center border border-white/5 text-white">
                <div class="text-3xl font-black text-white font-mono"><?php echo count($allReservations); ?></div>
                <div class="text-[9px] font-bold text-[#c6f135] uppercase tracking-widest mt-1">Total Booked</div>
            </div>
            <div class="bg-[#1A2419] p-5 rounded-3xl flex-1 text-center flex flex-col justify-center border border-white/5 text-white">
                <div class="text-3xl font-black text-[#FFB300] font-mono"><?php echo $strikesCount; ?></div>
                <div class="text-[9px] font-bold text-red-400 uppercase tracking-widest mt-1">Active Strikes</div>
            </div>
        </div>
    </div>

    <!-- Active Strikes Alert -->
    <?php if ($strikesCount > 0): ?>
        <div class="flex gap-3 bg-amber-50 border border-amber-200 p-4 rounded-2xl items-start">
            <i data-lucide="shield-alert" class="text-amber-500 w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="text-xs font-bold text-amber-950">Active Strike Warning: <?php echo $strikesCount; ?> / 3 Strikes</p>
                <p class="text-[11px] text-amber-800 leading-normal mt-0.5">Under DLSU AnimoClaim strike policy, students logging 3 unexcused no-shows will experience temporary booking restrictions.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Active Reservations summary -->
    <div class="space-y-3">
        <h2 class="text-lg font-black text-[#1A2419] tracking-tight uppercase flex items-center gap-2">
            <i data-lucide="ticket" class="w-5 h-5 text-[#7ba82a]"></i> Pending Claims
        </h2>
        <div class="space-y-3">
            <?php if (count($activeReservations) === 0): ?>
                <p class="text-gray-500 font-medium text-xs bg-white/50 p-4 rounded-2xl border border-dashed border-gray-300">You don't have any pending claims at the moment.</p>
            <?php else: ?>
                <?php foreach($activeReservations as $claim): ?>
                    <a href="/claim/student/tickets.php" class="flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-100 hover:border-[#c6f135] transition-colors cursor-pointer shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#c6f135]/15 flex items-center justify-center">
                                <i data-lucide="ticket" class="w-5 h-5 text-[#7ba82a]"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xs text-gray-900"><?php echo htmlspecialchars($claim['title']); ?></h3>
                                <p class="text-[10px] text-gray-500 mt-0.5"><?php echo htmlspecialchars($claim['location']); ?> • <?php echo date('M d, h:i A', strtotime($claim['start_time'])); ?></p>
                            </div>
                        </div>
                        <span class="bg-[#c6f135] text-[#1c261b] font-mono text-[9px] px-2.5 py-1 rounded-full uppercase tracking-wider font-bold">QR Code</span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Booking history on profile page -->
    <div class="space-y-3">
        <h2 class="text-lg font-black text-[#1A2419] tracking-tight uppercase">Reservation Logs</h2>
        <div class="bg-[#1A2419] rounded-3xl p-5 text-white border border-white/5">
            <?php if (count($pastReservations) === 0): ?>
                <p class="text-white/40 text-center text-xs py-6">Your past claimed or expired transactions log is empty.</p>
            <?php else: ?>
                <div class="divide-y divide-white/5">
                    <?php foreach($pastReservations as $item): ?>
                        <div class="flex justify-between items-center py-3 first:pt-0 last:pb-0">
                            <div>
                                <p class="font-bold text-xs text-white truncate max-w-xs md:max-w-md"><?php echo htmlspecialchars($item['title']); ?></p>
                                <p class="text-[10px] text-white/50 mt-0.5 font-mono"><?php echo date('M d, Y h:i A', strtotime($item['start_time'])); ?></p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-mono font-bold uppercase tracking-widest <?php echo $item['status'] === 'claimed' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400'; ?>">
                                <?php echo $item['status']; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sign Out CTA -->
    <a href="/claim/config/logout.php" class="w-full py-4 flex items-center justify-center gap-2 bg-red-900/10 border border-red-500/10 hover:bg-red-900/20 text-red-500 rounded-2xl transition-all font-black uppercase tracking-widest text-xs cursor-pointer mt-6">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        Sign Out Student Session
    </a>
</div>

<?php require_once '../includes/footer.php'; ?>