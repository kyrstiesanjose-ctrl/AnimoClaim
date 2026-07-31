<?php require_once '../includes/header.php'; ?>

<div class="space-y-6">
    <a href="/claim/student/index.php" class="flex items-center gap-2 text-xs font-bold text-gray-600">
        <i data-lucide="chevron-left" class="w-4 h-4"></i> Back to campaigns
    </a>
    
    <div class="relative rounded-3xl overflow-hidden shadow-lg border border-gray-200 h-64 md:h-80 flex items-end">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo htmlspecialchars($event['image_url']); ?>')"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-transparent z-10"></div>
        <div class="p-6 relative z-20 w-full text-white">
            <h2 class="text-xl md:text-3xl font-black leading-tight drop-shadow-md"><?php echo htmlspecialchars($event['title']); ?></h2>
        </div>
    </div>

    <div class="space-y-3">
        <h3 class="text-[10px] uppercase text-[#7ba82a] tracking-widest font-black">Select Your Claiming Slot</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <?php foreach($slots as $slot): 
                $isFull = $slot['current_reservations'] >= $slot['max_capacity'];
            ?>
                <button onclick="bookSlot(<?php echo $slot['id']; ?>)" <?php echo $isFull ? 'disabled' : ''; ?> class="p-3 text-xs text-center font-bold rounded-xl border transition-all flex flex-col items-center justify-center gap-1 cursor-pointer <?php echo $isFull ? 'bg-gray-100 text-gray-300' : 'bg-white text-gray-600 hover:border-[#c6f135]'; ?>">
                    <span class="font-mono"><?php echo date('h:i A', strtotime($slot['start_time'])); ?></span>
                    <span class="text-[9px] font-normal"><?php echo $isFull ? 'Sold Out' : ($slot['max_capacity'] - $slot['current_reservations']) . ' spots left'; ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<script>
async function bookSlot(id) {
    if(!confirm("Lock in this time slot?")) return;
    const res = await fetch('/claim/api/book_slot.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ time_slot_id: id, csrf_token: csrfToken })
    });
    const data = await res.json();
    if(data.success) { 
        alert('Successfully Reserved!'); 
        window.location = '/claim/student/tickets.php'; 
    } else { 
        alert(data.message); 
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>