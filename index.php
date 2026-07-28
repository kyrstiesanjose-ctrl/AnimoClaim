<?php
require_once 'config/database.php';

$base_url = '/claim';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid session token. Please refresh and try again.";
    } else {
        $dlsu_id = trim($_POST['dlsu_id'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE dlsu_id = ? LIMIT 1");
        $stmt->execute([$dlsu_id]);
        $user = $stmt->fetch();

        // Fallback to allow plain text "password123" during development
        if ($user && ($password === 'password123' || password_verify($password, $user['password']))) {

            $strikeStmt = $pdo->prepare("SELECT COUNT(*) FROM strike_logs WHERE user_id = ?");
            $strikeStmt->execute([$user['id']]);
            $strikes = $strikeStmt->fetchColumn();

            if ($user['status'] !== 'active' || $strikes >= 3) {
                $error = "Account suspended due to policy violations (3 or more strikes).";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['dlsu_id'] = $user['dlsu_id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['role'] = $user['role'];

                header("Location: " . $base_url . ($user['role'] === 'organizer' ? '/organizer/dashboard.php' : '/student/index.php'));
                exit;
            }
        } else {
            $error = "Invalid credentials.";
        }
    }
}

$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnimoClaim &mdash; DLSU Event & Merchandise Distribution Portal</title>
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
        <div class="absolute inset-0 bg-cover bg-center opacity-10 animate-bg-zoom mix-blend-multiply" style="background-image: url('<?php echo $base_url; ?>/assets/pictures/uaap_basketball.jpg');"></div>
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
                        <img src="<?php echo $base_url; ?>/assets/pictures/AnimoClaim_Logo.png" alt="AnimoClaim Logo" class="w-full h-full object-cover" onerror="this.src='<?php echo $base_url; ?>/assets/pictures/Event_Poster.png'">
                    </div>
                    <h1 class="text-2xl sm:text-5xl font-black text-[#163300] tracking-tight leading-none">Animo<span class="text-[#4cae15]">Claim</span></h1>
                </div>
                <p class="text-xs sm:text-base text-gray-700 leading-snug sm:leading-relaxed font-semibold max-w-xl">
                    The official DLSU rewards and ticket distribution app. Claim student giveaways, victory merch, and event passkeys with instant QR verification.
                </p>
            </div>

            <!-- Feature Pills Grid -->
            <div class="grid grid-cols-2 gap-2.5 sm:gap-3.5 pt-0.5">
                <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-white/90 border border-[#163300]/12 shadow-xs flex flex-col sm:flex-row items-start gap-2 sm:gap-3.5">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-[#e2f6d5] text-[#163300] flex items-center justify-center flex-shrink-0 font-black">
                        <i data-lucide="qr-code" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </div>
                    <div>
                        <p class="text-[10px] sm:text-xs font-black text-[#163300] uppercase tracking-wider leading-tight">Instant Onsite QR</p>
                        <p class="text-[9px] sm:text-[11px] text-gray-600 mt-0.5 font-medium leading-tight">Contactless scan at Gokongwei & Razon.</p>
                    </div>
                </div>

                <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-white/90 border border-[#163300]/12 shadow-xs flex flex-col sm:flex-row items-start gap-2 sm:gap-3.5">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-[#e2f6d5] text-[#163300] flex items-center justify-center flex-shrink-0 font-black">
                        <i data-lucide="clock" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </div>
                    <div>
                        <p class="text-[10px] sm:text-xs font-black text-[#163300] uppercase tracking-wider leading-tight">Zero-Queue Slots</p>
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
                    <p class="text-[11px] sm:text-xs text-gray-500 font-medium mt-0.5">Log in to your DLSU Archer account</p>
                </div>

                <?php if ($error): ?>
                    <div class="text-red-700 text-xs font-bold px-3 py-2 sm:px-4 sm:py-3 bg-red-50 border border-red-200 rounded-xl text-center mb-3 sm:mb-5 flex items-center justify-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 flex-shrink-0"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- LOGIN FORM -->
                <form id="loginForm" method="POST" action="<?php echo $base_url; ?>/index.php" class="space-y-3 sm:space-y-4" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <div>
                        <label class="block text-[10px] font-black text-[#163300] uppercase tracking-widest mb-1 ml-1">DLSU ID Number</label>
                        <div class="relative">
                            <i data-lucide="credit-card" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                            <input type="text" name="dlsu_id" id="dlsuIdInput" required placeholder="e.g. 12012345" class="w-full pl-10 pr-4 h-11 sm:h-14 rounded-xl sm:rounded-2xl bg-[#f8fcf6] border border-[#163300]/15 focus:border-[#163300] focus:ring-2 focus:ring-[#9fe870]/40 outline-none text-[#163300] text-xs sm:text-sm font-bold transition-all placeholder:text-gray-400" />
                        </div>
                        <p id="dlsuIdError" class="hidden text-[10px] font-bold text-red-600 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3 flex-shrink-0"></i> <span>Please enter your DLSU ID Number</span></p>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1 px-1">
                            <label class="block text-[10px] font-black text-[#163300] uppercase tracking-widest">Password</label>
                            <a href="<?php echo $base_url; ?>/forgot_password.php" class="text-[10px] font-extrabold text-[#163300] hover:underline transition-colors">Forgot Password?</a>
                        </div>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4"></i>
                            <input type="password" id="passwordInput" name="password" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" class="w-full pl-10 pr-11 h-11 sm:h-14 rounded-xl sm:rounded-2xl bg-[#f8fcf6] border border-[#163300]/15 focus:border-[#163300] focus:ring-2 focus:ring-[#9fe870]/40 outline-none text-[#163300] text-xs sm:text-sm font-bold transition-all placeholder:text-gray-400" />
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

            </div>
        </div>

    </div>

    <script>
        if (window.lucide) lucide.createIcons();

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
    </script>
</body>
</html>