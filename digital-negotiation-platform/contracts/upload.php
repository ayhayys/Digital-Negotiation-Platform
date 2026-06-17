<?php
$pageTitle = 'Upload Kontrak';
require_once '../config/database.php'; 
require_once '../includes/auth.php'; 
checkAuth();

$user = getCurrentUser($pdo); 
$error = '';

$clients = $pdo->query("SELECT user_id, fullname FROM users WHERE role = 'client'")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']); 
    $client_id = (int) $_POST['client_id'];
    
    if (empty($title) || !$client_id) { 
        $error = 'Judul dan Klien wajib diisi'; 
    } elseif (!isset($_FILES['contract_file']) || $_FILES['contract_file']['error'] !== UPLOAD_ERR_OK) { 
        $error = 'File wajib diupload'; 
    } else {
        $file = $_FILES['contract_file']; 
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, ['pdf', 'doc', 'docx'])) { 
            $error = 'Format PDF/DOC/DOCX only'; 
        } elseif ($file['size'] > 10 * 1024 * 1024) {
            $error = 'Ukuran file maksimal 10MB';
        } else {
            $uploadDir = __DIR__ . '/../storage/docs/'; 
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            $fileName = uniqid('contract_') . '.' . $ext;
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                $relativePath = '/storage/docs/' . $fileName;
                try {
                    $stmt = $pdo->prepare("INSERT INTO contracts (title, file_path, status, initiator_id, client_id) VALUES (?, ?, 'in_review', ?, ?)");
                    $stmt->execute([$title, $relativePath, $user['user_id'], $client_id]);
                    
                    flashMessage('Kontrak berhasil dikirim ke klien!', 'success');
                    header("Location: " . BASE_URL . "/contracts/index.php");
                    exit();
                } catch (PDOException $e) {
                    $error = 'Gagal menyimpan ke database: ' . $e->getMessage();
                }
            } else {
                $error = 'Gagal mengupload file. Pastikan folder storage/docs/ writable.';
            }
        }
    }
}
require_once '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Inisiasi Kontrak Baru</h5>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Judul Kontrak <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="Contoh: Perjanjian Kerahasiaan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kirim Kepada (Klien) <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-select" required>
                            <option value="">-- Pilih Klien --</option>
                            <?php foreach ($clients as $c): ?>
                            <option value="<?= $c['user_id'] ?>"><?= htmlspecialchars($c['fullname']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">File Kontrak <span class="text-danger">*</span></label>
                        <input type="file" name="contract_file" class="form-control" accept=".pdf,.doc,.docx" required>
                        <small class="text-muted">Maksimal 10MB. Format: PDF, DOC, DOCX</small>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send me-2"></i>Kirim untuk Review</button>
                    <a href="<?= BASE_URL ?>/contracts/index.php" class="btn btn-secondary ms-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>