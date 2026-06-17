<?php
$pageTitle = 'Simulasi Budaya';
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$user = getCurrentUser($pdo);
$culture = $_GET['culture'] ?? 'japanese';

$cultures = [
    'japanese' => ['name' => 'Jepang', 'icon' => '🇯🇵', 'scenarios' => [
        ['q' => 'Klien Jepang diam lama setelah Anda mengajukan proposal. Apa artinya?', 'options' => [['text' => 'Mereka tidak tertarik, tawarkan diskon', 'score' => 30], ['text' => 'Mereka sedang mempertimbangkan dengan serius, tunggu sabar', 'score' => 100], ['text' => 'Mereka marah, segera minta maaf', 'score' => 40]], 'tip' => 'Keheningan dalam budaya Jepang adalah tanda refleksi, bukan penolakan.'],
        ['q' => 'Klien memberikan business card (meishi). Bagaimana Anda menerimanya?', 'options' => [['text' => 'Satu tangan dan masuk saku', 'score' => 30], ['text' => 'Dua tangan, pelajari, simpan dengan hati-hati', 'score' => 100], ['text' => 'Tolak dengan sopan', 'score' => 10]], 'tip' => 'Business card di Jepang adalah representasi diri. Perlakukan dengan hormat.']
    ]],
    'american' => ['name' => 'Amerika', 'icon' => '🇺🇸', 'scenarios' => [
        ['q' => 'Klien Amerika langsung bertanya "Berapa harganya?" di awal meeting.', 'options' => [['text' => 'Langsung sebutkan harga', 'score' => 70], ['text' => 'Jelaskan value terlebih dahulu, baru harga', 'score' => 100], ['text' => 'Tolak menjawab sampai building rapport', 'score' => 40]], 'tip' => 'Orang Amerika menghargai directness tapi juga value proposition.'],
        ['q' => 'Klien Amerika ingin meeting dipercepat karena "time is money".', 'options' => [['text' => 'Setuju dan langsung ke inti pembicaraan', 'score' => 100], ['text' => 'Tetap dengan agenda lengkap', 'score' => 50], ['text' => 'Marah karena dianggap tidak penting', 'score' => 10]], 'tip' => 'Efisiensi waktu sangat dihargai dalam budaya bisnis Amerika.']
    ]]
];

if (!isset($cultures[$culture])) {
    flashMessage('Skenario budaya belum tersedia', 'warning');
    redirect(BASE_URL . '/training/index.php');
}

$currentCulture = $cultures[$culture];
$scenarios = $currentCulture['scenarios'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $totalScore = 0;
    foreach ($scenarios as $idx => $scenario) {
        $selected = (int) ($_POST['q' . $idx] ?? 0);
        $option = $scenario['options'][$selected] ?? null;
        $totalScore += $option ? $option['score'] : 0;
    }
    
    $avgScore = round($totalScore / count($scenarios));
    $feedback = $avgScore >= 80 ? 'Luar biasa! Pemahaman budaya sangat baik.' : ($avgScore >= 60 ? 'Bagus, namun masih perlu peningkatan.' : 'Perlu mempelajari kembali karakteristik budaya ini.');
    
    $stmt = $pdo->prepare("INSERT INTO culture_simulations (user_id, culture_type, performance_score, feedback_text) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user['user_id'], $currentCulture['name'], $avgScore, $feedback]);
    
    flashMessage("Simulasi selesai! Skor Anda: $avgScore", 'success');
    redirect(BASE_URL . '/training/index.php');
}

require_once '../includes/header.php';
?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/training/index.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body text-center">
        <div style="font-size: 4rem;"><?= $currentCulture['icon'] ?></div>
        <h4 class="mt-2">Simulasi Budaya <?= $currentCulture['name'] ?></h4>
        <p class="text-muted">Jawab <?= count($scenarios) ?> pertanyaan skenario berikut</p>
    </div>
</div>

<form method="POST">
    <?php foreach ($scenarios as $idx => $scenario): ?>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <h6 class="fw-bold">Pertanyaan <?= $idx + 1 ?></h6>
            <p class="mb-3"><?= htmlspecialchars($scenario['q']) ?></p>
            <div class="d-grid gap-2">
                <?php foreach ($scenario['options'] as $optIdx => $option): ?>
                <label class="btn btn-outline-primary text-start">
                    <input type="radio" name="q<?= $idx ?>" value="<?= $optIdx ?>" class="d-none" required>
                    <i class="bi bi-circle me-2"></i><?= htmlspecialchars($option['text']) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <button type="submit" class="btn btn-primary btn-lg w-100">
        <i class="bi bi-check-circle me-2"></i>Submit Jawaban
    </button>
</form>

<?php
$extraScripts = <<<HTML
<script>
document.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const name = this.name;
        document.querySelectorAll(`input[name="\${name}"]`).forEach(r => {
            r.closest('label').classList.remove('active');
            r.closest('label').querySelector('i').className = 'bi bi-circle me-2';
        });
        this.closest('label').classList.add('active');
        this.closest('label').querySelector('i').className = 'bi bi-check-circle-fill me-2';
    });
});
</script>
HTML;
require_once '../includes/footer.php';
?>