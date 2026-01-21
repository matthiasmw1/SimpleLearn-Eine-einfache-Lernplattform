<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/db_files.php';
require_once __DIR__ . '/util/auth_helper.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Bitte waehle eine Datei aus.';
    } else {
        $file = $_FILES['file'];

        if ($file['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Datei zu gross (max. 5 MB).';
        }

        $allowed_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'image/gif'
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Dateityp nicht erlaubt. Nur PDF, DOCX, DOC, JPG, PNG, GIF.';
        }

        if (empty($errors)) {
            $upload_dir = __DIR__ . '/uploads/courses/' . $course_id . '/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $original_name = basename($file['name']);
            $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $new_filename = uniqid() . '_' . time() . '.' . $ext;
            $file_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $file_id = uploadFile(
                    $course_id,
                    $original_name,
                    '/uploads/courses/' . $course_id . '/' . $new_filename,
                    $file['size'],
                    $file_type
                );

                if ($file_id) {
                    header("Location: course-details.php?id=$course_id");
                    exit;
                } else {
                    $errors[] = 'Fehler beim Speichern in der Datenbank.';
                    unlink($file_path);
                }
            } else {
                $errors[] = 'Fehler beim Hochladen der Datei.';
            }
        }
    }
}
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
        <h2 class="mb-4">Datei hochladen fuer: <?php echo htmlspecialchars($course['title']); ?></h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card col-lg-8">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label"><strong>Datei auswaehlen</strong></label>
                        <input type="file" name="file" class="form-control" required
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                        <small class="form-text text-muted d-block mt-2">
                            <strong>Erlaubte Dateitypen:</strong> PDF, DOCX, DOC, JPG, PNG, GIF<br>
                            <strong>Maximum:</strong> 5 MB
                        </small>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Datei hochladen
                        </button>
                        <a href="course-details.php?id=<?php echo $course_id; ?>" class="btn btn-secondary btn-lg">
                            Abbrechen
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
