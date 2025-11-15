<?php
session_start();

// Fake-User "Datenbank" – gleich wie beim Login
$fakeUsers = [
    [
        'id' => 1,
        'username' => 'testUser',
        'email' => 'example@example.org',
        'password_hash' => '$2y$10$QqSCJCBQJfXzsN91SwbYWe6TaD51M7eLeLdFzE8mBlwwv9qZ7sG9u',
        'role' => 'user'
    ]
];

function findUser($usernameOrEmail, $fakeUsers) {
    foreach ($fakeUsers as $u) {
        if ($u['username'] === $usernameOrEmail || $u['email'] === $usernameOrEmail) {
            return $u;
        }
    }
    return null;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordRepeat = $_POST['password_repeat'] ?? '';

    // Basis-Validierung
    if ($username === '' || $email === '' || $password === '' || $passwordRepeat === '') {
        $errors[] = 'Bitte alle Felder ausfüllen.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Bitte eine gültige E-Mail-Adresse eingeben.';
    }

    if ($password !== $passwordRepeat) {
        $errors[] = 'Die Passwörter stimmen nicht überein.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Das Passwort muss mindestens 6 Zeichen lang sein.';
    }

    // Prüfen, ob User/Email schon existieren (in Fake-DB)
    foreach ($fakeUsers as $u) {
        if ($u['username'] === $username) {
            $errors[] = 'Benutzername ist bereits vergeben.';
        }
        if ($u['email'] === $email) {
            $errors[] = 'E-Mail ist bereits registriert.';
        }
    }

    // Wenn alles ok ist:
    if (empty($errors)) {
        // Hier würde später der DB-Insert passieren
        // $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        // INSERT INTO users ...

        $success = 'Registrierung erfolgreich! Du kannst dich jetzt mit deinen Daten einloggen (Speicherung in DB folgt später).';
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/nav.php'; ?>

<main class="container mt-4">
    <h2 class="mb-4">Registrierung</h2>

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
            <label class="form-label">Benutzername</label>
            <input type="text" name="username" class="form-control"
                   value="<?php echo htmlspecialchars($username ?? ''); ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">E-Mail</label>
            <input type="email" name="email" class="form-control"
                   value="<?php echo htmlspecialchars($email ?? ''); ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Passwort</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Passwort wiederholen</label>
            <input type="password" name="password_repeat" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Registrieren</button>
    </form>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
