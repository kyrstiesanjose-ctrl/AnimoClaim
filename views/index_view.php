<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnimoClaim — DLSU Event & Merchandise Distribution Portal</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .wise-heading { font-family: 'Montserrat', sans-serif; }

        @keyframes subtleFloat {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-8px) scale(1.01); }
        }
        @keyframes bgZoom {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }
        .animate-float { animation: subtleFloat 5s ease-in-out infinite; }
        .animate-bg-zoom { animation: bgZoom 22s ease-in-out infinite; }
        
        .wise-btn {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .wise-btn:active {
            transform: scale(0.97);
        }
    </style>
</head>
<body class="bg-[#e8f5e1] text-[#163300] min-h-screen relative overflow-x-hidden flex items-center justify-center p-3 sm:p-6 lg:p-12">

    <!-- Light Ambient Background Layer -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-cover bg-center opacity-10 animate-bg-zoom mix-blend-multiply" style="background-image: url('/claim/assets/pictures/uaap_basketball.jpg');"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-[#e8f5e1] via-[#f2faf0] to-[#deedd8]"></div>

        <div class="absolute -top-32 -left-32 w-[450px] h-[450px] bg-[#9fe870]/30 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-32 -right-32 w-[450px] h-[450px] bg-[#8ee05a]/25 rounded-full blur-[100px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[650px] h-[650px] bg-white/40 rounded-full blur-[120px]"></div>

        <div class="absolute inset-0 bg-[radial-gradient(#163300_1px,transparent_1px)] [background-size:28px_28px] opacity-[0.06]"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 lg:gap-12 items-center">
        
        <!-- Left Hero Branding Column -->
        <div class="lg:col-span-6 space-y-3 sm:space-y-6 text-left">
            <div class="space-y-1.5 sm:space-y-3">
                <div class="flex items-center gap-2.5 sm:gap-3.5">
                    <div class="w-11 h-11 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-[#9fe870] border border-[#163300]/20 flex items-center justify-center p-1.5 sm:p-2 shadow-xs overflow-hidden flex-shrink-0">
                        <img src="/claim/assets/pictures/AnimoClaim_Logo.png" alt="AnimoClaim Logo" class="w-full h-full object-cover" onerror="this.src='/claim/assets/pictures/Event_Poster.png'">
                    </div>
                    <h1 class="text-2xl sm:text-5xl font-black text-[#163300] tracking-tight leading-none">Animo<span class="text-[#4cae15]">Claim</span></h1>
                </div>
                <p class="text-xs sm:text-base text-gray-700 leading-snug sm:leading-relaxed font-semibold max-w-xl">
                    The official DLSU rewards and ticket distribution app. Claim student giveaways, events, and UAAP tickets just by scanning DLSU ID.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-2.5 sm:gap-3.5 pt-0.5">
                <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-white/90 border border-[#163300]/12 shadow-xs flex flex-col sm:flex-row items-start gap-2 sm:gap-3.5">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-[#e2f6d5] text-[#163300] flex items-center justify-center flex-shrink-0 font-black">
                        <i data-lucide="qr-code" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </div>
                    <div>
                        <p class="text-[10px] sm:text-xs font-black text-[#163300] uppercase tracking-wider leading-tight">Scan DLSU ID</p>
                        <p class="text-[9px] sm:text-[11px] text-gray-600 mt-0.5 font-medium leading-tight">Contactless scan at Gokongwei & Razon.</p>
                    </div>
                </div>

                <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-white/90 border border-[#163300]/12 shadow-xs flex flex-col sm:flex-row items-start gap-2 sm:gap-3.5">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-[#e2f6d5] text-[#163300] flex items-center justify-center flex-shrink-0 font-black">
                        <i data-lucide="clock" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </div>
                    <div>
                        <p class="text-[10px] sm:text-xs font-black text-[#163300] uppercase tracking-wider leading-tight">Zero Queue Slots</p>
                        <p class="text-[9px] sm:text-[11px] text-gray-600 mt-0.5 font-medium leading-tight">Guaranteed pickup windows & live stock.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Form Column -->
        <div class="lg:col-span-6 w-full max-w-md mx-auto">
            <div class="bg-white rounded-[24px] sm:rounded-[36px] shadow-[0_12px_40px_rgba(22,51,0,0.08)] border border-[#163300]/12 p-4 sm:p-8 flex flex-col relative overflow-hidden">
                
                <div class="absolute top-0 left-0 right-0 h-2 bg-[#9fe870]"></div>

                <div class="mb-3 sm:mb-5 text-center">
                    <h2 class="text-lg sm:text-xl font-black text-[#163300] tracking-tight">Welcome to AnimoClaim</h2>
                    <p class="text-[11px] sm:text-xs text-gray-500 font-medium mt-0.5">Log in or create your DLSU Archer account</p>
                </div>
                
                <div class="flex p-1 bg-[#f0f7ec] border border-[#163300]/10 rounded-xl sm:rounded-2xl mb-3 sm:mb-6">
                    <button id="tabLogin" onclick="switchAuthTab('login')" class="flex-1 py-2 sm:py-2.5 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-black uppercase tracking-wider transition-all cursor-pointer bg-[#9fe870] text-[#163300] shadow-xs wise-btn">
                        Log In
                    </button>
                    <button id="tabRegister" onclick="switchAuthTab('register')" class="flex-1 py-2 sm:py-2.5 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-black uppercase tracking-wider transition-all cursor-pointer text-gray-600 hover:text-[#163300]">
                        Sign Up
                    </button>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="text-red-700 text-xs font-bold px-3 py-2 sm:px-4 sm:py-3 bg-red-50 border border-red-200 rounded-xl text-center mb-3 sm:mb-5 flex items-center justify-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 flex-shrink-0"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- LOGIN FORM -->
                <form id="loginForm" method="POST" action="/claim/index.php" class="space-y-3 sm:space-y-4" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <div>
                        <label class="block text-[10px] font-black text-[#163300] uppercase tracking-widest mb-1 ml-1">DLSU ID Number</label>
                        <div class="relative">
                            <i data-lucide="credit-card" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                            <input type="text" name="login_id" id="dlsuIdInput" required placeholder="e.g. 12012345 or email" class="w-full pl-10 pr-4 h-11 sm:h-14 rounded-xl sm:rounded-2xl bg-[#f8fcf6] border border-[#163300]/15 focus:border-[#163300] focus:ring-2 focus:ring-[#9fe870]/40 outline-none text-[#163300] text-xs sm:text-sm font-bold transition-all placeholder:text-gray-400" />                        </div>
                        <p id="dlsuIdError" class="hidden text-[10px] font-bold text-red-600 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3 flex-shrink-0"></i> <span>Please enter your DLSU ID Number</span></p>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1 px-1">
                            <label class="block text-[10px] font-black text-[#163300] uppercase tracking-widest">Password</label>
                            <a href="/claim/forgot_password.php" class="text-[10px] font-extrabold text-[#163300] hover:underline transition-colors">Forgot Password?</a>
                        </div>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                            <input type="password" id="passwordInput" name="password" required placeholder="••••••••" class="w-full pl-10 pr-11 h-11 sm:h-14 rounded-xl sm:rounded-2xl bg-[#f8fcf6] border border-[#163300]/15 focus:border-[#163300] focus:ring-2 focus:ring-[#9fe870]/40 outline-none text-[#163300] text-xs sm:text-sm font-bold transition-all placeholder:text-gray-400" />
                            <button type="button" id="togglePassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#163300] transition-colors cursor-pointer focus:outline-none">
                                <i data-lucide="eye" id="eyeIcon" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            </button>
                        </div>
                        <p id="passwordError" class="hidden text-[10px] font-bold text-red-600 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3 flex-shrink-0"></i> <span>Please enter your password</span></p>
                    </div>

                    <button type="submit" class="w-full h-12 sm:h-14 bg-[#9fe870] text-[#163300] rounded-xl sm:rounded-2xl font-black text-xs sm:text-sm uppercase tracking-widest hover:bg-[#8ee05a] transition-all cursor-pointer shadow-md shadow-[#9fe870]/30 wise-btn flex items-center justify-center gap-2 mt-1 sm:mt-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Log In to Portal
                    </button>
                </form>

                <!-- SIGNUP FORM -->
                <form id="registerForm" method="POST" action="/claim/register.php" class="space-y-3.5 hidden" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-black text-[#163300] uppercase tracking-widest mb-1 ml-1">First Name</label>
                            <input type="text" name="first_name" required placeholder="Archer" class="w-full px-3.5 h-12 rounded-xl bg-[#f8fcf6] border border-[#163300]/15 focus:border-[#163300] outline-none text-[#163300] text-xs font-bold placeholder:text-gray-400" />
                            <p class="field-error hidden text-[10px] font-bold text-red-600 mt-1">First name required</p>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-[#163300] uppercase tracking-widest mb-1 ml-1">Last Name</label>
                            <input type="text" name="last_name" required placeholder="Green" class="w-full px-3.5 h-12 rounded-xl bg-[#f8fcf6] border border-[#163300]/15 focus:border-[#163300] outline-none text-[#163300] text-xs font-bold placeholder:text-gray-400" />
                            <p class="field-error hidden text-[10px] font-bold text-red-600 mt-1">Last name required</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#163300] uppercase tracking-widest mb-1 ml-1">DLSU ID Number</label>
                        <input type="text" name="dlsu_id" required placeholder="12012345" class="w-full px-3.5 h-12 rounded-xl bg-[#f8fcf6] border border-[#163300]/15 focus:border-[#163300] outline-none text-[#163300] text-xs font-bold placeholder:text-gray-400" />
                        <p class="field-error hidden text-[10px] font-bold text-red-600 mt-1">DLSU ID Number required</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#163300] uppercase tracking-widest mb-1 ml-1">AnimoMail Address</label>
                        <input type="email" name="email" required placeholder="first_last@dlsu.edu.ph" class="w-full px-3.5 h-12 rounded-xl bg-[#f8fcf6] border border-[#163300]/15 focus:border-[#163300] outline-none text-[#163300] text-xs font-bold placeholder:text-gray-400" />
                        <p class="field-error hidden text-[10px] font-bold text-red-600 mt-1">Valid email address required</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-black text-[#163300] uppercase tracking-widest mb-1 ml-1">Degree Program</label>
                            <select name="program" class="w-full px-3.5 h-12 rounded-xl bg-[#f8fcf6] border border-[#163300]/15 focus:border-[#163300] outline-none text-[#163300] text-xs font-bold">
                                <option value="BS Computer Science">BS Computer Science</option>
                                <option value="BS Information Technology">BS InfoTech</option>
                                <option value="CLA Liberal Arts">CLA Liberal Arts</option>
                                <option value="RVRCOB Business">RVRCOB Business</option>
                                <option value="GCOE Engineering">GCOE Engineering</option>
                                <option value="COS Science">COS Science</option>
                                <option value="SOE Economics">SOE Economics</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-[#163300] uppercase tracking-widest mb-1 ml-1">Role</label>
                            <select name="role" class="w-full px-3.5 h-12 rounded-xl bg-[#f8fcf6] border border-[#163300]/15 focus:border-[#163300] outline-none text-[#163300] text-xs font-bold">
                                <option value="student">Student</option>
                                <option value="organizer">USG Organizer</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#163300] uppercase tracking-widest mb-1 ml-1">Password</label>
                        <input type="password" name="password" required placeholder="Create strong passkey" class="w-full px-3.5 h-12 rounded-xl bg-[#f8fcf6] border border-[#163300]/15 focus:border-[#163300] outline-none text-[#163300] text-xs font-bold placeholder:text-gray-400" />
                        <p class="field-error hidden text-[10px] font-bold text-red-600 mt-1">Password required</p>
                    </div>

                    <button type="submit" class="w-full h-14 bg-[#9fe870] text-[#163300] rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-[#8ee05a] transition-all cursor-pointer shadow-md shadow-[#9fe870]/30 wise-btn flex items-center justify-center gap-2 mt-2">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        Register & Enter Portal
                    </button>
                </form>

            </div>
        </div>

    </div>

    <script>
        if (window.lucide) lucide.createIcons();

        function switchAuthTab(tab) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const tabLogin = document.getElementById('tabLogin');
            const tabRegister = document.getElementById('tabRegister');

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');

                tabLogin.className = "flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer bg-[#9fe870] text-[#163300] shadow-xs wise-btn";
                tabRegister.className = "flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer text-gray-600 hover:text-[#163300]";
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');

                tabRegister.className = "flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer bg-[#9fe870] text-[#163300] shadow-xs wise-btn";
                tabLogin.className = "flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer text-gray-600 hover:text-[#163300]";
            }
        }

        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            if (window.lucide) lucide.createIcons();
        });

        const loginForm = document.getElementById('loginForm');
        const dlsuIdInput = document.getElementById('dlsuIdInput');
        const passwordInput = document.getElementById('passwordInput');
        const dlsuIdError = document.getElementById('dlsuIdError');
        const passwordError = document.getElementById('passwordError');

        function clearInputError(input, errorEl) {
            input.classList.remove('border-red-500', 'bg-red-50/50', 'ring-2', 'ring-red-200');
            input.classList.add('border-[#163300]/15', 'bg-[#f8fcf6]');
            if (errorEl) errorEl.classList.add('hidden');
        }

        function showInputError(input, errorEl) {
            input.classList.remove('border-[#163300]/15', 'bg-[#f8fcf6]');
            input.classList.add('border-red-500', 'bg-red-50/50', 'ring-2', 'ring-red-200');
            if (errorEl) errorEl.classList.remove('hidden');
        }

        if (dlsuIdInput) dlsuIdInput.addEventListener('input', () => clearInputError(dlsuIdInput, dlsuIdError));
        if (passwordInput) passwordInput.addEventListener('input', () => clearInputError(passwordInput, passwordError));

        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                let isValid = true;

                if (!dlsuIdInput.value.trim()) {
                    showInputError(dlsuIdInput, dlsuIdError);
                    isValid = false;
                }

                if (!passwordInput.value.trim()) {
                    showInputError(passwordInput, passwordError);
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    if (window.lucide) lucide.createIcons();
                }
            });
        }

        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            const inputs = registerForm.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const errorEl = this.parentNode.querySelector('.field-error');
                    clearInputError(this, errorEl);
                });
            });

            registerForm.addEventListener('submit', function(e) {
                let isValid = true;
                inputs.forEach(input => {
                    const errorEl = input.parentNode.querySelector('.field-error');
                    if (!input.value.trim()) {
                        showInputError(input, errorEl);
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    if (window.lucide) lucide.createIcons();
                }
            });
        }
    </script>
</body>
</html>