<?php
$pageTitle = 'Update Status Kontrak';
$baseUrl = '..';
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$user = getCurrentUser($pdo);
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM contracts WHERE contract_id = ? AND initiator_id = ?");
$stmt->execute([$id, $user['user_id']]);
$contract = $stmt->fetch();

if (!$contract) {
    flashMessage('Kontrak tidak ditemukan', 'danger');
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE contracts SET status = ? WHERE contract_id = ?");
    $stmt->execute([$status, $id]);
    flashMessage('Status berhasil diupdate', 'success');
    redirect('detail.php?id=' . $id);
}

require_once '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Update Status Kontrak</h5>
                <small class="text-muted"><?= htmlspecialchars($contract['title']) ?></small>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" <?= $contract['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="in_review" <?= $contract['status'] === 'in_review' ? 'selected' : '' ?>>In Review</option>
                            <option value="signed" <?= $contract['status'] === 'signed' ? 'selected' : '' ?>>Signed</option>
                            <option value="cancelled" <?= $contract['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="<?= BASE_URL ?>/contracts/index.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>