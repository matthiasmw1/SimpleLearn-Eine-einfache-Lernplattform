<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/auth_helper.php';
require_once __DIR__ . '/util/db_users.php';

requireLogin();

if (!isAdmin()) {
    header("Location: index.php");
    exit;
}

$user_id = $_GET['id'] ?? null;

if (!$user_id || !is_numeric($user_id)) {
    header("Location: admin.php");
    exit;
}

$user = getUserById($user_id);

if (!$user || $user_id === getCurrentUserId()) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $GLOBALS['pdo']->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$user_id])) {
            header("Location: admin.php?tab=users");
            exit;
        }
    } catch (PDOException $e) {
        $error = 'Fehler beim Löschen.';
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzer löschen - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <h2 class="mb-4">Benutzer löschen</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-warning col-lg-6">
            <strong>Warnung!</strong> Du wirst folgenden Benutzer permanent löschen:
            <br><br>
            <strong><?php echo htmlspecialchars($user['username']); ?></strong> (<?php echo htmlspecialchars($user['email']); ?>)
            <br><br>
            Alle Kurse und Aufgaben dieses Benutzers werden ebenfalls gelöscht!<br>
            Diese Aktion kann <strong>NICHT</strong> rückgängig gemacht werden!
        </div>

        <form method="post" class="col-lg-6">
            <div class="mb-3">
                <button type="submit" class="btn btn-danger btn-lg">
                    Ja, Benutzer löschen
                </button>
                <a href="admin.php?tab=users" class="btn btn-secondary btn-lg">
                    Abbrechen
                </a>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
