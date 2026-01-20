<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_files.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/auth_helper.php';

requireLogin();

$file_id = $_GET['id'] ?? null;

if (!$file_id || !is_numeric($file_id)) {
    header("Location: index.php");
    exit;
}

$file = getFileById($file_id);

if (!$file) {
    header("Location: index.php");
    exit;
}

if (!canEditCourse($file['course_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datei vom Server löschen
    $file_path = __DIR__ . $file['file_path'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    
    // Aus DB löschen
    if (deleteFile($file_id)) {
        header("Location: course-details.php?id=" . $file['course_id']);
        exit;
    } else {
        $error = 'Fehler beim Löschen der Datei.';
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datei löschen - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <h2 class="mb-4">Datei löschen</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-warning col-lg-6">
            <strong>Warnung!</strong> Du wirst folgende Datei permanent löschen:
            <br><br>
            <strong><?php echo htmlspecialchars($file['file_name']); ?></strong>
            <br><br>
            Diese Aktion kann <strong>NICHT</strong> rückgängig gemacht werden!
        </div>

        <form method="post" class="col-lg-6">
            <div class="mb-3">
                <button type="submit" class="btn btn-danger btn-lg">
                    Ja, Datei löschen
                </button>
                <a href="course-details.php?id=<?php echo $file['course_id']; ?>" class="btn btn-secondary btn-lg">
                    Abbrechen
                </a>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
