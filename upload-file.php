<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/db_files.php';
require_once __DIR__ . '/util/auth_helper.php';
require_once __DIR__ . '/util/upload_handler.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_FILES['file'])) {
        $errors[] = 'Bitte wähle eine Datei aus.';
    } else {
        // Validierung und Speichern mit neuem Handler
        $upload_result = UploadHandler::save($_FILES['file'], 'course_material', $course_id . '/');
        
        if ($upload_result['success']) {
            // In DB speichern
            $file_id = uploadFile(
                $course_id,
                $upload_result['original_name'],
                $upload_result['public_path'],
                $upload_result['size'],
                $upload_result['mime_type']
            );
            
            if ($file_id) {
                $success = '✅ Datei erfolgreich hochgeladen!';
                header("refresh:2;url=course-details.php?id=$course_id");
            } else {
                $errors[] = 'Fehler beim Speichern in der Datenbank.';
                UploadHandler::delete($upload_result['public_path'], 'course_material');
            }
        } else {
            $errors[] = $upload_result['error'];
        }
    }
}

$allowed_extensions = UploadHandler::getAllowedExtensions('course_material');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datei hochladen - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <h2 class="mb-4">📤 Datei hochladen für: <?php echo htmlspecialchars($course['title']); ?></h2>

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
                    <div>❌ <?php echo htmlspecialchars($e); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card col-lg-8">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label"><strong>Datei auswählen</strong></label>
                        <input type="file" name="file" class="form-control" required
                               accept="<?php echo $allowed_extensions; ?>">
                        <small class="form-text text-muted d-block mt-2">
                            <strong>Erlaubte Dateitypen:</strong> PDF, DOCX, DOC, JPG, PNG, GIF<br>
                            <strong>Maximum Dateigröße:</strong> 5 MB
                        </small>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            📤 Datei hochladen
                        </button>
                        <a href="course-details.php?id=<?php echo $course_id; ?>" class="btn btn-secondary btn-lg">
                            Abbrechen
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-info mt-4 col-lg-8">
            <strong>💡 Hinweis:</strong> Lade hier Lernmaterialien (PDFs, Dokumente, Bilder) hoch, die deine Kursteilnehmer herunterladen können.
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
