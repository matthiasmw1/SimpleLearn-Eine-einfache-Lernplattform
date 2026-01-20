<?php

function userExists($username, $email) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    return $stmt->fetch() !== false;
}

function createUser($username, $email, $password) {
    global $pdo;
    
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password_hash]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function findUserByUsernameOrEmail($usernameOrEmail) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, username, email, password_hash, role FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
    
    return $stmt->fetch();
}

function getUserById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    
    return $stmt->fetch();
}

function deleteUser($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}
?>
