<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/auth_helper.php';

requireLogin();

$courses = getCoursesByUserId(getCurrentUserId());
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

    <main class="container-fluid px-5">
        <div class="row align-items-center mb-4 mt-4">
            <div class="col">
                <h1>Meine Kurse</h1>
                <p class="lead">Deine erstellten Kurse</p>
            </div>
            <div class="col-auto">
                <a href="create-course.php" class="btn btn-success btn-lg">
                    + Neuer Kurs
                </a>
            </div>
        </div>

        <?php if (empty($courses)): ?>
            <div class="alert alert-info">
                Du hast noch keine Kurse erstellt.
                <a href="create-course.php">Erstelle jetzt deinen ersten Kurs!</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($courses as $course): ?>
                    <div class="col-12 col-md-6 col-xl-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                <p class="card-text text-muted small">
                                    Erstellt: <?php echo date('d.m.Y', strtotime($course['created_at'])); ?>
                                </p>
                                <p class="card-text">
                                    <?php 
                                    $desc = htmlspecialchars($course['description'] ?? '');
                                    echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                                    ?>
                                </p>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        📝 <?php echo $course['task_count']; ?> Aufgaben
                                    </small>
                                </div>
                                <div class="btn-group w-100" role="group">
                                    <a href="course-details.php?id=<?php echo $course['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        👁️ Ansehen
                                    </a>
                                    <a href="edit-course.php?id=<?php echo $course['id']; ?>" 
                                       class="btn btn-sm btn-warning">
                                        ✏️ Bearbeiten
                                    </a>
                                    <a href="delete-course.php?id=<?php echo $course['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Wirklich löschen?')">
                                        🗑️ Löschen
                                    </a>
                                </div>
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
