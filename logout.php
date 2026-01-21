<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];
session_unset();
session_destroy();

setcookie(session_name(), '', time() - 3600, '/');
setcookie('remember_user', '', time() - 3600, '/');

header('Location: index.php');
exit;
?>
