<?php
$pageTitle = 'Dashboard';
require_once 'config/database.php'; 
require_once 'includes/auth.php'; 
checkAuth();

$user = getCurrentUser($pdo);
$uid = $user['user_id'];

$stmt = $pdo->prepare("SELECT c.*, u_init.fullname as initiator_name, u_cli.fullname as client_name FROM contracts c JOIN users u_init ON c.initiator_id = u_init.user_id LEFT JOIN users u_cli ON c.client_id = u_cli.user_id WHERE c.initiator_id = ? OR c.client_id = ? ORDER BY c.created_at DESC LIMIT 5");
$stmt->execute([$uid, $uid]); 
$recentContracts = $stmt->fetchAll();

$totalContracts = $pdo->query("SELECT COUNT(*) FROM contracts WHERE initiator_id = $uid OR client_id = $uid")->fetchColumn();
$reviewCount = $pdo->query("SELECT COUNT(*) FROM contracts WHERE (initiator_id = $uid OR client_id = $uid) AND status = 'in_review'")->fetchColumn();
$signedCount = $pdo->query("SELECT COUNT(*) FROM contracts WHERE (initiator_id = $uid OR client_id = $uid) AND status = 'signed'")->fetchColumn();

require_once 'includes/header.php';
?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Total Kontrak</p>
                        <h3 class="mb-0 fw-bold"><?= $totalContracts ?></h3>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-folder2-open"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">In Review</p>
                        <h3 class="mb-0 fw-bold"><?= $reviewCount ?></h3>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Signed</p>
                        <h3 class="mb-0 fw-bold"><?= $signedCount ?></h3>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Kontrak Terbaru -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold">Aktivitas Kontrak Terbaru</h6>
                <a href="<?= BASE_URL ?>/contracts/index.php" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Judul</th>
                                <th>Pihak Terlibat</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentContracts)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada kontrak</td></tr>
                            <?php else: foreach ($recentContracts as $c): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($c['title']) ?></td>
                                <td class="small"><?= htmlspecialchars($c['initiator_name']) ?> &harr; <?= htmlspecialchars($c['client_name'] ?? 'Umum') ?></td>
                                <td><?= getStatusBadge($c['status']) ?></td>
                                <td class="small text-muted"><?= formatDate($c['created_at']) ?></td>
                                <td><a href="<?= BASE_URL ?>/contracts/detail.php?id=<?= $c['contract_id'] ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Akses Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>/contracts/upload.php" class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>Upload Kontrak Baru
                    </a>
                    <a href="<?= BASE_URL ?>/redlining/index.php" class="btn btn-outline-primary">
                        <i class="bi bi-pencil-square me-2"></i>Smart Redlining
                    </a>
                    <a href="<?= BASE_URL ?>/analytics/non_verbal.php" class="btn btn-outline-success">
                        <i class="bi bi-camera-video me-2"></i>Non-Verbal Analytics
                    </a>
                    <a href="<?= BASE_URL ?>/signature/index.php" class="btn btn-outline-info">
                        <i class="bi bi-pen me-2"></i>E-Signature
                    </a>
                    <a href="<?= BASE_URL ?>/training/index.php" class="btn btn-outline-warning">
                        <i class="bi bi-mortarboard me-2"></i>Culture Training
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>