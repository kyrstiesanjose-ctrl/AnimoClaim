<?php
require_once 'config/database.php';

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dlsu_id = trim($_POST['dlsu_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // Verify identity by matching DLSU ID and Email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE dlsu_id = ? AND email = ?");
        $stmt->execute([$dlsu_id, $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Identity verified, hash new password and update
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            
            if ($update->execute([$hashed_password, $user['id']])) {
                $success = "Password successfully changed! You can now log in.";
            } else {
                $error = "An error occurred while updating your password.";
            }
        } else {
            $error = "Identity verification failed. No account matches that ID and Email combination.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - AnimoClaim</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1 { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-[#011809] min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-[#1c261b] rounded-[32px] shadow-2xl border border-white/5 flex flex-col px-8 py-10 relative overflow-hidden">
        
        <!-- Back button -->
        <a href="index.php" class="absolute top-6 left-6 text-white/40 hover:text-white transition-colors cursor-pointer">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>

        <div class="flex flex-col items-center mb-8 mt-4">
            <div class="w-16 h-16 mb-4 flex items-center justify-center bg-blue-500/10 rounded-full border border-blue-500/20">
                <i data-lucide="shield-alert" class="w-8 h-8 text-blue-400"></i>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tighter mb-1">Reset Password</h1>
            <p class="text-white/40 text-xs text-center px-4 leading-relaxed mt-2">
                Verify your identity by entering your DLSU ID and registered DLSU Email to create a new password.
            </p>
        </div>
        
        <?php if ($error): ?>
            <div class="text-red-400 text-xs font-semibold px-4 py-3 bg-red-900/10 border border-red-500/10 rounded-lg text-center mb-4 leading-relaxed">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="text-green-400 text-xs font-semibold px-4 py-3 bg-green-900/10 border border-green-500/10 rounded-lg text-center mb-4 leading-relaxed flex flex-col items-center gap-3">
                <i data-lucide="check-circle-2" class="w-6 h-6 text-green-400"></i>
                <?php echo htmlspecialchars($success); ?>
                <a href="index.php" class="mt-2 inline-block px-4 py-2 bg-green-500/20 text-green-400 rounded-lg hover:bg-green-500/30 transition-colors">Return to Login</a>
            </div>
        <?php else: ?>

            <form method="POST" class="w-full flex flex-col gap-4">
                <div>
                    <label class="block text-[10px] font-black text-white/60 uppercase tracking-widest mb-1.5 ml-1">DLSU ID Number</label>
                    <input type="text" name="dlsu_id" required placeholder="12012345" class="w-full px-4 h-12 rounded-xl bg-[#0f2419] border border-white/10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-white text-sm transition-all" />
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-white/60 uppercase tracking-widest mb-1.5 ml-1">Registered DLSU Email</label>
                    <input type="email" name="email" required placeholder="name.surname@dlsu.edu.ph" class="w-full px-4 h-12 rounded-xl bg-[#0f2419] border border-white/10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-white text-sm transition-all" />
                </div>

                <hr class="border-white/5 my-2">

                <div>
                    <label class="block text-[10px] font-black text-white/60 uppercase tracking-widest mb-1.5 ml-1">New Password</label>
                    <input type="password" name="new_password" required placeholder="••••••••" class="w-full px-4 h-12 rounded-xl bg-[#0f2419] border border-white/10 focus:border-[#c6f135] focus:ring-1 focus:ring-[#c6f135] outline-none text-white text-sm transition-all" />
                </div>

                <div>
                    <label class="block text-[10px] font-black text-white/60 uppercase tracking-widest mb-1.5 ml-1">Confirm New Password</label>
                    <input type="password" name="confirm_password" required placeholder="••••••••" class="w-full px-4 h-12 rounded-xl bg-[#0f2419] border border-white/10 focus:border-[#c6f135] focus:ring-1 focus:ring-[#c6f135] outline-none text-white text-sm transition-all" />
                </div>

                <button type="submit" class="w-full h-14 mt-4 bg-blue-500 text-white rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-blue-600 transition-all cursor-pointer shadow-lg shadow-blue-500/20">
                    Change Password
                </button>
            </form>

        <?php endif; ?>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>