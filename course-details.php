<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/db_tasks.php';
require_once __DIR__ . '/util/db_files.php';
require_once __DIR__ . '/util/auth_helper.php';

$course_id = $_GET['id'] ?? null;

if (!$course_id || !is_numeric($course_id)) {
    header("Location: index.php");
    exit;
}

$course = getCourseById($course_id);

if (!$course) {
    header("Location: index.php");
    exit;
}

$tasks = getTasksByCourseId($course_id);
$files = getFilesByCourseId($course_id);
$can_edit = isLoggedIn() && canEditCourse($course_id);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - SimpleLearn</title>
    <?php include __DIR__ . '/includes/head-includes.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php include __DIR__ . '/includes/nav.php'; ?>

    <main class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <h1><?php echo htmlspecialchars($course['title']); ?></h1>
                <p class="text-muted">
                    von <strong><?php echo htmlspecialchars($course['creator_name']); ?></strong>
                    <br>
                    <small>Erstellt: <?php echo date('d.m.Y', strtotime($course['created_at'])); ?></small>
                </p>
            </div>
            <?php if ($can_edit): ?>
                <div class="col-auto">
                    <a href="edit-course.php?id=<?php echo $course_id; ?>" class="btn btn-warning">
                        Bearbeiten
                    </a>
                    <form method="post" action="delete-item.php" style="display: inline;">
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        <input type="hidden" name="action" value="delete_course">
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Kurs wirklich löschen?')">
                            Löschen
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Kursbeschreibung -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Kursbeschreibung</h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo htmlspecialchars($course['description']); ?></p>
                    </div>
                </div>

                <!-- Kursinhalt -->
                <?php if ($course['content']): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Inhalt</h5>
                        </div>
                        <div class="card-body">
                            <p><?php echo nl2br(htmlspecialchars($course['content'])); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Aufgaben -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Aufgaben (<?php echo count($tasks); ?>)</h5>
                        <?php if ($can_edit): ?>
                            <a href="create-task.php?course_id=<?php echo $course_id; ?>" class="btn btn-sm btn-primary">
                                Neue Aufgabe
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (empty($tasks)): ?>
                            <p class="text-muted">Keine Aufgaben vorhanden.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($tasks as $task): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <?php echo htmlspecialchars($task['title']); ?>
                                                    <span class="badge bg-<?php 
                                                        echo $task['status'] === 'pending' ? 'warning' : 
                                                             ($task['status'] === 'submitted' ? 'info' : 'success'); 
                                                    ?>">
                                                        <?php 
                                                        echo $task['status'] === 'pending' ? 'Ausstehend' :
                                                             ($task['status'] === 'submitted' ? 'Eingereicht' : 'Bewertet');
                                                        ?>
                                                    </span>
                                                </h6>
                                                <p class="mb-1"><?php echo htmlspecialchars($task['description']); ?></p>
                                                <small class="text-muted">
                                                    Fällig: <?php echo date('d.m.Y', strtotime($task['due_date'])); ?>
                                                </small>
                                            </div>
                                            <?php if ($can_edit): ?>
                                                <div>
                                                    <a href="edit-task.php?id=<?php echo $task['id']; ?>" 
                                                       class="btn btn-sm btn-warning">
                                                        Bearbeiten
                                                    </a>
                                                    <form method="post" action="delete-item.php" style="display: inline;">
                                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                        <input type="hidden" name="action" value="delete_task">
                                                        <input type="hidden" name="redirect_url" value="course-details.php?id=<?php echo $course_id; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Aufgabe wirklich löschen?')">
                                                            Löschen
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Dateien -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Dateien (<?php echo count($files); ?>)</h5>
                        <?php if ($can_edit): ?>
                            <a href="upload-file.php?course_id=<?php echo $course_id; ?>" class="btn btn-sm btn-primary">
                                Datei hochladen
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (empty($files)): ?>
                            <p class="text-muted">Keine Dateien vorhanden.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($files as $file): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                <?php echo htmlspecialchars($file['file_name']); ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?php echo round($file['file_size'] / 1024, 2); ?> KB | 
                                                <?php echo date('d.m.Y H:i', strtotime($file['uploaded_at'])); ?>
                                            </small>
                                        </div>
                                        <div>
                                            <a href="download-file.php?id=<?php echo $file['id']; ?>" 
                                               class="btn btn-sm btn-success">
                                                Download
                                            </a>
                                            <?php if ($can_edit): ?>
                                                <form method="post" action="delete-item.php" style="display: inline;">
                                                    <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
                                                    <input type="hidden" name="action" value="delete_file">
                                                    <input type="hidden" name="redirect_url" value="course-details.php?id=<?php echo $course_id; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Datei wirklich löschen?')">
                                                        Löschen
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Kurs-Info</h5>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Ersteller:</strong><br>
                            <?php echo htmlspecialchars($course['creator_name']); ?>
                        </p>
                        <p>
                            <strong>E-Mail:</strong><br>
                            <?php echo htmlspecialchars($course['creator_email']); ?>
                        </p>
                        <p>
                            <strong>Aufgaben:</strong><br>
                            <?php echo $course['task_count']; ?>
                        </p>
                        <p>
                            <strong>Dateien:</strong><br>
                            <?php echo $course['file_count']; ?>
                        </p>
                        <hr>
                        <a href="index.php" class="btn btn-secondary w-100">
                            Zurück zu Kursen
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
