<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/redlining/index.php');
}

$user = getCurrentUser($pdo);
$contractId = (int) $_POST['contract_id'];
$original = trim($_POST['original_text']);
$revised = trim($_POST['revised_text']);
$translated = trim($_POST['translated_text'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM contracts WHERE contract_id = ? AND initiator_id = ?");
$stmt->execute([$contractId, $user['user_id']]);
if (!$stmt->fetch()) {
    flashMessage('Kontrak tidak ditemukan', 'danger');
    redirect(BASE_URL . '/redlining/index.php');
}

$stmt = $pdo->prepare("INSERT INTO redlining_logs (contract_id, user_id, original_text, revised_text, translated_text) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$contractId, $user['user_id'], $original, $revised, $translated ?: null]);

flashMessage('Revisi berhasil disimpan', 'success');
redirect(BASE_URL . '/redlining/translate.php?contract_id=' . $contractId);
?>