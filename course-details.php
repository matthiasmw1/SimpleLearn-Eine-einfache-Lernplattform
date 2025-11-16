<?php
require __DIR__ . '/util/auth.php';
startAuthSession();

// Nur eingeloggte Nutzer dürfen Kursdetails sehen
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fake-Kurse – gleich wie in index.php
$courses = [
    [
        'id' => 1,
        'title' => 'Einführung in PHP',
        'description' => 'Lerne die Grundlagen von PHP für Webentwicklung.',
        'content' => 'Hier könnten später Lektionen, Dateien oder Videos zum Kurs "Einführung in PHP" stehen.'
    ],
    [
        'id' => 2,
        'title' => 'HTML & CSS Basics',
        'description' => 'Erstelle schöne Webseiten mit HTML und CSS.',
        'content' => 'Grundlagen von HTML, CSS, Layouts und responsivem Design.'
    ],
    [
        'id' => 3,
        'title' => 'Git und Versionierung',
        'description' => 'Arbeite im Team mit Git und GitHub.',
        'content' => 'Einführung in Git, Commits, Branches und Zusammenarbeit mit GitHub.'
    ],
];

function findCourseById($id, $courses) {
    foreach ($courses as $c) {
        if ((int)$c['id'] === (int)$id) {
            return $c;
        }
    }
    return null;
}

$course = null;
$error = '';

$id = $_GET['id'] ?? null;

if ($id === null) {
    $error = 'Kein Kurs angegeben.';
} else {
    $course = findCourseById($id, $courses);
    if (!$course) {
        $error = 'Kurs wurde nicht gefunden.';
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kursdetails - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/nav.php'; ?>

<main class="container mt-4">
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <a href="index.php" class="btn btn-secondary">Zur Kursübersicht</a>
    <?php else: ?>
        <h2 class="mb-3">
            <?php echo htmlspecialchars($course['title']); ?>
        </h2>
        <p class="text-muted mb-4">
            <?php echo htmlspecialchars($course['description']); ?>
        </p>

        <div class="mb-4">
            <h4>Inhalte</h4>
            <p><?php echo nl2br(htmlspecialchars($course['content'])); ?></p>
        </div>

        <!-- Platzhalter für Downloads / Materialien -->
        <div class="mb-4">
            <h4>Materialien (später)</h4>
            <p>Hier könnten später Dateien oder Links zum Kurs angezeigt werden.</p>
        </div>

        <a href="index.php" class="btn btn-secondary">Zurück zur Übersicht</a>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
