<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/auth_helper.php';
require_once __DIR__ . '/util/db_users.php';
require_once __DIR__ . '/util/db_courses.php';

requireLogin();

// Nur Admins dürfen hier rein!
if (!isAdmin()) {
    header("Location: index.php");
    exit;
}

// Tabs
$tab = $_GET['tab'] ?? 'users';

// Alle Benutzer holen
$users = [];
$courses = [];

if ($tab === 'users') {
    $stmt = $GLOBALS['pdo']->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} elseif ($tab === 'courses') {
    $stmt = $GLOBALS['pdo']->query("SELECT c.*, u.username as creator_name FROM courses c JOIN users u ON c.created_by = u.id ORDER BY c.created_at DESC");
    $courses = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
    <style>
        .admin-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-admin {
            background-color: #dc3545;
            color: white;
        }
        .badge-user {
            background-color: #6c757d;
            color: white;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .stats-card h3 {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .stats-card .number {
            font-size: 32px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container-fluid px-5">
        <div class="row align-items-center mb-4 mt-4">
            <div class="col">
                <h1>🔑 Admin Panel</h1>
                <p class="lead">Verwalte Benutzer, Kurse und die Plattform</p>
            </div>
        </div>

        <!-- Statistiken -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>Benutzer</h3>
                    <?php 
                    $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) as count FROM users");
                    $result = $stmt->fetch();
                    ?>
                    <div class="number"><?php echo $result['count']; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>Kurse</h3>
                    <?php 
                    $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) as count FROM courses");
                    $result = $stmt->fetch();
                    ?>
                    <div class="number"><?php echo $result['count']; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>Aufgaben</h3>
                    <?php 
                    $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) as count FROM tasks");
                    $result = $stmt->fetch();
                    ?>
                    <div class="number"><?php echo $result['count']; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>Dateien</h3>
                    <?php 
                    $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) as count FROM files");
                    $result = $stmt->fetch();
                    ?>
                    <div class="number"><?php echo $result['count']; ?></div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?php echo $tab === 'users' ? 'active' : ''; ?>" 
                   href="?tab=users">
                    👥 Benutzer
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $tab === 'courses' ? 'active' : ''; ?>" 
                   href="?tab=courses">
                    📚 Kurse
                </a>
            </li>
        </ul>

        <!-- Benutzer-Tab -->
        <?php if ($tab === 'users'): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Alle Benutzer</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Benutzername</th>
                                <th>Email</th>
                                <th>Rolle</th>
                                <th>Registriert</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#<?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="admin-badge badge-<?php echo $user['role']; ?>">
                                            <?php echo strtoupper($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="admin-edit-user.php?id=<?php echo $user['id']; ?>" 
                                               class="btn btn-warning">
                                                ✏️ Bearbeiten
                                            </a>
                                            <?php if ($user['id'] !== getCurrentUserId()): ?>
                                                <a href="admin-delete-user.php?id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-danger"
                                                   onclick="return confirm('Wirklich löschen?')">
                                                    🗑️ Löschen
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Kurse-Tab -->
        <?php if ($tab === 'courses'): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Alle Kurse</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Kurstitel</th>
                                <th>Ersteller</th>
                                <th>Aufgaben</th>
                                <th>Erstellt</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): 
                                // Task-Count für jeden Kurs holen
                                $task_stmt = $GLOBALS['pdo']->prepare("SELECT COUNT(*) as count FROM tasks WHERE course_id = ?");
                                $task_stmt->execute([$course['id']]);
                                $task_result = $task_stmt->fetch();
                                $task_count = $task_result['count'] ?? 0;
                            ?>
                                <tr>
                                    <td>#<?php echo $course['id']; ?></td>
                                    <td><?php echo htmlspecialchars($course['title']); ?></td>
                                    <td><?php echo htmlspecialchars($course['creator_name']); ?></td>
                                    <td><?php echo $task_count; ?></td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($course['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="course-details.php?id=<?php echo $course['id']; ?>" 
                                               class="btn btn-info">
                                                👁️ Ansehen
                                            </a>
                                            <a href="admin-delete-course.php?id=<?php echo $course['id']; ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('Wirklich löschen?')">
                                                🗑️ Löschen
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
