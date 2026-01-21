<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_users.php';

$errors = [];
$success = '';
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_repeat = $_POST['password_repeat'] ?? '';
    

    if (!$username || !$email || !$password || !$password_repeat) {
        $errors[] = 'Bitte alle Felder ausfüllen.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Bitte eine gültige E-Mail-Adresse eingeben.';
    }
    if ($password !== $password_repeat) {
        $errors[] = 'Die Passwörter stimmen nicht überein.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Das Passwort muss mindestens 6 Zeichen lang sein.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Das Passwort muss mindestens einen Kleinbuchstaben enthalten.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Das Passwort muss mindestens einen Großbuchstaben enthalten.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Das Passwort muss mindestens eine Zahl enthalten.';
    }
    if (userExists($username, $email)) {
        $errors[] = 'Benutzername oder E-Mail ist bereits registriert.';
    }
    if (empty($errors)) {
        if (createUser($username, $email, $password)) {
            $success = 'Registrierung erfolgreich! Du kannst dich jetzt einloggen.';
            $username = '';
            $email = '';
        } else {
            $errors[] = 'Registrierung fehlgeschlagen. Bitte versuche es später erneut.';
        }
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

        <form method="post" class="col-md-6" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Benutzername</label>
                <input type="text" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($username); ?>" autocomplete="off" required>
            </div>

            <div class="mb-3">
                <label class="form-label">E-Mail</label>
                <input type="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($email); ?>" autocomplete="off" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Passwort</label>
                <input type="password" name="password" class="form-control" autocomplete="off" required>
                <small class="form-text text-muted">
                    Mindestens 6 Zeichen, mit Groß- und Kleinbuchstaben und Ziffer
                </small>
            </div>

            <div class="mb-3">
                <label class="form-label">Passwort wiederholen</label>
                <input type="password" name="password_repeat" class="form-control" autocomplete="off" required>
            </div>

            <button type="submit" class="btn btn-primary">Registrieren</button>
        </form>

        <p class="mt-3">
            Bereits registriert? <a href="login.php">Zum Login</a>
        </p>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
