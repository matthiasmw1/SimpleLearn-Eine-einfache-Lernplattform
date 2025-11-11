<?php
declare(strict_types=1);
require_once __DIR__ . '/util/dbUtil.php';
require_once __DIR__ . '/util/utils.php';
start_session_once();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usernameOrEmail = trim($_POST['user'] ?? '');
  $password        = $_POST['password'] ?? '';

  if ($usernameOrEmail === '' || $password === '') {
    $errors[] = 'Bitte Benutzername/E-Mail und Passwort angeben.';
  } else {
    try {
      $pdo = DB::conn();
      $stmt = $pdo->prepare('SELECT id, username, email, password_hash, role FROM users WHERE username = :u OR email = :u LIMIT 1');
      $stmt->execute([':u' => $usernameOrEmail]);
      $user = $stmt->fetch();
      if (!$user || !password_verify($password, $user['password_hash'])) {
        $errors[] = 'Ungültige Anmeldedaten.';
      } else {
        // Session setzen
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // 'user' | 'admin'
        redirect('index.php');
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
  <title>Login</title>
</head>
<body class="container py-4" >
  <h1>Login</h1>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" class="vstack gap-3" novalidate>
    <div>
      <label class="form-label">Benutzername oder E-Mail</label>
      <input class="form-control" name="user" value="<?= htmlspecialchars($_POST['user'] ?? '') ?>" required>
    </div>
    <div>
      <label class="form-label">Passwort</label>
      <input class="form-control" type="password" name="password" required>
    </div>
    <button class="btn btn-primary" type="submit">Anmelden</button>
    <a class="btn btn-link" href="register.php">Neu? Registrieren</a>
  </form>
</body>
</html>
