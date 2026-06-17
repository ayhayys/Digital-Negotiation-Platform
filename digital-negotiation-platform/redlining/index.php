<?php
$pageTitle = 'Smart Redlining';
require_once '../config/database.php'; require_once '../includes/auth.php'; checkAuth();
$user = getCurrentUser($pdo); $uid = $user['user_id'];

$stmt = $pdo->prepare("SELECT c.*, u_init.fullname as initiator_name FROM contracts c JOIN users u_init ON c.initiator_id = u_init.user_id WHERE (c.initiator_id = ? OR c.client_id = ?) AND c.status = 'in_review'");
$stmt->execute([$uid, $uid]); $contracts = $stmt->fetchAll();
require_once '../includes/header.php';
?>
<div class="mb-4"><h4>Smart Redlining</h4><p class="text-muted">Pilih kontrak yang sedang dalam status <b>In Review</b> untuk disunting.</p></div>
<div class="card border-0 shadow-sm"><div class="card-body">
    <?php if (empty($contracts)): ?><div class="text-center py-5 text-muted">Tidak ada kontrak yang perlu direvisi saat ini.</div>
    <?php else: ?><div class="row g-3"><?php foreach ($contracts as $c): ?>
        <div class="col-md-6"><div class="card h-100 border"><div class="card-body">
            <h6 class="mb-0"><?= htmlspecialchars($c['title']) ?></h6>
            <small class="text-muted d-block mb-3">Dari: <?= htmlspecialchars($c['initiator_name']) ?></small>
            <a href="<?= BASE_URL ?>/redlining/translate.php?contract_id=<?= $c['contract_id'] ?>" class="btn btn-primary w-100">Edit & Terjemahkan</a>
        </div></div></div>
    <?php endforeach; ?></div><?php endif; ?>
</div></div>
<?php require_once '../includes/footer.php'; ?>