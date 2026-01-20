<?php

function getAllCourses() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM v_courses_with_creator ORDER BY created_at DESC");
    return $stmt->fetchAll();
}


function getCourseById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM v_courses_with_creator WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}


function getCoursesByUserId($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM v_courses_with_creator WHERE created_by = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}


function createCourse($title, $description, $content, $created_by) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO courses (title, description, content, created_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $content, $created_by]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        return false;
    }
}


function updateCourse($id, $title, $description, $content) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE courses SET title = ?, description = ?, content = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $content, $id]);
    } catch (PDOException $e) {
        return false;
    }
}

function deleteCourse($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        return false;
    }
}

function searchCourses($keyword) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM v_courses_with_creator WHERE MATCH(title, description) AGAINST(? IN BOOLEAN MODE) ORDER BY created_at DESC");
    $stmt->execute([$keyword]);
    return $stmt->fetchAll();
}
?>
