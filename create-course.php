<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/auth_helper.php';

requireLogin();

$errors = [];
$title = '';
$description = '';
$content = '';

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
    
    if (strlen($title) > 150) {
        $errors[] = 'Kurstitel darf maximal 150 Zeichen lang sein.';
    }
    
    if (!$description) {
        $errors[] = 'Kursbeschreibung ist erforderlich.';
    }
    
    if (strlen($description) < 10) {
        $errors[] = 'Kursbeschreibung muss mindestens 10 Zeichen lang sein.';
    }
    
    if (empty($errors)) {
        $course_id = createCourse($title, $description, $content, getCurrentUserId());
        
        if ($course_id) {
            header("Location: course-details.php?id=$course_id");
            exit;
        } else {
            $errors[] = 'Fehler beim Erstellen des Kurses. Bitte versuche es später erneut.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuer Kurs - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <h2 class="mb-4">Neuer Kurs</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <div><?php echo htmlspecialchars($e); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="col-lg-8">
            <div class="mb-3">
                <label class="form-label">Kurstitel <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" 
                       value="<?php echo htmlspecialchars($title); ?>" 
                       maxlength="150" required>
                <small class="form-text text-muted">Max. 150 Zeichen</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Kursbeschreibung <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="4" 
                          maxlength="1000" required><?php echo htmlspecialchars($description); ?></textarea>
                <small class="form-text text-muted">Max. 1000 Zeichen</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Kursinhalt</label>
                <textarea name="content" class="form-control" rows="10" 
                          placeholder="Gib hier den Inhalt deines Kurses ein (HTML wird nicht interpretiert)"><?php echo htmlspecialchars($content); ?></textarea>
                <small class="form-text text-muted">Optional - kann später bearbeitet werden</small>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary btn-lg">Kurs erstellen</button>
                <a href="index.php" class="btn btn-secondary btn-lg">Abbrechen</a>
            </div>
        </form>

        <div class="alert alert-info mt-4 col-lg-8">
            <strong>Hinweis:</strong> Nach dem Erstellen kannst du Aufgaben und Dateien hinzufuegen.
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
