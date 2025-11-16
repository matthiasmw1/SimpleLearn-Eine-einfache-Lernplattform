<?php
require __DIR__ . '/util/users.php';
session_start();


$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usernameOrEmail = $_POST['usernameOrEmail'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($usernameOrEmail === '' || $password === '') {
        $errors[] = 'Bitte Benutzername/E-Mail und Passwort angeben.';
    } else {
        $user = findUserByUsernameOrEmail($usernameOrEmail);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Ungültige Anmeldedaten.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Remember-Me?
            $remember = !empty($_POST['remember_me']);

            if ($remember) {
                // Cookie für z. B. 7 Tage
                setcookie('remember_user', $user['id'], time() + 60 * 60 * 24 * 7, '/');
            } else {
                // Falls es einen alten Cookie gibt → löschen
                setcookie('remember_user', '', time() - 3600, '/');
            }

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

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember_me" class="form-check-input" id="rememberMe">
            <label class="form-check-label" for="rememberMe">
                Angemeldet bleiben
            </label>
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