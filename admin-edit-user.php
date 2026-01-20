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

if (!$user) {
    header("Location: admin.php");
    exit;
}

$errors = [];
$success = '';
$role = $user['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? 'user';
    
    if (!in_array($role, ['user', 'admin'])) {
        $errors[] = 'Ungültige Rolle.';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $GLOBALS['pdo']->prepare("UPDATE users SET role = ? WHERE id = ?");
            if ($stmt->execute([$role, $user_id])) {
                $success = 'Benutzer erfolgreich aktualisiert!';
                header("refresh:2;url=admin.php?tab=users");
            } else {
                $errors[] = 'Fehler beim Aktualisieren.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Datenbankfehler.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzer bearbeiten - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <h2 class="mb-4">Benutzer bearbeiten: <?php echo htmlspecialchars($user['username']); ?></h2>

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

        <div class="card col-lg-6">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Benutzername</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        <small class="form-text text-muted">Kann nicht geändert werden</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rolle</label>
                        <select name="role" class="form-control">
                            <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>Benutzer</option>
                            <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">Speichern</button>
                        <a href="admin.php?tab=users" class="btn btn-secondary btn-lg">Abbrechen</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
