<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/auth_helper.php';

requireLogin();

$user_id = getCurrentUserId();
$status_filter = $_GET['status'] ?? 'all';
$sort_by = $_GET['sort'] ?? 'due_date';


$query = "
    SELECT 
        t.id,
        t.course_id,
        t.title,
        t.description,
        t.due_date,
        t.status,
        t.created_at,
        c.title AS course_title
    FROM tasks t
    JOIN courses c ON t.course_id = c.id
    WHERE c.created_by = ?
";
$params = [$user_id];


if ($status_filter !== 'all') {
    $query .= " AND t.status = ?";
    $params[] = $status_filter;
}


if ($sort_by === 'status') {
    $query .= " ORDER BY t.status ASC, t.due_date ASC";
} elseif ($sort_by === 'newest') {
    $query .= " ORDER BY t.created_at DESC";
} else {
    $query .= " ORDER BY t.due_date ASC, t.created_at DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll();


function statusBadge($status) {
    return [
        'pending'   => ['bg-warning', 'Ausstehend'],
        'submitted' => ['bg-info', 'Eingereicht'],
        'graded'    => ['bg-success', 'Bewertet'],
    ][$status] ?? ['bg-secondary', 'Unbekannt'];
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Aufgaben - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/nav.php'; ?>

<main class="container mt-4">
    <h1>Meine Aufgaben</h1>
    <p class="lead">Alle Aufgaben aus meinen Kursen</p>

    <div class="mb-3">
        <strong>Status:</strong>
        <a href="?status=all&sort=<?php echo htmlspecialchars($sort_by); ?>" class="btn btn-sm <?php echo $status_filter==='all'?'btn-primary':'btn-outline-primary'; ?>">Alle</a>
        <a href="?status=pending&sort=<?php echo htmlspecialchars($sort_by); ?>" class="btn btn-sm <?php echo $status_filter==='pending'?'btn-primary':'btn-outline-primary'; ?>">Ausstehend</a>
        <a href="?status=submitted&sort=<?php echo htmlspecialchars($sort_by); ?>" class="btn btn-sm <?php echo $status_filter==='submitted'?'btn-primary':'btn-outline-primary'; ?>">Eingereicht</a>
        <a href="?status=graded&sort=<?php echo htmlspecialchars($sort_by); ?>" class="btn btn-sm <?php echo $status_filter==='graded'?'btn-primary':'btn-outline-primary'; ?>">Bewertet</a>
    </div>

    <div class="mb-3">
        <strong>Sortierung:</strong>
        <a href="?status=<?php echo htmlspecialchars($status_filter); ?>&sort=due_date" class="btn btn-sm <?php echo $sort_by==='due_date'?'btn-secondary':'btn-outline-secondary'; ?>">Fälligkeitsdatum</a>
        <a href="?status=<?php echo htmlspecialchars($status_filter); ?>&sort=status" class="btn btn-sm <?php echo $sort_by==='status'?'btn-secondary':'btn-outline-secondary'; ?>">Status</a>
        <a href="?status=<?php echo htmlspecialchars($status_filter); ?>&sort=newest" class="btn btn-sm <?php echo $sort_by==='newest'?'btn-secondary':'btn-outline-secondary'; ?>">Neueste</a>
    </div>

    <?php if (empty($tasks)): ?>
        <div class="alert alert-info">Du hast aktuell keine Aufgaben in deinen Kursen.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($tasks as $task): 
                [$badgeClass, $badgeText] = statusBadge($task['status']);
            ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-1"><?php echo htmlspecialchars($task['title']); ?></h5>
                            <small class="text-muted">
                                Kurs: <?php echo htmlspecialchars($task['course_title']); ?> ·
                                Fällig: <?php echo htmlspecialchars($task['due_date']); ?>
                            </small>
                        </div>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                    </div>
                    <?php if (!empty($task['description'])): ?>
                        <p class="mb-1 mt-2 text-muted">
                            <?php echo nl2br(htmlspecialchars($task['description'])); ?>
                        </p>
                    <?php endif; ?>
                    <div class="mt-2">
                        <a href="course-details.php?id=<?php echo (int)$task['course_id']; ?>" class="btn btn-sm btn-primary">Zum Kurs</a>
                        <a href="edit-task.php?id=<?php echo (int)$task['id']; ?>" class="btn btn-sm btn-outline-secondary">Bearbeiten</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
