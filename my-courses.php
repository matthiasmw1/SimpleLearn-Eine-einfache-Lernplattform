<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/util/courses.php';

$userId = (int)$_SESSION['user_id'];
$myCourses = getCoursesByOwner($userId);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meine Kurse - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/nav.php'; ?>

<main class="container mt-4">
    <h2 class="mb-4">Meine Kurse</h2>

    <?php if (empty($myCourses)): ?>
        <div class="alert alert-info">
            Du hast noch keine eigenen Kurse angelegt.
            <a href="create-course.php" class="alert-link">Jetzt einen Kurs erstellen</a>.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($myCourses as $course): ?>
                <div class="col-12 col-md-6 col-xl-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($course['title']); ?>
                            </h5>
                            <p class="card-text">
                                <?php echo htmlspecialchars($course['description']); ?>
                            </p>

                            <a href="course-details.php?id=<?php echo $course['id']; ?>"
                               class="btn btn-sm btn-outline-primary me-2">
                                Details
                            </a>

                            <!-- Bearbeiten kommt später -->
                            <a href="delete-course.php?id=<?php echo $course['id']; ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Diesen Kurs wirklich löschen? (Demo)');">
                                Löschen
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
