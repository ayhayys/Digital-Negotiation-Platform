<?php
require_once 'config/database.php';
if (isLoggedIn()) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['fullname'];
        $_SESSION['user_role'] = $user['role'];
        redirect('dashboard.php');
    }
    $error = 'Username/email atau password salah';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Negotiation Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #0B1021 0%, #1E3A8A 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .login-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); padding: 40px; max-width: 450px; width: 100%; }
        .brand-logo i { font-size: 4rem; color: #1E3A8A; }
        .btn-primary { background-color: #1E3A8A; border-color: #1E3A8A; }
        .btn-primary:hover { background-color: #0B1021; border-color: #0B1021; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-logo text-center mb-4"><i class="bi bi-file-earmark-text"></i><h3 class="mt-3">Negotiation Platform</h3><p class="text-muted">Digital Negotiation Platform</p></div>
        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3"><label class="form-label">Username / Email</label><div class="input-group"><span class="input-group-text"><i class="bi bi-person"></i></span><input type="text" name="username" class="form-control" required></div></div>
            <div class="mb-4"><label class="form-label">Password</label><div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span><input type="password" name="password" class="form-control" required></div></div>
            <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
        </form>
        <div class="text-center mt-4"><p class="text-muted small">Belum punya akun? <a href="register.php">Daftar disini</a></p></div>
    </div>
</body>
</html>