<?php
$pageTitle = 'Manajemen Kontrak';
require_once '../config/database.php'; 
require_once '../includes/auth.php'; 
checkAuth();

$user = getCurrentUser($pdo); 
$uid = $user['user_id'];

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM contracts WHERE contract_id = ? AND initiator_id = ?");
        $stmt->execute([$id, $uid]);
        
        flashMessage('Kontrak berhasil dihapus', 'success');
    } catch (PDOException $e) {
        flashMessage('Gagal menghapus kontrak', 'danger');
    }

    header("Location: " . BASE_URL . "/contracts/index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT c.*, u_init.fullname as initiator_name, u_cli.fullname as client_name FROM contracts c JOIN users u_init ON c.initiator_id = u_init.user_id LEFT JOIN users u_cli ON c.client_id = u_cli.user_id WHERE c.initiator_id = ? OR c.client_id = ? ORDER BY c.created_at DESC");
$stmt->execute([$uid, $uid]); 
$contracts = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Daftar Kontrak</h4>
    <a href="<?= BASE_URL ?>/contracts/upload.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Upload Kontrak Baru
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Pihak</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contracts)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada kontrak.
                        </td>
                    </tr>
                    <?php else: foreach ($contracts as $c): ?>
                    <tr>
                        <td>#<?= $c['contract_id'] ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($c['title']) ?></td>
                        <td class="small"><?= htmlspecialchars($c['initiator_name']) ?> &harr; <?= htmlspecialchars($c['client_name'] ?? '-') ?></td>
                        <td><?= getStatusBadge($c['status']) ?></td>
                        <td class="small"><?= formatDate($c['created_at']) ?></td>
                        <td class="text-end">
                            <a href="<?= BASE_URL ?>/contracts/detail.php?id=<?= $c['contract_id'] ?>" class="btn btn-sm btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>

                            <?php if ($c['initiator_id'] == $uid): ?>
                            <a href="?delete=<?= $c['contract_id'] ?>" 
                               class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Yakin ingin menghapus kontrak ini? Tindakan ini tidak dapat dibatalkan.')"
                               title="Hapus">
                                <i class="bi bi-trash"></i>
                            </a>
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