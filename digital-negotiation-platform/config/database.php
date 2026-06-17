<?php
session_start();

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$project_folder = basename(dirname(__DIR__));
define('BASE_URL', $protocol . $host . '/' . $project_folder);

$host_db = "localhost";
$dbname = "db_digital_negotiation";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host_db;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

function redirect($url) {
    if (strpos($url, 'http') !== 0) $url = BASE_URL . '/' . ltrim($url, '/');
    header("Location: " . $url); exit();
}

function isLoggedIn() { return isset($_SESSION['user_id']); }
function requireLogin() { if (!isLoggedIn()) redirect('login.php'); }

function getCurrentUser($pdo) {
    if (!isLoggedIn()) return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function flashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = ['message' => $_SESSION['flash_message'], 'type' => $_SESSION['flash_type'] ?? 'info'];
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return $msg;
    }
    return null;
}

function formatDate($date) { return date('d M Y H:i', strtotime($date)); }

function getStatusBadge($status) {
    $badges = [
        'draft' => '<span class="badge bg-secondary">Draft</span>',
        'in_review' => '<span class="badge bg-warning text-dark">In Review</span>',
        'signed' => '<span class="badge bg-success">Signed</span>',
        'cancelled' => '<span class="badge bg-danger">Cancelled</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-light">Unknown</span>';
}
?>