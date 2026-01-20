<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/auth_helper.php';

requireLogin();

$course_id = $_GET['id'] ?? null;

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
$title = $course['title'];
$description = $course['description'];
$content = $course['content'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (!$title) {
        $errors[] = 'Kurstitel ist erforderlich.';
    }
    
    if (strlen($title) < 3) {
        $errors[] = 'Kurstitel muss mindestens 3 Zeichen lang sein.';
    }
    
    if (!$description) {
        $errors[] = 'Kursbeschreibung ist erforderlich.';
    }
    
    if (empty($errors)) {
        if (updateCourse($course_id, $title, $description, $content)) {
            $success = 'Kurs erfolgreich aktualisiert!';
            header("refresh:2;url=course-details.php?id=$course_id");
        } else {
            $errors[] = 'Fehler beim Aktualisieren des Kurses.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurs bearbeiten - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <h2 class="mb-4">Kurs bearbeiten</h2>

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
                <label class="form-label">Kurstitel</label>
                <input type="text" name="title" class="form-control" 
                       value="<?php echo htmlspecialchars($title); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Kursbeschreibung</label>
                <textarea name="description" class="form-control" rows="4" required>
<?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Kursinhalt</label>
                <textarea name="content" class="form-control" rows="10">
<?php echo htmlspecialchars($content); ?></textarea>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary btn-lg">Speichern</button>
                <a href="course-details.php?id=<?php echo $course_id; ?>" class="btn btn-secondary btn-lg">
                    Abbrechen
                </a>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
