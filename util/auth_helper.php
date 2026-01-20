<?php


function login($user_id, $username, $role) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['logged_in'] = true;
}


function logout() {
    session_destroy();
    $_SESSION = [];
}


function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}


function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}


function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}


function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}


function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}


function canEditCourse($course_id) {
    global $pdo;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    if (isAdmin()) {
        return true;
    }
    
    $stmt = $pdo->prepare("SELECT created_by FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();
    
    return $course && $course['created_by'] == getCurrentUserId();
}


function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}


function requireAdmin() {
    if (!isAdmin()) {
        header("Location: index.php");
        exit;
    }
}
?>
