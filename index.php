<?php
// index.php
declare(strict_types=1);
require_once __DIR__ . '/util/dbUtil.php';
require_once __DIR__ . '/util/utils.php';
require __DIR__ . '/includes/nav.php';
start_session_once();

$ok = 'not connected';
try {
  $pdo = DB::conn();
  $ok = 'DB connected ✅';
} catch (Throwable $e) {
  $ok = 'DB error: ' . $e->getMessage();
}

$sql = 'SELECT c.title, c.description, c.created_at, u.username AS author
        FROM courses c
        JOIN users u ON u.id = c.created_by
        ORDER BY c.created_at DESC
        LIMIT 20';

$stmt = $pdo->prepare($sql);
$stmt->execute();

$courses = $stmt->fetchAll();
echo "<pre>";
print_r($courses);
echo "</pre>";

?>




<!doctype html>
<html lang="de">
  <head>
    <?php require __DIR__ . '/includes/head-includes.php'; ?>
    <title>Start</title>
  </head>
  <body class="container py-4">
    <h1>SimpleLearn</h1>

    <?php if(empty($courses)): ?>
      <p>Noch keine Kurse vorhanden.</p>
      <button type="button">Kurs erstellen</button>
    <?php else: ?>
      <?php foreach ($courses as $courses): ?>
        <p><?= htmlspecialchars($courses['title']) ?></p>
      <?php endforeach; ?>

    <p><?= htmlspecialchars($ok) ?></p>
    <p><a class="btn btn-primary" href="about.php">About</a></p>
  </body>
</html>
