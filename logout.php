<?php
require_once __DIR__ . '/config.php';

// Session starten falls nicht bereits gestartet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session clearen
$_SESSION = [];
session_unset();
session_destroy();

// Cookies löschen
setcookie(session_name(), '', time() - 3600, '/');
setcookie('remember_user', '', time() - 3600, '/');

// Zur Startseite leiten
header('Location: index.php');
exit;
?>
