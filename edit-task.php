<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_tasks.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/auth_helper.php';

requireLogin();

$task_id = $_GET['id'] ?? null;

if (!$task_id || !is_numeric($task_id)) {
    header("Location: index.php");
    exit;
}

$task = getTaskById($task_id);

if (!$task) {
    header("Location: index.php");
    exit;
}

if (!canEditCourse($task['course_id'])) {
    header("Location: index.php");
    exit;
}

$course = getCourseById($task['course_id']);

$errors = [];
$success = '';
$title = $task['title'];
$description = $task['description'];
$due_date = $task['due_date'];
$status = $task['status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? '';
    $status = $_POST['status'] ?? 'pending';
    
    if (!$title) {
        $errors[] = 'Aufgabentitel ist erforderlich.';
    }
    
    if (!$description) {
        $errors[] = 'Aufgabenbeschreibung ist erforderlich.';
    }
    
    if (!$due_date) {
        $errors[] = 'Fälligkeitsdatum ist erforderlich.';
    }
    
    if (empty($errors)) {
        if (updateTask($task_id, $title, $description, $due_date, $status)) {
            $success = 'Aufgabe erfolgreich aktualisiert!';
            header("refresh:2;url=course-details.php?id=" . $task['course_id']);
        } else {
            $errors[] = 'Fehler beim Aktualisieren der Aufgabe.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aufgabe bearbeiten - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <h2 class="mb-4">Aufgabe bearbeiten</h2>

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
                <label class="form-label">Aufgabentitel</label>
                <input type="text" name="title" class="form-control" 
                       value="<?php echo htmlspecialchars($title); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Aufgabenbeschreibung</label>
                <textarea name="description" class="form-control" rows="6" required>
<?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Fälligkeitsdatum</label>
                <input type="date" name="due_date" class="form-control" 
                       value="<?php echo htmlspecialchars($due_date); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Ausstehend</option>
                    <option value="submitted" <?php echo $status === 'submitted' ? 'selected' : ''; ?>>Eingereicht</option>
                    <option value="graded" <?php echo $status === 'graded' ? 'selected' : ''; ?>>Bewertet</option>
                </select>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary btn-lg">Speichern</button>
                <a href="course-details.php?id=<?php echo $task['course_id']; ?>" class="btn btn-secondary btn-lg">
                    Abbrechen
                </a>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
