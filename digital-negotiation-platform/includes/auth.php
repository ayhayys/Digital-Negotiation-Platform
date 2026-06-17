<?php
require_once __DIR__ . '/../config/database.php';
function checkAuth() { if (!isLoggedIn()) redirect('login.php'); }
?>