<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_users.php';
require_once __DIR__ . '/util/auth_helper.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['username_or_email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!$usernameOrEmail || !$password) {
        $errors[] = 'Benutzername/E-Mail und Passwort erforderlich.';
    } else {
        $user = findUserByUsernameOrEmail($usernameOrEmail);
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Ungültige Anmeldedaten.';
        } else {
            // Login erfolgreich
            login($user['id'], $user['username'], $user['role']);
            header("Location: index.php");
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

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <div><?php echo htmlspecialchars($e); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="col-md-6" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Benutzername oder E-Mail</label>
                <input type="text" name="username_or_email" class="form-control" autocomplete="off" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label">Passwort</label>
                <input type="password" name="password" class="form-control" autocomplete="off" required>
            </div>

            <button type="submit" class="btn btn-primary">Einloggen</button>
        </form>

        <p class="mt-3">
            Noch kein Konto? <a href="register.php">Jetzt registrieren</a>
        </p>

    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
