<?php
$pageTitle = 'Culture Training';
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$user = getCurrentUser($pdo);

$history = $pdo->prepare("SELECT * FROM culture_simulations WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$history->execute([$user['user_id']]);
$history = $history->fetchAll();

$cultures = [
    'japanese' => ['name' => 'Jepang', 'icon' => '🇵', 'desc' => 'Formal, hierarkis, menghargai kesopanan dan keheningan.'],
    'american' => ['name' => 'Amerika', 'icon' => '🇺🇸', 'desc' => 'To-the-point, berorientasi hasil, menghargai efisiensi waktu.'],
    'arab' => ['name' => 'Arab', 'icon' => '🇸🇦', 'desc' => 'Mengutamakan hubungan personal (relationship-first), negosiasi fleksibel.'],
    'chinese' => ['name' => 'China', 'icon' => '🇨🇳', 'desc' => 'Guanxi (koneksi), menghormati senioritas, komunikasi tidak langsung.'],
    'german' => ['name' => 'Jerman', 'icon' => '🇩', 'desc' => 'Sangat terstruktur, tepat waktu, detail-oriented, kritik langsung.'],
    'indian' => ['name' => 'India', 'icon' => '🇮🇳', 'desc' => 'Hierarkis, negosiasi polikronik (tidak terburu-buru), fleksibel.']
];

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Culture Simulation Training</h4>
</div>

<div class="row g-4 mb-4">
    <?php foreach ($cultures as $key => $culture): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div style="font-size: 4rem;"><?= $culture['icon'] ?></div>
                <h5 class="mt-3"><?= $culture['name'] ?></h5>
                <p class="text-muted small"><?= $culture['desc'] ?></p>
                <a href="<?= BASE_URL ?>/training/simulate.php?culture=<?= $key ?>" class="btn btn-primary w-100 mt-auto">
                    <i class="bi bi-play-circle me-2"></i>Mulai Simulasi
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (!empty($history)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Riwayat Simulasi Terakhir</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Budaya</th><th>Skor</th><th>Feedback</th><th>Tanggal</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($h['culture_type']) ?></td>
                        <td><span class="badge bg-<?= $h['performance_score'] >= 80 ? 'success' : ($h['performance_score'] >= 60 ? 'warning' : 'danger') ?>"><?= $h['performance_score'] ?></span></td>
                        <td class="small"><?= htmlspecialchars(substr($h['feedback_text'] ?? '-', 0, 50)) ?>...</td>
                        <td class="small"><?= formatDate($h['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>