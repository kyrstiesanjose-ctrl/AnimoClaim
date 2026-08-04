<?php require_once '../includes/header.php'; ?>

<div class="space-y-6 relative">
    
    <?php if (isset($success_msg)): ?>
        <div class="bg-[#e2f6d5] border border-[#9fe870] text-[#163300] px-5 py-3.5 rounded-2xl flex items-center gap-2.5 font-bold text-xs shadow-sm">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-[#163300]"></i> <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_msg)): ?>
        <div class="bg-[#fde2e2] border border-[#f08a8a] text-[#5c0f0f] px-5 py-3.5 rounded-2xl flex items-center gap-2.5 font-bold text-xs shadow-sm">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-[#5c0f0f]"></i> <?php echo htmlspecialchars($error_msg); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Active Campaigns</span>
            <span class="text-3xl font-black text-[#0e0f0c] font-mono mt-2"><?php echo $activeCampaigns; ?></span>
            <span class="text-[10px] text-emerald-700 font-bold mt-1">Live distribution</span>
        </div>
        <div class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Total Reserved</span>
            <span class="text-3xl font-black text-blue-600 font-mono mt-2"><?php echo $totalReserved; ?></span>
            <span class="text-[10px] text-gray-500 font-medium mt-1">Awaiting onsite claim</span>
        </div>
        <div class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider">Items Claimed</span>
            <span class="text-3xl font-black text-[#163300] font-mono mt-2"><?php echo $itemsClaimed; ?></span>
            <span class="text-[10px] text-emerald-700 font-bold mt-1">100% Verified onsite</span>
        </div>
        <div class="bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider">No Show Rate</span>
            <span class="text-3xl font-black text-red-500 font-mono mt-2"><?php echo $noShowRate; ?>%</span>
            <span class="text-[10px] text-red-500 font-bold mt-1">Strike policy active</span>
        </div>
    </div>

    <div class="flex justify-between items-center bg-white p-5 rounded-[28px] border border-[#0e0f0c]/12 shadow-sm">
        <div>
            <h3 class="font-black text-sm text-[#0e0f0c] uppercase tracking-wider wise-heading">Active Distribution Campaigns</h3>
            <p class="text-[11px] text-gray-500 font-medium mt-0.5">Configure capacities and monitor participant metrics</p>
        </div>
        <button onclick="toggleModal(true)" class="flex items-center gap-1.5 px-5 h-12 bg-[#0e0f0c] text-[#9fe870] hover:bg-black text-xs font-black uppercase tracking-wider rounded-full transition-all cursor-pointer shadow-md wise-btn">
            <i data-lucide="plus" class="w-4 h-4"></i> New Campaign
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($campaigns as $event): 
            $percent = $event['total_capacity'] > 0 ? ($event['total_reservations'] / $event['total_capacity']) * 100 : 0;
        ?>
            <div class="bg-white rounded-[28px] border border-[#0e0f0c]/12 shadow-sm overflow-hidden flex flex-col justify-between">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <span class="bg-[#e2f6d5] text-[#163300] text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full border border-[#9fe870]/40 font-mono">
                            <?php echo htmlspecialchars($event['category'] ?? 'General'); ?>
                        </span>
                        <span class="text-[10px] font-mono text-gray-400 font-bold">Event ID: #<?php echo $event['id']; ?></span>
                    </div>
                    <h4 class="font-black text-base text-[#0e0f0c] leading-snug"><?php echo htmlspecialchars($event['title']); ?></h4>
                    <p class="text-xs text-gray-500 font-medium mt-1.5 flex items-center gap-1.5">
                        <i data-lucide="map-pin" class="w-4 h-4 text-[#163300]"></i> <?php echo htmlspecialchars($event['location']); ?>
                    </p>
                    <div class="mt-5 space-y-2">
                        <div class="flex justify-between text-[11px] font-bold">
                            <span class="text-gray-500">Booked Slots Headcount</span>
                            <span class="text-[#0e0f0c] font-mono"><?php echo $event['total_reservations']; ?> / <?php echo $event['total_capacity']; ?></span>
                        </div>
                        <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-[#9fe870] h-full transition-all duration-1000" style="width: <?php echo $percent; ?>%"></div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-[11px] text-gray-500 font-mono font-bold"><?php echo $event['slot_count']; ?> claiming windows</span>
                    
                    <div class="flex items-center gap-2">
                        <form action="delete_event.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel/delete this event?');">
                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                            <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 hover:bg-red-100 text-[10px] font-black uppercase tracking-wider rounded-full transition-all cursor-pointer border border-red-200">
                                Delete
                            </button>
                        </form>

                        <a href="/claim/organizer/terminal.php?event_id=<?php echo $event['id']; ?>" class="px-4 py-2 bg-[#0e0f0c] text-[#9fe870] hover:bg-black text-[10px] font-black uppercase tracking-wider rounded-full transition-all cursor-pointer shadow-sm wise-btn">
                            Scan Claims
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="createModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[32px] w-full max-w-lg shadow-2xl border border-[#0e0f0c]/12 p-7 space-y-4 text-[#0e0f0c]">
        <div class="flex justify-between items-center">

        <h3 class="text-base font-black uppercase tracking-wider wise-heading">
            Launch New Campaign
        </h3>
            <button onclick="toggleModal(false)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors cursor-pointer">
                <i data-lucide="x" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>
        
        <!-- Updated action to point to create_event.php -->
        <form action="create_event.php" method="POST" enctype="multipart/form-data" class="space-y-3.5">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="create_campaign" value="1">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Campaign Title</label>
                    <input type="text" name="title" required placeholder="e.g., Blood Drive Snack Package" class="w-full px-4 h-12 rounded-2xl bg-gray-50 border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-bold" />
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Event Poster (Required)</label>
                    <input type="file" name="event_image" required accept="image/png, image/jpeg, image/webp" class="w-full px-4 py-2.5 h-12 rounded-2xl bg-gray-50 border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-bold file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:bg-[#0e0f0c] file:text-[#9fe870] hover:file:bg-black cursor-pointer" />
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Category</label>
                    <select name="category" id="category-select" onchange="toggleTicketFields()" class="w-full px-4 h-12 rounded-2xl bg-gray-50 border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-bold">
                        <option value="Food">Food</option>
                        <option value="Beverage">Beverage</option>
                        <option value="Merchandise">Merchandise</option>
                        <option value="Academic Kit">Academic Kit</option>
                        <option value="Ticket">Ticket</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Fulfillment Spot</label>
                    <select name="location" required class="w-full px-4 h-12 rounded-2xl bg-gray-50 border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-bold">
                        <option value="" disabled selected>Select DLSU Campus & Venue</option>
                        <optgroup label="Laguna Campus">
                            <option value="Richard L. Lee Engineering Block">Richard L. Lee Engineering Block</option>
                            <option value="Dr. George S.K. Ty Bldg">Dr. George S.K. Ty Bldg</option>
                            <option value="Enrique K. Razon Jr. Hall">Enrique K. Razon Jr. Hall</option>
                            <option value="John L. Gokongwei Jr. Innovation Center">John L. Gokongwei Jr. Innovation Center</option>
                            <option value="Milagros R. Del Rosario Bldg">Milagros R. Del Rosario Bldg</option>
                        </optgroup>
                        <optgroup label="Manila Campus">
                            <option value="St. La Salle Hall">St. La Salle Hall</option>
                            <option value="Henry Sy Sr. Hall">Henry Sy Sr. Hall</option>
                            <option value="Enrique Razon Sports Center">Enrique Razon Sports Center</option>
                            <option value="Velasco Hall">Velasco Hall</option>
                            <option value="Gokongwei Hall">Gokongwei Hall</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <!-- Ticket-only fields: hidden unless Category = Ticket -->
            <div id="ticket-fields" class="hidden space-y-2.5 bg-[#e2f6d5]/50 border border-[#9fe870]/50 rounded-2xl p-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 ml-1">Ticket Tiers &amp; Ticket Slots</p>

                <div class="grid grid-cols-[1fr_auto_100px] items-center gap-2">
                    <span class="text-xs font-bold text-[#0e0f0c]">General Admission</span>
                    <span class="text-xs font-mono font-bold text-gray-500 justify-self-end">₱100.00</span>
                    <input type="number" name="qty_general" id="qty-general" min="0" placeholder="Slots" class="w-full px-3 h-10 rounded-xl bg-white border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-bold font-mono" />
                </div>
                <div class="grid grid-cols-[1fr_auto_100px] items-center gap-2">
                    <span class="text-xs font-bold text-[#0e0f0c]">Upper Box</span>
                    <span class="text-xs font-mono font-bold text-gray-500 justify-self-end">₱200.00</span>
                    <input type="number" name="qty_upper" id="qty-upper" min="0" placeholder="Slots" class="w-full px-3 h-10 rounded-xl bg-white border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-bold font-mono" />
                </div>
                <div class="grid grid-cols-[1fr_auto_100px] items-center gap-2">
                    <span class="text-xs font-bold text-[#0e0f0c]">Lower Box</span>
                    <span class="text-xs font-mono font-bold text-gray-500 justify-self-end">₱300.00</span>
                    <input type="number" name="qty_lower" id="qty-lower" min="0" placeholder="Slots" class="w-full px-3 h-10 rounded-xl bg-white border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-bold font-mono" />
                </div>
            </div>

            <div id="capacity-ticket-note" class="hidden px-4 py-3 rounded-2xl bg-gray-50 border border-gray-200">
                <p class="text-[10px] text-gray-500 font-semibold leading-relaxed">Pickup capacity is set automatically from your ticket counts above — any ticket holder can claim at any of the 3 time windows.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div id="target-audience-field">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Target Audience / Beneficiaries</label>
                    <select name="target_audience" required class="w-full px-4 h-12 rounded-2xl bg-gray-50 border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-bold">
                        <option value="University wide">University Wide</option>
                        <option value="College Specific">College Specific</option>
                        <option value="Faculty Specific">Faculty Specific</option>
                        <option value="Organization Members Only">Organization Members Only</option>
                    </select>
                </div>
                <div id="capacity-field">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Max Capacity Per Slot</label>
                    <input type="number" name="capacity" id="capacity-input" required min="5" max="500" value="50" class="w-full px-4 h-12 rounded-2xl bg-gray-50 border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-bold font-mono" />
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Event Date & Time</label>
                <input type="datetime-local" name="event_date" required class="w-full px-4 h-12 rounded-2xl bg-gray-50 border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-bold font-mono" />
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Campaign Description</label>
                <textarea name="description" rows="3" placeholder="Specify distribution mechanics and items included..." class="w-full p-4 rounded-2xl bg-gray-50 border border-gray-200 focus:border-[#0e0f0c] focus:outline-none text-xs font-medium"></textarea>
            </div>
            
            <div class="pt-3 flex gap-3">
                <button type="button" onclick="toggleModal(false)" class="flex-1 h-12 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-black uppercase tracking-wider rounded-full transition-all cursor-pointer">Cancel</button>
                <button type="submit" class="flex-1 h-12 bg-[#0e0f0c] text-[#9fe870] hover:bg-black text-xs font-black uppercase tracking-wider rounded-full transition-all shadow-md cursor-pointer wise-btn">Launch Event</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(show) {
        const modal = document.getElementById('createModal');
        if (show) { modal.classList.remove('hidden'); } 
        else { modal.classList.add('hidden'); }
    }

    function toggleTicketFields() {
        const isTicket = document.getElementById('category-select').value === 'Ticket';
        const wrap = document.getElementById('ticket-fields');
        wrap.classList.toggle('hidden', !isTicket);
        if (!isTicket) {
            document.getElementById('qty-general').value = '';
            document.getElementById('qty-upper').value = '';
            document.getElementById('qty-lower').value = '';
        }

        document.getElementById('capacity-field').classList.toggle('hidden', isTicket);
        document.getElementById('capacity-ticket-note').classList.toggle('hidden', !isTicket);
        document.getElementById('capacity-input').required = !isTicket;
        document.getElementById('target-audience-field').classList.toggle('md:col-span-2', isTicket);
    }

    document.addEventListener('DOMContentLoaded', toggleTicketFields);
</script>

<?php require_once '../includes/footer.php'; ?>