<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/auth_helper.php';
require_once __DIR__ . '/util/db_courses.php';

requireLogin();

if (!isAdmin()) {
    header("Location: index.php");
    exit;
}

$course_id = $_GET['id'] ?? null;

if (!$course_id || !is_numeric($course_id)) {
    header("Location: admin.php");
    exit;
}

$course = getCourseById($course_id);

if (!$course) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (deleteCourse($course_id)) {
        header("Location: admin.php?tab=courses");
        exit;
    } else {
        $error = 'Fehler beim Löschen des Kurses.';
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurs löschen - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <h2 class="mb-4">Kurs löschen (Admin)</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-warning col-lg-6">
            <strong>Warnung!</strong> Du wirst folgenden Kurs permanent löschen:
            <br><br>
            <strong><?php echo htmlspecialchars($course['title']); ?></strong>
            <br><br>
            Diese Aktion kann <strong>NICHT</strong> rückgängig gemacht werden!
        </div>

        <form method="post" class="col-lg-6">
            <div class="mb-3">
                <button type="submit" class="btn btn-danger btn-lg">
                    Ja, Kurs löschen
                </button>
                <a href="admin.php?tab=courses" class="btn btn-secondary btn-lg">
                    Abbrechen
                </a>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
