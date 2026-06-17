<?php
$pageTitle = 'E-Signature';
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$user = getCurrentUser($pdo);
$uid = $user['user_id'];

$stmt = $pdo->prepare("SELECT c.*, u_init.fullname as initiator_name FROM contracts c JOIN users u_init ON c.initiator_id = u_init.user_id WHERE (c.initiator_id = ? OR c.client_id = ?) AND c.status != 'signed'");
$stmt->execute([$uid, $uid]);
$contracts = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Cross-Jurisdiction E-Signature</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul Kontrak</th>
                        <th>Inisiator</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contracts)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada kontrak untuk ditandatangani.
                        </td>
                    </tr>
                    <?php else: foreach ($contracts as $c): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($c['title']) ?></td>
                        <td><?= htmlspecialchars($c['initiator_name']) ?></td>
                        <td><?= getStatusBadge($c['status']) ?></td>
                        <td class="small"><?= formatDate($c['created_at']) ?></td>
                        <td class="text-end">
                            <?php if ($c['status'] !== 'signed'): ?>
                            <a href="<?= BASE_URL ?>/signature/sign.php?contract_id=<?= $c['contract_id'] ?>" class="btn btn-sm btn-success">
                                <i class="bi bi-pen me-1"></i>Tandatangani
                            </a>
                            <?php else: ?>
                            <span class="text-success small"><i class="bi bi-check-circle"></i> Signed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>