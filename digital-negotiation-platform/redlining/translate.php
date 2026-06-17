<?php
$pageTitle = 'Redlining & Translation';
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$user = getCurrentUser($pdo);
$contractId = (int) ($_GET['contract_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM contracts WHERE contract_id = ? AND initiator_id = ?");
$stmt->execute([$contractId, $user['user_id']]);
$contract = $stmt->fetch();

if (!$contract) {
    flashMessage('Kontrak tidak ditemukan', 'danger');
    redirect(BASE_URL . '/redlining/index.php');
}

$revisions = $pdo->prepare("SELECT r.*, u.fullname FROM redlining_logs r JOIN users u ON r.user_id = u.user_id WHERE r.contract_id = ? ORDER BY r.timestamp DESC");
$revisions->execute([$contractId]);
$revisions = $revisions->fetchAll();

require_once '../includes/header.php';
?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/redlining/index.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><?= htmlspecialchars($contract['title']) ?></h5>
        <small class="text-muted">Sunting dan terjemahkan klausul kontrak</small>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/redlining/save_revision.php">
            <input type="hidden" name="contract_id" value="<?= $contractId ?>">
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Teks Asli (Original)</label>
                    <textarea name="original_text" class="form-control" rows="6" required placeholder="Salin teks asli dari kontrak di sini..."></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Teks Revisi (Revised)</label>
                    <textarea name="revised_text" class="form-control" rows="6" required placeholder="Tulis perubahan/revisi di sini..."></textarea>
                </div>
            </div>
            
            <div class="mt-3">
                <label class="form-label fw-bold">Terjemahan (Opsional)</label>
                <div class="input-group">
                    <select name="target_lang" class="form-select" style="max-width: 150px;">
                        <option value="en">English</option>
                        <option value="ja">Japanese</option>
                        <option value="zh">Chinese</option>
                        <option value="ar">Arabic</option>
                    </select>
                    <textarea name="translated_text" class="form-control" rows="2" placeholder="Hasil terjemahan AI akan muncul di sini (Simulasi)"></textarea>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success mt-3">
                <i class="bi bi-check-circle me-2"></i>Simpan Revisi
            </button>
        </form>
    </div>
</div>

<?php if (!empty($revisions)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Riwayat Redlining</h6>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <?php foreach ($revisions as $rev): ?>
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <strong><?= htmlspecialchars($rev['fullname']) ?></strong>
                    <small class="text-muted"><?= formatDate($rev['timestamp']) ?></small>
                </div>
                <div class="mt-2 small">
                    <div class="text-danger"><del>[-] <?= htmlspecialchars($rev['original_text']) ?></del></div>
                    <div class="text-success">[+] <?= htmlspecialchars($rev['revised_text']) ?></div>
                    <?php if ($rev['translated_text']): ?>
                    <div class="text-primary mt-1 fst-italic"> <?= htmlspecialchars($rev['translated_text']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>