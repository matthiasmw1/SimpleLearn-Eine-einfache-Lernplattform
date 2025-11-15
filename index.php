<?php
session_start();

require __DIR__ . '/util/courses.php';
// index.php

// Später: hier könntest du DB-Verbindung einbinden (config.php, dbUtil.php)

// Fake-Daten für Kurse (statt Datenbank)
$courses = getAllCourses();

?>



<!DOCTYPE html>
<html lang="de">
<head>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/nav.php'; ?>

<main class="container-fluid px-5">
    <h2 class="mb-4">Alle Kurse</h2>

    <?php if (empty($_SESSION['user_id'])): ?>

        <div class="alert alert-info">
            Um die Kurse zu sehen, musst du eingeloggt sein.
            <a href="login.php" class="alert-link">Zum Login</a>
            oder
            <a href="register.php" class="alert-link">jetzt registrieren</a>.
        </div>

    <?php else: ?>

        <div class="row">
            <?php foreach ($courses as $course): ?>
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
