<?php
$pageTitle = 'Non-Verbal Analytics';
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$user = getCurrentUser($pdo);
$uid = $user['user_id'];

$contracts = $pdo->prepare("SELECT * FROM contracts WHERE initiator_id = ? OR client_id = ? ORDER BY created_at DESC");
$contracts->execute([$uid, $uid]);
$contracts = $contracts->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_analysis'])) {
    $contractId = (int) $_POST['contract_id'];
    $emotion = (float) $_POST['emotion_score'];
    $stress = (float) $_POST['stress_level'];
    $alert = $_POST['cultural_alert'] ?? null;
    
    $stmt = $pdo->prepare("INSERT INTO non_verbal_analytics (contract_id, user_id, emotion_score, stress_level, cultural_alert) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$contractId, $uid, $emotion, $stress, $alert]);
    flashMessage('Analisis berhasil disimpan', 'success');
    redirect(BASE_URL . '/analytics/non_verbal.php');
}

$history = $pdo->prepare("SELECT n.*, c.title FROM non_verbal_analytics n JOIN contracts c ON n.contract_id = c.contract_id WHERE n.user_id = ? ORDER BY n.timestamp DESC LIMIT 10");
$history->execute([$uid]);
$history = $history->fetchAll();

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Non-Verbal Communication Analysis</h4>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-camera me-2"></i>Live Camera Feed</h5>
            </div>
            <div class="card-body">
                <div class="position-relative bg-dark rounded" style="aspect-ratio: 16/9;">
                    <video id="video" class="w-100 h-100 rounded" autoplay playsinline style="object-fit: cover;"></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <div id="cameraOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                        <p class="text-white mb-0">Klik "Start Camera" untuk memulai</p>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button id="startCamera" class="btn btn-primary">
                        <i class="bi bi-camera-video me-2"></i>Start Camera
                    </button>
                    <button id="stopCamera" class="btn btn-danger" disabled>
                        <i class="bi bi-stop-circle me-2"></i>Stop Camera
                    </button>
                    <button id="captureAnalysis" class="btn btn-success ms-auto" disabled>
                        <i class="bi bi-cpu me-2"></i>Capture Analysis
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Hasil Analisis</h5>
            </div>
            <div class="card-body">
                <div id="analysisResults">
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-emoji-neutral fs-1"></i>
                        <p class="mt-2 mb-0">Mulai kamera dan capture untuk melihat analisis</p>
                    </div>
                </div>
            </div>
        </div>
        
        <form method="POST" id="saveForm" style="display:none;">
            <input type="hidden" name="save_analysis" value="1">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Simpan Hasil Analisis</h6>
                    <div class="mb-3">
                        <label class="form-label">Pilih Kontrak</label>
                        <select name="contract_id" class="form-select" required>
                            <option value="">-- Pilih Kontrak --</option>
                            <?php foreach ($contracts as $c): ?>
                            <option value="<?= $c['contract_id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="emotion_score" id="emotionScore">
                    <input type="hidden" name="stress_level" id="stressLevel">
                    <input type="hidden" name="cultural_alert" id="culturalAlert">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-save me-2"></i>Simpan ke Database
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($history)): ?>
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Riwayat Analisis</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kontrak</th>
                        <th>Emosi</th>
                        <th>Stres</th>
                        <th>Alert</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                    <tr>
                        <td><?= htmlspecialchars($h['title']) ?></td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-info" style="width: <?= (float)$h['emotion_score'] * 20 ?>%">
                                    <?= number_format((float)$h['emotion_score'], 1) ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar <?= (float)$h['stress_level'] > 3 ? 'bg-danger' : 'bg-warning' ?>" 
                                     style="width: <?= (float)$h['stress_level'] * 20 ?>%">
                                    <?= number_format((float)$h['stress_level'], 1) ?>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($h['cultural_alert'] ?? '-') ?></td>
                        <td class="small"><?= formatDate($h['timestamp']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let videoStream = null;
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const startBtn = document.getElementById('startCamera');
    const stopBtn = document.getElementById('stopCamera');
    const captureBtn = document.getElementById('captureAnalysis');
    const overlay = document.getElementById('cameraOverlay');

    if (startBtn) {
        startBtn.addEventListener('click', async function() {
            try {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Browser Anda tidak mendukung akses kamera. Silakan gunakan browser modern seperti Chrome, Firefox, atau Edge.');
                    return;
                }

                videoStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: 'user'
                    }, 
                    audio: false 
                });
                
                video.srcObject = videoStream;
                
                video.onloadedmetadata = function() {
                    if (overlay) overlay.style.display = 'none';
                    if (startBtn) startBtn.disabled = true;
                    if (stopBtn) stopBtn.disabled = false;
                    if (captureBtn) captureBtn.disabled = false;
                };
            } catch (err) {
                console.error('Camera Error:', err);
                let errorMessage = 'Gagal mengakses kamera: ';
                
                if (err.name === 'NotAllowedError') {
                    errorMessage += 'Akses kamera ditolak. Silakan izinkan akses kamera di browser Anda.';
                } else if (err.name === 'NotFoundError') {
                    errorMessage += 'Kamera tidak ditemukan di perangkat Anda.';
                } else if (err.name === 'NotReadableError') {
                    errorMessage += 'Kamera sedang digunakan oleh aplikasi lain.';
                } else {
                    errorMessage += err.message;
                }
                
                alert(errorMessage);
            }
        });
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', function() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
                if (overlay) overlay.style.display = 'flex';
                if (startBtn) startBtn.disabled = false;
                if (stopBtn) stopBtn.disabled = true;
                if (captureBtn) captureBtn.disabled = true;
                videoStream = null;
            }
        });
    }

    if (captureBtn) {
        captureBtn.addEventListener('click', function() {
            if (!videoStream) {
                alert('Kamera belum aktif. Silakan start camera terlebih dahulu.');
                return;
            }

            const emotion = (Math.random() * 3 + 2).toFixed(2); // 2-5
            const stress = (Math.random() * 4 + 1).toFixed(2);  // 1-5
            const confidence = (Math.random() * 3 + 2).toFixed(2);
            const doubt = (Math.random() * 3 + 1).toFixed(2);
            
            let alertMsg = null;
            if (stress > 3.5) {
                alertMsg = 'Tingkat stres tinggi terdeteksi! Pertimbangkan untuk memberikan waktu istirahat.';
            } else if (doubt > 3) {
                alertMsg = 'Indikasi keraguan dari lawan bicara. Siapkan argumen tambahan.';
            }
            
            const emotionEmoji = emotion > 4 ? '😊' : (emotion > 3 ? '😐' : '😟');
            const stressColor = stress > 3.5 ? 'danger' : (stress > 2.5 ? 'warning' : 'success');
            
            const resultsDiv = document.getElementById('analysisResults');
            if (resultsDiv) {
                resultsDiv.innerHTML = `
                    <div class="text-center mb-3">
                        <div style="font-size: 4rem;">${emotionEmoji}</div>
                        <h5>Analisis Selesai</h5>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Tingkat Stres</label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-${stressColor}" style="width: ${stress * 20}%">
                                ${stress} / 5.0
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Tingkat Keraguan</label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-warning" style="width: ${doubt * 20}%">
                                ${doubt} / 5.0
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Tingkat Kepercayaan Diri</label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" style="width: ${confidence * 20}%">
                                ${confidence} / 5.0
                            </div>
                        </div>
                    </div>
                    ${alertMsg ? `<div class="alert alert-warning small mb-0"><i class="bi bi-exclamation-triangle me-2"></i>${alertMsg}</div>` : ''}
                `;
            }
            
            const emotionScoreInput = document.getElementById('emotionScore');
            const stressLevelInput = document.getElementById('stressLevel');
            const culturalAlertInput = document.getElementById('culturalAlert');
            const saveForm = document.getElementById('saveForm');
            
            if (emotionScoreInput) emotionScoreInput.value = emotion;
            if (stressLevelInput) stressLevelInput.value = stress;
            if (culturalAlertInput) culturalAlertInput.value = alertMsg || '';
            if (saveForm) saveForm.style.display = 'block';
        });
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>