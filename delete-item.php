<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/auth_helper.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/db_tasks.php';
require_once __DIR__ . '/util/db_files.php';

requireLogin();

$errors = [];
$pdo = $GLOBALS['pdo'];

// DELETE Course (nur Owner oder Admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_course') {
    $course_id = $_POST['course_id'] ?? null;
    
    if ($course_id && is_numeric($course_id)) {
        $course = getCourseById($course_id);
        
        if ($course && (canEditCourse($course_id) || isAdmin())) {
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            if ($stmt->execute([$course_id])) {
                header("Location: my-courses.php");
                exit;
            } else {
                $errors[] = 'Fehler beim Löschen.';
            }
        } else {
            $errors[] = 'Keine Berechtigung.';
        }
    }
}

// DELETE Task (nur Owner oder Admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_task') {
    $task_id = $_POST['task_id'] ?? null;
    
    if ($task_id && is_numeric($task_id)) {
        $stmt = $pdo->prepare("SELECT course_id FROM tasks WHERE id = ?");
        $stmt->execute([$task_id]);
        $task = $stmt->fetch();
        
        if ($task && canEditCourse($task['course_id'])) {
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
            if ($stmt->execute([$task_id])) {
                header("Location: " . $_POST['redirect_url']);
                exit;
            } else {
                $errors[] = 'Fehler beim Löschen.';
            }
        } else {
            $errors[] = 'Keine Berechtigung.';
        }
    }
}

// DELETE File (nur Owner oder Admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_file') {
    $file_id = $_POST['file_id'] ?? null;
    
    if ($file_id && is_numeric($file_id)) {
        $stmt = $pdo->prepare("SELECT course_id, file_path FROM files WHERE id = ?");
        $stmt->execute([$file_id]);
        $file = $stmt->fetch();
        
        if ($file && canEditCourse($file['course_id'])) {
            // Datei vom Server löschen
            $file_path = __DIR__ . $file['file_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Aus DB löschen
            $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
            if ($stmt->execute([$file_id])) {
                header("Location: " . $_POST['redirect_url']);
                exit;
            } else {
                $errors[] = 'Fehler beim Löschen.';
            }
        } else {
            $errors[] = 'Keine Berechtigung.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fehler</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
