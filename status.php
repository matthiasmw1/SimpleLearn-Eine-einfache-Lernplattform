<?php
// index.php
declare(strict_types=1);
require_once __DIR__ . '/util/dbUtil.php';
require_once __DIR__ . '/util/utils.php';


$ok = 'not connected';
try {
  $pdo = DB::conn();
  $ok = 'DB connected ✅';
} catch (Throwable $e) {
  $ok = 'DB error: ' . $e->getMessage();
}


?>

<!doctype html>
<html lang="de">
  <head>
    <?php require __DIR__ . '/includes/head-includes.php'; ?>
    <title>Status</title>
  </head>
  <body class="container py-4">
    <h1>SimpleLearn</h1>
    <p><?= htmlspecialchars($ok) ?></p>
    <p><a class="btn btn-primary" href="about.php">About</a></p>
  </body>
</html>
