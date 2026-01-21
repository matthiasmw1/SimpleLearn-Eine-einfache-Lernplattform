<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/auth_helper.php';

requireLogin();

$pdo = $GLOBALS['pdo'];
$user_id = getCurrentUserId();
$errors = [];

// Aktuelle Daten laden
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$username = $user['username'];
$email = $user['email'];

// Username ändern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_username') {
    $new_username = trim($_POST['new_username'] ?? '');
    
    if (!$new_username) {
        $errors[] = 'Benutzername darf nicht leer sein.';
    } elseif (strlen($new_username) < 3) {
        $errors[] = 'Benutzername muss mindestens 3 Zeichen lang sein.';
    } else {
        // Prüfen ob Username schon existiert
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$new_username, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = 'Dieser Benutzername existiert bereits.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            if ($stmt->execute([$new_username, $user_id])) {
                $_SESSION['username'] = $new_username;
                $username = $new_username;
                $errors[] = 'Benutzername aktualisiert.';
            }
        }
    }
}

// Email ändern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_email') {
    $new_email = trim($_POST['new_email'] ?? '');
    
    if (!$new_email) {
        $errors[] = 'Email darf nicht leer sein.';
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Ungueltige Email-Adresse.';
    } else {
        // Prüfen ob Email schon existiert
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$new_email, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = 'Diese Email existiert bereits.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            if ($stmt->execute([$new_email, $user_id])) {
                $email = $new_email;
                $errors[] = 'Email aktualisiert.';
            }
        }
    }
}

// Passwort ändern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $new_password_repeat = $_POST['new_password_repeat'] ?? '';
    
    // Altes Passwort prüfen
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();
    
    if (!password_verify($old_password, $user_data['password_hash'])) {
        $errors[] = 'Altes Passwort ist falsch.';
    } elseif (!$new_password || !$new_password_repeat) {
        $errors[] = 'Neues Passwort darf nicht leer sein.';
    } elseif (strlen($new_password) < 6) {
        $errors[] = 'Passwort muss mindestens 6 Zeichen lang sein.';
    } elseif ($new_password !== $new_password_repeat) {
        $errors[] = 'Passwörter stimmen nicht überein.';
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        if ($stmt->execute([$hashed, $user_id])) {
            $errors[] = 'Passwort aktualisiert.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <h2 class="mb-4">Mein Profil</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-<?php echo strpos($errors[0], 'aktualisiert') !== false ? 'success' : 'danger'; ?>">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <!-- Benutzername ändern -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Benutzername</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Aktueller Benutzername: <strong><?php echo htmlspecialchars($username); ?></strong></p>
                        <form method="post" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">Neuer Benutzername</label>
                                <input type="text" name="new_username" class="form-control" autocomplete="off" required>
                            </div>
                            <input type="hidden" name="action" value="change_username">
                            <button type="submit" class="btn btn-primary">Ändern</button>
                        </form>
                    </div>
                </div>

                <!-- Email ändern -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Email-Adresse</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Aktuelle Email: <strong><?php echo htmlspecialchars($email); ?></strong></p>
                        <form method="post" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">Neue Email-Adresse</label>
                                <input type="email" name="new_email" class="form-control" autocomplete="off" required>
                            </div>
                            <input type="hidden" name="action" value="change_email">
                            <button type="submit" class="btn btn-primary">Aendern</button>
                        </form>
                    </div>
                </div>

                <!-- Passwort ändern -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Passwort</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">Altes Passwort</label>
                                <input type="password" name="old_password" class="form-control" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Neues Passwort</label>
                                <input type="password" name="new_password" class="form-control" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Neues Passwort wiederholen</label>
                                <input type="password" name="new_password_repeat" class="form-control" autocomplete="off" required>
                            </div>
                            <input type="hidden" name="action" value="change_password">
                            <button type="submit" class="btn btn-primary">Ändern</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Account-Info</h5>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Benutzername:</strong><br>
                            <?php echo htmlspecialchars($username); ?>
                        </p>
                        <p>
                            <strong>Email:</strong><br>
                            <?php echo htmlspecialchars($email); ?>
                        </p>
                        <hr>
                        <a href="logout.php" class="btn btn-danger w-100">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
