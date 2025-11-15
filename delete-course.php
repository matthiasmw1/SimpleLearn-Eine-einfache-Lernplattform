<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/util/courses.php';

$userId = (int)$_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$course = $id ? findCourseById($id) : null;

$error = '';
$success = '';

if (!$course) {
    $error = 'Kurs wurde nicht gefunden.';
} elseif ((int)$course['owner_id'] !== $userId) {
    $error = 'Du darfst diesen Kurs nicht löschen.';
} else {
    // SPÄTER: Hier würde der Kurs in der Datenbank gelöscht
    $success = 'Der Kurs "' . $course['title'] . '" wurde (Demo) gelöscht. Die echte Löschung folgt mit der Datenbank.';
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
    <h2 class="mb-4">Kurs löschen</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <a href="my-courses.php" class="btn btn-secondary">Zurück zu meinen Kursen</a>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
