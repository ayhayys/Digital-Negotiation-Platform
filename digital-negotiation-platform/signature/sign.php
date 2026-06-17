<?php
$pageTitle = 'Tanda Tangani';
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

$user = getCurrentUser($pdo);
$uid = $user['user_id'];
$contractId = (int) ($_GET['contract_id'] ?? 0);

if ($contractId <= 0) {
    flashMessage('Kontrak tidak valid', 'danger');
    header("Location: " . BASE_URL . "/signature/index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM contracts WHERE contract_id = ?");
$stmt->execute([$contractId]);
$contract = $stmt->fetch();

if (!$contract) {
    flashMessage('Kontrak tidak ditemukan', 'danger');
    header("Location: " . BASE_URL . "/signature/index.php");
    exit();
}

if ($contract['initiator_id'] != $uid && $contract['client_id'] != $uid) {
    flashMessage('Akses ditolak', 'danger');
    header("Location: " . BASE_URL . "/signature/index.php");
    exit();
}

if ($contract['status'] === 'signed') {
    flashMessage('Kontrak sudah ditandatangani', 'warning');
    header("Location: " . BASE_URL . "/signature/index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM signatures WHERE contract_id = ? AND user_id = ?");
$stmt->execute([$contractId, $uid]);
$alreadySigned = $stmt->fetchColumn() > 0;

if ($alreadySigned) {
    flashMessage('Anda sudah menandatangani kontrak ini', 'warning');
    header("Location: " . BASE_URL . "/signature/index.php");
    exit();
}

$step = isset($_GET['step']) ? $_GET['step'] : '1';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'send_otp') {
        $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $_SESSION['otp_' . $contractId] = $otpCode;
        $_SESSION['otp_time_' . $contractId] = time();
        $step = '2';
        
    } elseif ($_POST['action'] === 'verify_otp') {
        $inputOtp = $_POST['otp'] ?? '';
        $savedOtp = $_SESSION['otp_' . $contractId] ?? '';
        $otpTime = $_SESSION['otp_time_' . $contractId] ?? 0;
        
        if (empty($savedOtp)) {
            $error = 'OTP tidak ditemukan. Silakan minta OTP baru.';
        } elseif (time() - $otpTime > 300) {
            $error = 'OTP sudah kedaluwarsa (5 menit). Silakan minta OTP baru.';
            unset($_SESSION['otp_' . $contractId], $_SESSION['otp_time_' . $contractId]);
        } elseif ($inputOtp !== $savedOtp) {
            $error = 'Kode OTP salah. Silakan coba lagi.';
        } else {
            try {
                $hashData = $contractId . $uid . time() . $_SERVER['REMOTE_ADDR'];
                $hash = hash('sha256', $hashData);
                $jurisdiction = $_POST['jurisdiction'] ?? 'ID-IDN';
                
                $stmt = $pdo->prepare("INSERT INTO signatures (contract_id, user_id, otp_code, jurisdiction_code, certificate_hash) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$contractId, $uid, $inputOtp, $jurisdiction, $hash]);
                
                $stmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) as signer_count FROM signatures WHERE contract_id = ?");
                $stmt->execute([$contractId]);
                $signerCount = $stmt->fetchColumn();
                
                if ($signerCount >= 2) {
                    $stmt = $pdo->prepare("UPDATE contracts SET status = 'signed' WHERE contract_id = ?");
                    $stmt->execute([$contractId]);
                }
                
                unset($_SESSION['otp_' . $contractId], $_SESSION['otp_time_' . $contractId]);
                
                $_SESSION['flash_message'] = 'Kontrak berhasil ditandatangani!';
                $_SESSION['flash_type'] = 'success';
                
                header("Location: " . BASE_URL . "/signature/index.php");
                exit();
                
            } catch (PDOException $e) {
                $error = 'Terjadi kesalahan: ' . $e->getMessage();
            }
        }
    }
}

require_once '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-1">Kontrak: <?= htmlspecialchars($contract['title']) ?></h6>
                <p class="text-muted small mb-0">File: <?= basename($contract['file_path']) ?></p>
                <p class="text-muted small mb-0">Status: <?= getStatusBadge($contract['status']) ?></p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-3"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($step === '1'): ?>
                    <i class="bi bi-shield-lock fs-1 text-primary mb-3"></i>
                    <h5>Verifikasi Identitas</h5>
                    <p class="text-muted">Klik tombol di bawah untuk menerima kode OTP</p>
                    <form method="POST">
                        <input type="hidden" name="action" value="send_otp">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-send me-2"></i>Kirim OTP
                        </button>
                    </form>
                    
                <?php elseif ($step === '2'): ?>
                    <i class="bi bi-key fs-1 text-warning mb-3"></i>
                    <h5>Masukkan Kode OTP</h5>
                    <p class="text-muted mb-4">Kode OTP Anda (Simulasi): <br><strong class="text-primary fs-3"><?= $_SESSION['otp_' . $contractId] ?? '******' ?></strong></p>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="verify_otp">
                        <div class="mb-3">
                            <label class="form-label text-start d-block">Kode OTP (6 digit)</label>
                            <input type="text" name="otp" class="form-control form-control-lg text-center" maxlength="6" pattern="[0-9]{6}" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-start d-block">Yurisdiksi Hukum</label>
                            <select name="jurisdiction" class="form-select" required>
                                <option value="ID-IDN">Indonesia (ID-IDN)</option>
                                <option value="US-NY">United States - New York (US-NY)</option>
                                <option value="SG-SGP">Singapore (SG-SGP)</option>
                                <option value="JP-JPN">Japan (JP-JPN)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            <i class="bi bi-pen me-2"></i>Tanda Tangani & Kunci Dokumen
                        </button>
                    </form>
                    <div class="mt-3">
                        <a href="?contract_id=<?= $contractId ?>&step=1" class="btn btn-link">Kirim Ulang OTP</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>