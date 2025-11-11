<?php
declare(strict_types=1);
require_once __DIR__ . '/util/dbUtil.php';
require_once __DIR__ . '/util/utils.php';
require_once __DIR__ . '/config/config.php';
start_session_once();

$errors = [];
$success = null;

// Form verarbeitet?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $roleReq  = $_POST['role'] ?? 'user';

  // 1) Grundvalidierung
  if ($username === '' || strlen($username) < 3) { $errors[] = 'Username min. 3 Zeichen.'; }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Ungültige E-Mail.'; }
  if (strlen($password) < 6) { $errors[] = 'Passwort min. 6 Zeichen.'; }

  // 2) Rolle nur erlauben, wenn Flag aktiv — sonst immer "user"
  $role = 'user';
  if (ALLOW_ROLE_SELECT_ON_REGISTER) {
    $role = ($roleReq === 'admin') ? 'admin' : 'user';
  }

  if (!$errors) {
    try {
      $pdo = DB::conn();

      // 3) Duplikate prüfen
      $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1');
      $stmt->execute([':u' => $username, ':e' => $email]);
      if ($stmt->fetch()) {
        $errors[] = 'Username oder E-Mail existiert bereits.';
      } else {
        // 4) Hash + Insert
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (username, email, password_hash, role) VALUES (:u, :e, :p, :r)');
        $ins->execute([':u' => $username, ':e' => $email, ':p' => $hash, ':r' => $role]);
        $success = 'Registrierung erfolgreich. Du kannst dich jetzt einloggen.';
      }
    } catch (Throwable $t) {
      $errors[] = 'DB-Fehler: ' . $t->getMessage();
    }
  }
}
?>
<!doctype html>
<html lang="de">
<head>
  <?php require __DIR__ . '/includes/head-includes.php'; ?>
  <title>Registrieren</title>
</head>
<body class="container py-4">
  <h1>Registrieren</h1>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <p><a class="btn btn-primary" href="login.php">Zum Login</a></p>
  <?php endif; ?>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" class="vstack gap-3" novalidate>
    <div>
      <label class="form-label">Username</label>
      <input class="form-control" name="username" required minlength="3" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label">E-Mail</label>
      <input class="form-control" type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label">Passwort</label>
      <input class="form-control" type="password" name="password" required minlength="6">
    </div>

    <?php if (ALLOW_ROLE_SELECT_ON_REGISTER): ?>
      <div>
        <label class="form-label">Rolle (nur Test)</label>
        <select class="form-select" name="role">
          <option value="user"  <?= (($_POST['role'] ?? '')==='user')?'selected':''; ?>>User</option>
          <option value="admin" <?= (($_POST['role'] ?? '')==='admin')?'selected':''; ?>>Admin</option>
        </select>
        <div class="form-text">Nur lokal zum Testen. Später deaktivieren.</div>
      </div>
    <?php endif; ?>

    <button class="btn btn-success" type="submit">Registrieren</button>
  </form>
</body>
</html>
