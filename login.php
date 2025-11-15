<?php
    $fakeUsers = [
        [
            'id' => 1,
            'username' => 'testUser',
            'email' => 'example@example.org',
            'password_hash' => '$2y$10$LQicULMMXeBtUmbGG3.O../BkKIDZNtfbuJYmP4FQRAS5NkAaFTiq',
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

session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $usernameOrEmail = $_POST['usernameOrEmail'] ?? '';
    $password = $_POST['password'] ?? '';
    var_dump($usernameOrEmail, $password);

    if ($usernameOrEmail === '' || $password === '') {
        $errors[] = 'Bitte Benutzername/E-Mail und Passwort angeben.';
    } else {
        $user = findUser($usernameOrEmail, $fakeUsers);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Ungültige Anmeldedaten.';
        } else {
            // Session setzen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header('Location: index.php');
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SimpleLearn</title>

    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
    <h2 class="mb-4">Login</h2>

    <!-- Fehlerausgabe -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?php echo htmlspecialchars($e); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Login-Formular -->
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Benutzername oder E-Mail</label>
            <input type="text" name="usernameOrEmail" class="form-control"
                   placeholder="z.B. testUser oder example@example.org"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Passwort</label>
            <input type="password" name="password" class="form-control"
                   placeholder="Passwort eingeben"
                   required>
        </div>

        <button type="submit" class="btn btn-primary">Einloggen</button>

        <p class="mt-3">
            Noch kein Konto?
            <a href="register.php">Jetzt registrieren</a>
        </p>

    </form>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>