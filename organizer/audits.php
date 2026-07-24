<?php 
require_once '../config/database.php';
requireLogin('organizer');
require_once '../includes/header.php';

// 1. Fetch Audit Logs (Reservations joined with user and event data)
$logStmt = $pdo->query("
    SELECT r.*, u.first_name, u.last_name, u.dlsu_id, e.title, t.start_time
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN event_time_slots t ON r.time_slot_id = t.id
    JOIN events e ON t.event_id = e.id
    ORDER BY r.created_at DESC
");
$reservations = $logStmt->fetchAll();

// 2. Fetch Students and Dynamically Count their Strikes from strike logs table
$studentStmt = $pdo->query("
    SELECT u.*, (SELECT COUNT(*) FROM strike_logs s WHERE s.user_id = u.id) as strikes 
    FROM users u 
    WHERE role = 'student' 
    ORDER BY u.first_name ASC
");
$students = $studentStmt->fetchAll();
?>

<div class="space-y-6">
    <!-- Reports top row with export button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm">
        <div>
            <h3 class="font-black text-sm uppercase tracking-wider text-gray-900">Distribution Audit Logs & Strike Policy Enforcement</h3>
            <p class="text-[11px] text-gray-500">Monitor attendee compliance logs and manage student no show penalties.</p>
        </div>
        <button onclick="alert('Simulating Export: CSV generated with transaction logs and attendance metrics.')" class="flex items-center gap-1.5 px-4 h-11 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all cursor-pointer shadow-md w-full sm:w-auto justify-center">
            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
            Export Audit Data
        </button>
    </div>

    <!-- Audit logs table and Strikes policy layout split -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Transaction Log list -->
        <div class="lg:col-span-7">
            <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h4 class="font-black text-xs uppercase tracking-wider text-gray-900">Recent Transaction Audit Logs</h4>
                    <span class="text-[10px] text-gray-400 font-bold"><?php echo count($reservations); ?> records</span>
                </div>
                <div class="divide-y divide-gray-100 max-h-[450px] overflow-y-auto pr-1 hide-scrollbar">
                    <?php foreach($reservations as $item): ?>
                        <div class="py-3 flex justify-between items-center first:pt-0 last:pb-0">
                            <div>
                                <p class="font-bold text-xs text-gray-900"><?php echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']); ?></p>
                                <p class="text-[10px] text-gray-500 mt-0.5 font-mono">
                                    <?php echo htmlspecialchars($item['title']); ?> • <?php echo date('M d, h:i A', strtotime($item['start_time'])); ?>
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php 
                                    $badgeClass = 'bg-gray-100 text-gray-600';
                                    if ($item['status'] === 'claimed') $badgeClass = 'bg-green-100 text-green-700';
                                    if ($item['status'] === 'reserved') $badgeClass = 'bg-blue-100 text-blue-700 animate-pulse';
                                    if ($item['status'] === 'expired') $badgeClass = 'bg-red-100 text-red-600';
                                ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-mono font-black uppercase tracking-widest <?php echo $badgeClass; ?>">
                                    <?php echo $item['status']; ?>
                                </span>
                                
                                <?php if($item['status'] === 'reserved'): ?>
                                    <button onclick="triggerNoShow(<?php echo $item['id']; ?>, <?php echo $item['user_id']; ?>)" title="Mark No Show (Strike)" class="p-1 rounded bg-amber-100 text-amber-700 hover:bg-amber-200 transition-colors cursor-pointer">
                                        <i data-lucide="flame" class="w-3.5 h-3.5"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Right Strikes Enforcement management panels -->
        <div class="lg:col-span-5">
            <div class="bg-[#1c261b] p-5 rounded-3xl text-white border border-white/5 space-y-4">
                <div>
                    <h4 class="font-black text-xs uppercase tracking-wider text-[#c6f135] flex items-center gap-1.5">
                        <i data-lucide="flame" class="w-4 h-4 text-amber-500"></i>
                        Student Strikes & Penalty Status
                    </h4>
                    <p class="text-[10px] text-white/50 mt-1 leading-normal">
                        Manage strikes manually or inspect student accounts suspended due to exceeding the 3 strike threshold.
                    </p>
                </div>
                <div class="divide-y divide-white/5 space-y-3 pt-2 max-h-[350px] overflow-y-auto pr-2 hide-scrollbar">
                    <?php foreach($students as $student): 
                        $strikes = (int)$student['strikes'];
                        $isSuspended = $strikes >= 3;
                    ?>
                        <div class="pt-3 flex justify-between items-center first:pt-0">
                            <div>
                                <p class="font-bold text-xs text-white leading-tight"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
                                <p class="text-[9px] text-[#c6f135]/60 font-mono mt-0.5"><?php echo htmlspecialchars($student['dlsu_id']); ?> • <?php echo htmlspecialchars($student['program']); ?></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1">
                                    <button onclick="adjustStrike(<?php echo $student['id']; ?>, 'remove')" class="w-5 h-5 bg-white/5 hover:bg-white/10 flex items-center justify-center rounded text-xs cursor-pointer">
                                        -
                                    </button>
                                    <span class="font-mono text-xs font-black w-4 text-center" id="strike-count-<?php echo $student['id']; ?>"><?php echo $strikes; ?></span>
                                    <button onclick="adjustStrike(<?php echo $student['id']; ?>, 'add')" class="w-5 h-5 bg-white/5 hover:bg-white/10 flex items-center justify-center rounded text-xs cursor-pointer">
                                        +
                                    </button>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest <?php echo $isSuspended ? 'bg-red-500 text-white' : 'bg-[#c6f135] text-[#1c261b]'; ?>">
                                    <?php echo $isSuspended ? 'Suspended' : 'Clear'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    async function triggerNoShow(reservationId, userId) {
        if(!confirm("Mark this reservation as expired and issue a strike?")) return;
        
        try {
            const res = await fetch('/claim/api/manage_strikes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'no-show', 
                    reservation_id: reservationId, 
                    user_id: userId,
                    csrf_token: csrfToken 
                })
            });
            const data = await res.json();
            if(data.success) {
                window.location.reload();
            } else {
                alert("Error: " + data.message);
            }
        } catch(err) {
            alert("Network error.");
        }
    }

    async function adjustStrike(userId, action) {
        try {
            const res = await fetch('/claim/api/manage_strikes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: action, 
                    user_id: userId,
                    csrf_token: csrfToken 
                })
            });
            const data = await res.json();
            if(data.success) {
                window.location.reload();
            } else {
                alert("Error: " + data.message);
            }
        } catch(err) {
            alert("Network error.");
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>