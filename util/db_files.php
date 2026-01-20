<?php
// util/db_files.php - erweitert

/**
 * Holt alle Dateien eines Kurses
 */
function getFilesByCourseId($course_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM files WHERE course_id = ? ORDER BY uploaded_at DESC");
    $stmt->execute([$course_id]);
    return $stmt->fetchAll();
}

/**
 * Holt eine einzelne Datei
 */
function getFileById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Speichert eine neue Datei in der DB
 */
function uploadFile($course_id, $file_name, $file_path, $file_size, $file_type) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO files (course_id, file_name, file_path, file_size, file_type) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$course_id, $file_name, $file_path, $file_size, $file_type]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Upload DB Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Löscht eine Datei
 */
function deleteFile($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log("Delete DB Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Format: Dateisize schön anzeigen (KB, MB)
 */
function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}

/**
 * Icon basierend auf MIME-Type
 */
function getFileIcon($mime_type) {
    $icons = [
        'application/pdf' => '📄',
        'application/msword' => '📝',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '📝',
        'image/jpeg' => '🖼️',
        'image/png' => '🖼️',
        'image/gif' => '🖼️'
    ];
    
    return $icons[$mime_type] ?? '📎';
}
?>
