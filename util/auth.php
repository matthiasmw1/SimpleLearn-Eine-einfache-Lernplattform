<?php
// util/auth.php
require_once __DIR__ . '/users.php';

function startAuthSession(): void
{
    session_start();

    // Wenn schon eingeloggt, nix tun
    if (!empty($_SESSION['user_id'])) {
        return;
    }

    // Wenn ein Remember-Me-Cookie existiert, versuchen wir auto-login
    if (!empty($_COOKIE['remember_user'])) {
        $userId = (int)$_COOKIE['remember_user'];
        $user = findUserById($userId);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
        } else {
            // Ungültigen Cookie aufräumen
            setcookie('remember_user', '', time() - 3600, '/');
        }
    }
}
