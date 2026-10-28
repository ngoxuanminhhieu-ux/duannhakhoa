<?php
// web/admin/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAdminAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        $redirect = !empty($_SERVER['REQUEST_URI']) ? '?redirect=' . urlencode($_SERVER['REQUEST_URI']) . '&role=admin' : '?role=admin';
        header("Location: ../login.php" . $redirect);
        exit;
    }
}
