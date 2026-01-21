<?php

function startAuthSession(): void
{
    session_start();

    if (!empty($_SESSION['user_id'])) {
        return;
    }

    if (!empty($_COOKIE['remember_user'])) {
        $userId = (int)$_COOKIE['remember_user'];
        $user = findUserById($userId);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
        } else {
            setcookie('remember_user', '', time() - 3600, '/');
        }
    }
}
