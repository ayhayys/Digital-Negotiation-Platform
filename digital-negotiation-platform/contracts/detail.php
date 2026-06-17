<?php
$pageTitle = 'Detail Kontrak';
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$user = getCurrentUser($pdo);
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT c.*, u_init.fullname as initiator_name, u_cli.fullname as client_name 
                       FROM contracts c 
                       JOIN users u_init ON c.initiator_id = u_init.user_id 
                       LEFT JOIN users u_cli ON c.client_id = u_cli.user_id 
                       WHERE c.contract_id = ?");
$stmt->execute([$id]);
$contract = $stmt->fetch();

if (!$contract) {
    flashMessage('Kontrak tidak ditemukan', 'danger');
    redirect(BASE_URL . '/contracts/index.php');
}

if ($contract['initiator_id'] != $user['user_id'] && $contract['client_id'] != $user['user_id']) {
    flashMessage('Akses ditolak', 'danger');
    redirect(BASE_URL . '/contracts/index.php');
}

$fileExt = strtolower(pathinfo($contract['file_path'], PATHINFO_EXTENSION));
$fileUrl = BASE_URL . $contract['file_path'];

require_once '../includes/header.php';
?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/contracts/index.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kontrak
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div>
                <h4 class="fw-bold mb-1"><?= htmlspecialchars($contract['title']) ?></h4>
                <p class="text-muted mb-0">
                    <i class="bi bi-calendar3 me-1"></i> Dibuat pada <?= formatDate($contract['created_at']) ?>
                </p>
            </div>
            <div>
                <?= getStatusBadge($contract['status']) ?>
            </div>
        </div>
        
        <hr class="my-3">
        
        <div class="row g-3">
            <div class="col-md-6">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Inisiator (Pengirim)</small>
                <div class="d-flex align-items-center mt-1">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="fw-semibold"><?= htmlspecialchars($contract['initiator_name']) ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Klien (Penerima)</small>
                <div class="d-flex align-items-center mt-1">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="fw-semibold"><?= htmlspecialchars($contract['client_name'] ?? '-') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Preview Dokumen</h6>
                <a href="<?= $fileUrl ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Buka Tab Baru
                </a>
            </div>
            <div class="card-body p-0 bg-light">
                <?php if ($fileExt === 'pdf'): ?>
                    <iframe src="<?= $fileUrl ?>" width="100%" height="700px" style="border: none; min-height: 70vh;">
                        <p>Browser Anda tidak mendukung iframe. Silakan download dokumen di bawah ini.</p>
                    </iframe>
                <?php elseif (in_array($fileExt, ['doc', 'docx'])): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-file-word text-primary" style="font-size: 5rem;"></i>
                        <h5 class="mt-3">Preview tidak tersedia untuk format Word</h5>
                        <p class="text-muted">Silakan download dokumen untuk melihat isinya.</p>
                        <a href="<?= $fileUrl ?>" class="btn btn-primary mt-2" download>
                            <i class="bi bi-download me-2"></i>Download Dokumen
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark text-muted" style="font-size: 5rem;"></i>
                        <h5 class="mt-3">Format file tidak didukung untuk preview</h5>
                        <a href="<?= $fileUrl ?>" class="btn btn-primary mt-2" download>
                            <i class="bi bi-download me-2"></i>Download Dokumen
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Informasi File</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <small class="text-muted d-block">Nama File</small>
                        <span class="fw-semibold text-break"><?= basename($contract['file_path']) ?></span>
                    </li>
                    <li class="mb-2">
                        <small class="text-muted d-block">Format</small>
                        <span class="badge bg-secondary text-uppercase"><?= $fileExt ?></span>
                    </li>
                    <li>
                        <small class="text-muted d-block">Lokasi Penyimpanan</small>
                        <code class="small"><?= htmlspecialchars($contract['file_path']) ?></code>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if (in_array($contract['status'], ['draft', 'in_review'])): ?>
                    <a href="<?= BASE_URL ?>/redlining/translate.php?contract_id=<?= $id ?>" class="btn btn-outline-primary">
                        <i class="bi bi-pencil-square me-2"></i>Smart Redlining / Revisi
                    </a>
                    <?php endif; ?>

                    <?php if ($contract['status'] !== 'signed'): ?>
                    <a href="<?= BASE_URL ?>/signature/sign.php?contract_id=<?= $id ?>" class="btn btn-success">
                        <i class="bi bi-pen me-2"></i>Tandatangani Kontrak
                    </a>
                    <?php else: ?>
                    <div class="alert alert-success mb-0 text-center">
                        <i class="bi bi-check-circle-fill me-1"></i> Kontrak Sudah Sah
                    </div>
                    <?php endif; ?>

                    <a href="<?= $fileUrl ?>" class="btn btn-outline-secondary" download>
                        <i class="bi bi-download me-2"></i>Download File
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>