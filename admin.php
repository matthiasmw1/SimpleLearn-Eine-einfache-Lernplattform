<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/auth_helper.php';
require_once __DIR__ . '/util/db_users.php';
require_once __DIR__ . '/util/db_courses.php';

requireLogin();

if (!isAdmin()) {
    header("Location: index.php");
    exit;
}

$tab = $_GET['tab'] ?? 'users';
$errors = [];
$success = '';

// DELETE User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    $user_id = $_POST['user_id'] ?? null;
    
    if ($user_id && is_numeric($user_id) && $user_id != getCurrentUserId()) {
        $stmt = $GLOBALS['pdo']->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$user_id])) {
            $success = 'User gelöscht.';
        } else {
            $errors[] = 'Fehler beim Löschen.';
        }
    } else {
        $errors[] = 'Du kannst dich selbst nicht löschen!';
    }
}

// RESET Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $user_id = $_POST['user_id'] ?? null;
    $new_password = 'password123';
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    
    if ($user_id && is_numeric($user_id)) {
        $stmt = $GLOBALS['pdo']->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        if ($stmt->execute([$hashed, $user_id])) {
            $success = "Passwort zurückgesetzt auf: $new_password";
        } else {
            $errors[] = 'Fehler beim Zurücksetzen.';
        }
    }
}

// DELETE Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_course') {
    $course_id = $_POST['course_id'] ?? null;
    
    if ($course_id && is_numeric($course_id)) {
        $stmt = $GLOBALS['pdo']->prepare("DELETE FROM courses WHERE id = ?");
        if ($stmt->execute([$course_id])) {
            $success = 'Kurs gelöscht.';
        } else {
            $errors[] = 'Fehler beim Löschen.';
        }
    }
}

// Daten laden
$users = [];
$courses = [];

if ($tab === 'users') {
    $stmt = $GLOBALS['pdo']->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} elseif ($tab === 'courses') {
    $stmt = $GLOBALS['pdo']->query("SELECT c.*, u.username as creator_name FROM courses c JOIN users u ON c.created_by = u.id ORDER BY c.created_at DESC");
    $courses = $stmt->fetchAll();
}

// Stats
$user_count = $GLOBALS['pdo']->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
$course_count = $GLOBALS['pdo']->query("SELECT COUNT(*) as count FROM courses")->fetch()['count'];
$task_count = $GLOBALS['pdo']->query("SELECT COUNT(*) as count FROM tasks")->fetch()['count'];
$file_count = $GLOBALS['pdo']->query("SELECT COUNT(*) as count FROM files")->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container-fluid px-4">
        <div class="row align-items-center mb-4 mt-4">
            <div class="col">
                <h1>Admin Panel</h1>
                <p class="lead">Verwalte Benutzer, Kurse und die Plattform</p>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Benutzer</h5>
                        <h2><?php echo $user_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Kurse</h5>
                        <h2><?php echo $course_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Aufgaben</h5>
                        <h2><?php echo $task_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Dateien</h5>
                        <h2><?php echo $file_count; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?php echo $tab === 'users' ? 'active' : ''; ?>" href="?tab=users">
                    Benutzer
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $tab === 'courses' ? 'active' : ''; ?>" href="?tab=courses">
                    Kurse
                </a>
            </li>
        </ul>

        <!-- Users Tab -->
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
                                <th>Username</th>
                                <th>Email</th>
                                <th>Rolle</th>
                                <th>Erstellt</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'secondary'; ?>">
                                            <?php echo strtoupper($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="reset_password">
                                            <button type="submit" class="btn btn-sm btn-warning" 
                                                    onclick="return confirm('Passwort auf password123 zurücksetzen?')">
                                                Reset Password
                                            </button>
                                        </form>

                                        <?php if ($user['id'] != getCurrentUserId()): ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="delete_user">
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('User wirklich löschen?')">
                                                    Löschen
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Courses Tab -->
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
                                <th>Titel</th>
                                <th>Ersteller</th>
                                <th>Aufgaben</th>
                                <th>Erstellt</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): 
                                $task_stmt = $GLOBALS['pdo']->prepare("SELECT COUNT(*) as count FROM tasks WHERE course_id = ?");
                                $task_stmt->execute([$course['id']]);
                                $task_count = $task_stmt->fetch()['count'] ?? 0;
                            ?>
                                <tr>
                                    <td><?php echo $course['id']; ?></td>
                                    <td><?php echo htmlspecialchars($course['title']); ?></td>
                                    <td><?php echo htmlspecialchars($course['creator_name']); ?></td>
                                    <td><?php echo $task_count; ?></td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($course['created_at'])); ?></td>
                                    <td>
                                        <a href="course-details.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-info">
                                            Ansehen
                                        </a>

                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                            <input type="hidden" name="action" value="delete_course">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Kurs wirklich löschen?')">
                                                Löschen
                                            </button>
                                        </form>
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
