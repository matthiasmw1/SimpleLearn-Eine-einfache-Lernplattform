<?php
declare(strict_types=1);
require_once __DIR__ . '/util/utils.php';
start_session_once();
$_SESSION = [];
session_destroy();
redirect('index.php');
