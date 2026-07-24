<?php
require_once 'config/database.php';

// 1. Define the base URL so your logo image path works correctly
$base_url = '/claim';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dlsu_id = trim($_POST['dlsu_id'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE dlsu_id = ? LIMIT 1");
    $stmt->execute([$dlsu_id]);
    $user = $stmt->fetch();

    // 2. Added a fallback to allow plain text "password123" during development
    if ($user && ($password === 'password123' || password_verify($password, $user['password']))) {
        
        // Count strikes from the new strike_logs table
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
            
            // Redirect using the base_url
            header("Location: " . $base_url . ($user['role'] === 'organizer' ? '/organizer/dashboard.php' : '/student/index.php'));
            exit;
        }
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - AnimoClaim</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1 { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-[#011809] min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-[#1c261b] rounded-[32px] shadow-2xl border border-white/5 flex flex-col px-8 py-10">
        <div class="flex flex-col items-center mb-8">
           <div class="w-16 h-16 mb-3 flex items-center justify-center bg-[#c6f135]/10 rounded-full border border-[#c6f135]/20 overflow-hidden shadow-inner">
            <img src="<?php echo $base_url; ?>/assets/pictures/AnimoClaim_Logo.png" alt="Logo" class="w-full h-full object-cover">
        </div>
            <h1 class="text-4xl font-black text-[#c6f135] tracking-tighter mb-1">AnimoClaim</h1>
        </div>
        
        <?php if ($error): ?>
            <div class="text-red-400 text-xs font-semibold px-4 py-3 bg-red-900/10 border border-red-500/10 rounded-lg text-center mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="w-full flex flex-col gap-4">
            <div>
                <label class="block text-[10px] font-black text-[#c6f135] uppercase tracking-widest mb-1.5 ml-1">DLSU ID Number</label>
                <input type="text" name="dlsu_id" required placeholder="12012345" class="w-full px-4 h-14 rounded-2xl bg-[#0f2419] border border-white/10 focus:border-[#c6f135] focus:ring-1 focus:ring-[#c6f135] outline-none text-white text-sm transition-all" />
            </div>
            <div>
                <div class="flex justify-between items-center mb-1.5 px-1">
                    <label class="block text-[10px] font-black text-[#c6f135] uppercase tracking-widest">Password</label>
                    <a href="forgot_password.php" class="text-[10px] font-bold text-white/40 hover:text-[#c6f135] transition-colors">Forgot Password?</a>
                </div>
                <div class="relative">
                    <input type="password" id="passwordInput" name="password" required placeholder="••••••••" class="w-full pl-4 pr-12 h-14 rounded-2xl bg-[#0f2419] border border-white/10 focus:border-[#c6f135] focus:ring-1 focus:ring-[#c6f135] outline-none text-white text-sm transition-all" />
                    <!-- See Password Toggle Button -->
                    <button type="button" id="togglePassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/40 hover:text-[#c6f135] transition-colors cursor-pointer focus:outline-none">
                        <i data-lucide="eye" id="eyeIcon" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="w-full h-14 mt-4 bg-[#c6f135] text-[#1c261b] rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-[#b8e02d] transition-all cursor-pointer shadow-lg shadow-[#c6f135]/10">Login</button>
        </form>
    </div>

    <script>
        // Initialize Icons
        lucide.createIcons();

        // See Password Toggle Logic
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
            
            // Re-render the new icon
            lucide.createIcons();
        });
    </script>
</body>
</html>