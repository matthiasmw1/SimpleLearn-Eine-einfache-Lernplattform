<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/util/db_files.php';
require_once __DIR__ . '/util/db_courses.php';
require_once __DIR__ . '/util/auth_helper.php';

$file_id = $_GET['id'] ?? null;

if (!$file_id || !is_numeric($file_id)) {
    header("Location: index.php");
    exit;
}

$file = getFileById($file_id);

if (!$file) {
    header("Location: index.php");
    exit;
}

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$course = getCourseById($file['course_id']);

if (!$course) {
    header("Location: index.php");
    exit;
}


$file_path = __DIR__ . $file['file_path'];


if (!file_exists($file_path)) {
    header("Location: course-details.php?id=" . $file['course_id'] . "&error=Datei nicht gefunden");
    exit;
}

header('Content-Type: ' . $file['file_type']);
header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($file_path);
exit;
?>
