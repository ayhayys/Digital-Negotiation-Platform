<aside class="sidebar" id="sidebar">
    <div class="sidebar-header p-3 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <div class="brand-icon"><i class="bi bi-file-earmark-text"></i></div>
            <div>
                <h6 class="mb-0 text-white">Negotiation Platform</h6>
                <small class="text-white-50">Digital Platform</small>
            </div>
        </div>
    </div>
    <nav class="sidebar-nav p-3">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            <li class="nav-section">Kontrak</li>
            <li class="nav-item"><a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/contracts/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/contracts/index.php"><i class="bi bi-folder2-open me-2"></i>Management Contracts</a></li>
            <li class="nav-item"><a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/redlining/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/redlining/index.php"><i class="bi bi-pencil-square me-2"></i>Smart Redlining</a></li>
            <li class="nav-section">Analitik</li>
            <li class="nav-item"><a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/analytics/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/analytics/non_verbal.php"><i class="bi bi-camera-video me-2"></i>Non-Verbal Analytics</a></li>
            <li class="nav-section">Finalisasi</li>
            <li class="nav-item"><a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/signature/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/signature/index.php"><i class="bi bi-pen me-2"></i>E-Signature</a></li>
            <li class="nav-item"><a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/training/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/training/index.php"><i class="bi bi-mortarboard me-2"></i>Culture Training</a></li>
        </ul>
    </nav>
</aside>