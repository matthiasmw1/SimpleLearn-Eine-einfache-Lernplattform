<?php


function getTasksByCourseId($course_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE course_id = ? ORDER BY due_date ASC");
    $stmt->execute([$course_id]);
    return $stmt->fetchAll();
}


function getTaskById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}


function createTask($course_id, $title, $description, $due_date) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (course_id, title, description, due_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$course_id, $title, $description, $due_date]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        return false;
    }
}


function updateTask($id, $title, $description, $due_date, $status) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ?, status = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $due_date, $status, $id]);
    } catch (PDOException $e) {
        return false;
    }
}


function deleteTask($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        return false;
    }
}
?>
