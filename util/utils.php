<?php

declare(strict_types=1);

function start_session_once(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
}

function redirect(string $path): never {
  header('Location: ' . $path);
  exit;
}
