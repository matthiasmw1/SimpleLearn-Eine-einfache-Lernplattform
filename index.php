<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/auth_helper.php';

$courses = getAllCourses();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container-fluid px-5">
        <div class="row align-items-center mb-4 mt-4">
            <div class="col">
                <h1>SimpleLearn</h1>
                <p class="lead">Deine Lernplattform für Online-Kurse</p>
            </div>
            <?php if (isLoggedIn()): ?>
                <div class="col-auto">
                    <a href="create-course.php" class="btn btn-success">+ Neuer Kurs</a>
                </div>
            <?php endif; ?>
        </div>

        <h2 class="mb-4">Verfügbare Kurse</h2>

        <?php if (empty($courses)): ?>
            <div class="alert alert-info">
                Es gibt noch keine Kurse. 
                <?php if (isLoggedIn()): ?>
                    <a href="create-course.php">Erstelle jetzt einen!</a>
                <?php else: ?>
                    <a href="login.php">Melde dich an um einen zu erstellen!</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($courses as $course): ?>
                    <div class="col-12 col-md-6 col-xl-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                <p class="card-text text-muted small">
                                    von <?php echo htmlspecialchars($course['creator_name']); ?>
                                </p>
                                <p class="card-text">
                                    <?php 
                                    $desc = htmlspecialchars($course['description'] ?? '');
                                    echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                                    ?>
                                </p>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        📝 <?php echo $course['task_count']; ?> Aufgaben | 
                                        📁 <?php echo $course['file_count']; ?> Dateien
                                    </small>
                                </div>
                                <a href="course-details.php?id=<?php echo $course['id']; ?>" 
                                   class="btn btn-primary btn-sm">
                                    Details ansehen
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
