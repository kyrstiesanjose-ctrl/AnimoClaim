<!DOCTYPE html>
<html class="light dark" lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    <title>AnimoClaim - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Jetbrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            background-color: #011809;
            min-height: 100vh;
            width: 100%;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="font-body text-[#cdead1] selection:bg-[#3f4a3d] selection:text-[#adb8a8] p-4 md:p-8">
    
    <div class="w-full max-w-md bg-[#1c261b] rounded-[32px] shadow-2xl border border-white/5 flex flex-col px-8 py-12">
        <div class="flex-1 flex flex-col justify-center items-center">
            
            <div class="w-32 h-32 mb-6 flex items-center justify-center">
        <img src="assets/pictures/AnimoClaim_Logo.png" alt="AnimoClaim Logo" class="w-full h-full object-contain">
</div>
            </div>
            <h1 class="text-4xl font-extrabold text-[#c6f135] tracking-tighter mb-2 font-['Montserrat']">AnimoClaim</h1>
            <p class="text-white/60 text-center text-sm mb-10">Secure your spot. Claim your gear. Live the Animo.</p>

            <form action="api/process_login.php" method="POST" class="w-full flex flex-col gap-5">
                <div>
                    <label class="block text-[11px] font-bold text-[#c6f135] uppercase tracking-widest mb-1.5 ml-1">ID Number</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-white/40">badge</span>
                        <input type="text" name="dlsu_id" class="w-full pl-12 pr-4 h-14 rounded-2xl bg-[#0f2419] border border-white/10 focus:border-[#c6f135] focus:ring-1 focus:ring-[#c6f135] transition-all outline-none text-white placeholder:text-white/30" placeholder="120*****" required>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#c6f135] uppercase tracking-widest mb-1.5 ml-1">Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-white/40">lock</span>
                        <input type="password" name="password" class="w-full pl-12 pr-4 h-14 rounded-2xl bg-[#0f2419] border border-white/10 focus:border-[#c6f135] focus:ring-1 focus:ring-[#c6f135] transition-all outline-none text-white placeholder:text-white/30" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="flex justify-end mt-1">
                    <a href="#" class="text-[12px] font-bold text-white/60 hover:text-[#c6f135] transition-colors">Forgot Password?</a>
                </div>

                <button type="submit" class="w-full h-14 mt-6 bg-[#c6f135] text-[#1c261b] rounded-2xl font-bold text-[16px] uppercase tracking-widest hover:opacity-90 active:scale-95 transition-all shadow-lg shadow-[#c6f135]/20">
                    Login
                </button>
            </form>
        </div>
    </div>

</body>
</html>