<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'simplelearn_db_user');
define('DB_PASSWORD', 'nsZfUXRS29DowgoNrMgm2Qw2eCRT3wxhWrmX5ZHbo2YZjhqoZe');
define('DB_NAME', 'simplelearn');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}

define('UPLOADS_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

session_start();
?>
