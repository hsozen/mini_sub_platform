<?php
// File: /app/bootstrap.php
declare(strict_types=1);

// 1) Load config (must return array)
$configFile = __DIR__ . "/config.php";
if (!file_exists($configFile)) {
  throw new RuntimeException("Missing config.php at: " . $configFile);
}
$config = require $configFile;
if (!is_array($config)) {
  throw new RuntimeException("config.php must return an array.");
}

$GLOBALS["config"] = $config;

// 2) Start session
$sessionName = (string)($config["session_name"] ?? "app_session");
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_name($sessionName);
  session_start();
}

// 3) Helpers
require_once __DIR__ . "/db.php";

// If you have these helper files, keep them. If not, remove the lines.
$helpers = [
  __DIR__ . "/helpers.php",
  __DIR__ . "/auth/auth.php",
];

foreach ($helpers as $f) {
  if (file_exists($f)) require_once $f;
}

// 4) DB init
$pdo = db($config);
$GLOBALS["pdo"] = $pdo;

// 5) Ensure DB file exists only if your db_path is a filesystem path (sqlite)
$dbPath = (string)($config["db_path"] ?? "");
if ($dbPath !== "" && str_starts_with($dbPath, "/") || str_contains($dbPath, ":\\") || str_contains($dbPath, __DIR__)) {
  $dir = dirname($dbPath);
  if (!is_dir($dir)) {
    @mkdir($dir, 0777, true);
  }
  if (!file_exists($dbPath)) {
    // create empty sqlite file
    @touch($dbPath);
  }
}

// 6) Basic response helpers (only if not already defined elsewhere)
if (!function_exists("redirect")) {
  function redirect(string $to): void {
    header("Location: " . $to);
    exit;
  }
}
if (!function_exists("h")) {
  function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
  }
}
