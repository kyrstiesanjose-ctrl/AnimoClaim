<?php require_once '../includes/header.php'; ?>

<div class="space-y-6 relative">
    
    <?php if (isset($success_msg)): ?>
        <div class="bg-[#e2f6d5] border border-[#9fe870] text-[#163300] px-5 py-3.5 rounded-2xl flex items-center gap-2.5 font-bold text-xs shadow-sm">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-[#163300]"></i> <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_msg)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl flex items-center gap-2.5 font-bold text-xs shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i> <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <!-- Analytics Top Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Tracked Campaigns</span>
            <span class="text-3xl font-black text-[#0e0f0c] font-mono mt-2"><?php echo $totalItems; ?></span>
            <span class="text-[10px] text-emerald-700 font-bold mt-1">Active items logged</span>
        </div>
        <div class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Total Remaining Stock</span>
            <span class="text-3xl font-black text-blue-600 font-mono mt-2"><?php echo $totalStock; ?></span>
            <span class="text-[10px] text-gray-500 font-medium mt-1">Physical items available</span>
        </div>
        <div class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Low Stock Alerts</span>
            <span class="text-3xl font-black <?php echo $lowStockCount > 0 ? 'text-red-500' : 'text-[#163300]'; ?> font-mono mt-2">
                <?php echo $lowStockCount; ?>
            </span>
            <span class="text-[10px] <?php echo $lowStockCount > 0 ? 'text-red-500' : 'text-emerald-700'; ?> font-bold mt-1">Items under 20% capacity</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Master Inventory Grid -->
        <div class="lg:col-span-7">
            <div class="bg-white p-6 rounded-[32px] border border-[#0e0f0c]/12 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h4 class="font-black text-xs uppercase tracking-wider text-[#0e0f0c]">Real-Time Stock Engine</h4>
                    <span class="text-[10px] text-gray-400 font-bold font-mono">Master View</span>
                </div>
                
                <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto pr-2 hide-scrollbar">
                    <?php foreach($inventory_items as $item): 
                        $percent = $item['total_inventory'] > 0 ? ($item['remaining_balance'] / $item['total_inventory']) * 100 : 0;
                        $isLow = $percent <= 20;
                    ?>
                        <div class="py-4 flex flex-col gap-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-xs text-[#0e0f0c] leading-tight"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <p class="text-[10px] text-gray-500 mt-0.5 font-mono font-medium truncate max-w-[200px]">
                                        <?php echo htmlspecialchars($item['event_title']); ?> • <?php echo htmlspecialchars($item['category']); ?>
                                    </p>
                                </div>
                                <button onclick="openAdjustModal(<?php echo $item['item_id']; ?>, '<?php echo addslashes(htmlspecialchars($item['description'])); ?>')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-[9px] font-black uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                                    Adjust
                                </button>
                            </div>
                            
                            <!-- Stock Progress Bar -->
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-[10px] font-mono font-bold">
                                    <span class="<?php echo $isLow ? 'text-red-500' : 'text-emerald-600'; ?>">
                                        <?php echo $item['remaining_balance']; ?> left
                                    </span>
                                    <span class="text-gray-400"><?php echo $item['total_inventory']; ?> total</span>
                                </div>
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full transition-all duration-1000 <?php echo $isLow ? 'bg-red-500' : 'bg-[#9fe870]'; ?>" style="width: <?php echo $percent; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Historical Transaction Logs -->
        <div class="lg:col-span-5">
            <div class="bg-[#0e0f0c] p-6 rounded-[32px] text-white border border-white/10 shadow-xl space-y-4">
                <div>
                    <h4 class="font-black text-xs uppercase tracking-wider text-[#9fe870] flex items-center gap-1.5 font-mono">
                        <i data-lucide="database" class="w-4 h-4 text-[#9fe870]"></i> Inventory Movement Log
                    </h4>
                    <p class="text-[10px] text-gray-400 font-medium mt-1">Immutable granular audit trail</p>
                </div>
                
                <div class="divide-y divide-white/10 space-y-3 pt-2 max-h-[420px] overflow-y-auto pr-2 hide-scrollbar">
                    <?php if (empty($inventory_logs)): ?>
                        <p class="text-white/40 text-[10px] text-center py-4 font-mono">No granular logs found.</p>
                    <?php else: ?>
                        <?php foreach($inventory_logs as $log): 
                            $isAddition = $log['quantity_adjusted'] > 0;
                        ?>
                            <div class="pt-3 flex justify-between items-center">
                                <div class="max-w-[150px]">
                                    <p class="font-bold text-[11px] text-white truncate" title="<?php echo htmlspecialchars($log['description']); ?>">
                                        <?php echo htmlspecialchars($log['description']); ?>
                                    </p>
                                    <p class="text-[9px] text-white/50 font-mono mt-0.5"><?php echo date('M d, H:i', strtotime($log['timestamp'])); ?></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-mono text-white/40"><?php echo $log['adjustment_type']; ?></span>
                                    <span class="px-2 py-1 rounded-md text-[10px] font-black font-mono <?php echo $isAddition ? 'bg-[#9fe870]/20 text-[#9fe870]' : 'bg-red-500/20 text-red-400'; ?>">
                                        <?php echo $isAddition ? '+' : ''; ?><?php echo $log['quantity_adjusted']; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manual Adjustment Modal -->
<div id="adjustModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[32px] w-full max-w-sm shadow-2xl border border-[#0e0f0c]/12 p-7 space-y-4 text-[#0e0f0c]">
        <div class="flex justify-between items-center">
            <h3 class="text-sm font-black uppercase tracking-wider wise-heading flex items-center gap-2">
                Manual Adjustment
            </h3>
            <button onclick="closeAdjustModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors cursor-pointer">
                <i data-lucide="x" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="adjust_inventory" value="1">
            <input type="hidden" name="item_id" id="modal_item_id" value="">
            
            <p id="modal_item_name" class="text-xs font-bold text-blue-600 bg-blue-50 p-3 rounded-2xl truncate"></p>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Quantity Change</label>
                <input type="number" name="adjustment_qty" required placeholder="e.g., 50 or -10" class="w-full px-4 h-12 rounded-2xl bg-gray-50 border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-bold font-mono" />
                <p class="text-[9px] text-gray-400 mt-1.5 ml-1 leading-snug">Use positive numbers for additions (e.g., restocks). Use negative numbers for subtractions (e.g., damages).</p>
            </div>
            
            <button type="submit" class="w-full h-12 mt-2 bg-[#0e0f0c] text-[#9fe870] hover:bg-black text-xs font-black uppercase tracking-wider rounded-full transition-all shadow-md cursor-pointer wise-btn">Log Stock Movement</button>
        </form>
    </div>
</div>

<script>
    function openAdjustModal(itemId, itemName) {
        document.getElementById('modal_item_id').value = itemId;
        document.getElementById('modal_item_name').textContent = itemName;
        document.getElementById('adjustModal').classList.remove('hidden');
    }

    function closeAdjustModal() {
        document.getElementById('adjustModal').classList.add('hidden');
        document.getElementById('modal_item_id').value = '';
    }
</script>

<?php require_once '../includes/footer.php'; ?>