<?php
require_once 'config/database.php';
if (isLoggedIn()) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']); $email = trim($_POST['email']); $fullname = trim($_POST['fullname']); $password = $_POST['password']; $role = $_POST['role'] ?? 'client';
    if (strlen($password) < 6) { $error = 'Password minimal 6 karakter'; } 
    else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, fullname, password_hash, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $fullname, password_hash($password, PASSWORD_DEFAULT), $role]);
            flashMessage('Registrasi berhasil!', 'success'); redirect('login.php');
        } catch (PDOException $e) { $error = 'Username atau email sudah terdaftar'; }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Negotiation Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #0B1021 0%, #1E3A8A 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .register-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); padding: 40px; max-width: 500px; width: 100%; }
        .btn-primary { background-color: #1E3A8A; border-color: #1E3A8A; }
    </style>
</head>
<body>
    <div class="register-card">
        <h3 class="text-center mb-4"><i class="bi bi-person-plus text-primary"></i> Daftar Akun</h3>
        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" name="fullname" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
            <div class="mb-4"><label class="form-label">Role</label><select name="role" class="form-select"><option value="client">Client</option><option value="internal">Internal (Tim Legal)</option></select></div>
            <button type="submit" class="btn btn-primary w-100 py-2">Daftar</button>
        </form>
        <div class="text-center mt-3"><p class="text-muted small">Sudah punya akun? <a href="login.php">Login</a></p></div>
    </div>
</body>
</html>