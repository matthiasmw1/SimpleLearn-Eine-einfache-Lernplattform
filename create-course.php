<?php
require __DIR__ . '/util/auth.php';
startAuthSession();

// Nur eingeloggte Benutzer dürfen Kurse erstellen
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = '';

$title = '';
$description = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $description === '' || $content === '') {
        $errors[] = 'Bitte alle Felder ausfüllen.';
    }

    // Später mehr Validierung, z. B. Länge, HTML, etc.

    if (empty($errors)) {
        // SPÄTER: Speichern in Datenbank
        // $stmt = $pdo->prepare('INSERT INTO courses ...');

        $success = 'Kurs wurde (demo-mäßig) erstellt. Die echte Speicherung in der Datenbank folgt später.';
        
        // Optional Felder leeren nach Erfolg
        $title = '';
        $description = '';
        $content = '';
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurs erstellen - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/nav.php'; ?>

<main class="container mt-4">
    <h2 class="mb-4">Neuen Kurs erstellen</h2>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?php echo htmlspecialchars($e); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Kurstitel</label>
            <input type="text" name="title" class="form-control"
                   value="<?php echo htmlspecialchars($title); ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kurzbeschreibung</label>
            <textarea name="description" class="form-control" rows="2"
                      required><?php echo htmlspecialchars($description); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Inhalte / Beschreibung</label>
            <textarea name="content" class="form-control" rows="5"
                      required><?php echo htmlspecialchars($content); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Kurs erstellen</button>
    </form>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
