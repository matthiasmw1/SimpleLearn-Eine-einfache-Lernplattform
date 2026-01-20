<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/db_tasks.php';
require_once __DIR__ . '/util/auth_helper.php';

// Nur eingeloggte User
requireLogin();

$course_id = $_GET['course_id'] ?? null;

if (!$course_id || !is_numeric($course_id)) {
    header("Location: index.php");
    exit;
}

$course = getCourseById($course_id);

if (!$course || !canEditCourse($course_id)) {
    header("Location: index.php");
    exit;
}

$errors = [];
$success = '';
$title = '';
$description = '';
$due_date = date('Y-m-d', strtotime('+7 days'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? '';
    
    // Validierung
    if (!$title) {
        $errors[] = 'Aufgabentitel ist erforderlich.';
    }
    
    if (strlen($title) < 3) {
        $errors[] = 'Aufgabentitel muss mindestens 3 Zeichen lang sein.';
    }
    
    if (!$description) {
        $errors[] = 'Aufgabenbeschreibung ist erforderlich.';
    }
    
    if (!$due_date) {
        $errors[] = 'Fälligkeitsdatum ist erforderlich.';
    } else {
        $due_timestamp = strtotime($due_date);
        if ($due_timestamp < time()) {
            $errors[] = 'Fälligkeitsdatum kann nicht in der Vergangenheit liegen.';
        }
    }
    
    // Keine Fehler? Speichern!
    if (empty($errors)) {
        $task_id = createTask($course_id, $title, $description, $due_date);
        
        if ($task_id) {
            $success = 'Aufgabe erfolgreich erstellt!';
            header("refresh:2;url=course-details.php?id=$course_id");
            $title = '';
            $description = '';
            $due_date = date('Y-m-d', strtotime('+7 days'));
        } else {
            $errors[] = 'Fehler beim Erstellen der Aufgabe. Bitte versuche es später erneut.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neue Aufgabe - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <h2 class="mb-4">Neue Aufgabe für: <?php echo htmlspecialchars($course['title']); ?></h2>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
                <br>
                <small>Du wirst in Kürze weitergeleitet...</small>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <div><?php echo htmlspecialchars($e); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="col-lg-8">
            <div class="mb-3">
                <label class="form-label">Aufgabentitel <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" 
                       value="<?php echo htmlspecialchars($title); ?>" 
                       maxlength="200" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Aufgabenbeschreibung <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="6" required>
<?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Fälligkeitsdatum <span class="text-danger">*</span></label>
                <input type="date" name="due_date" class="form-control" 
                       value="<?php echo htmlspecialchars($due_date); ?>" required>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary btn-lg">Aufgabe erstellen</button>
                <a href="course-details.php?id=<?php echo $course_id; ?>" class="btn btn-secondary btn-lg">
                    Abbrechen
                </a>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
