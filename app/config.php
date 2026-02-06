<?php
// File: /app/config.php
declare(strict_types=1);

$dbPath = getenv("DB_PATH");
if (!$dbPath) {
  $dbPath = __DIR__ . "/database.sqlite";
}

return [
  "db_path" => "/tmp/database.sqlite",

  "app_name" => "Mini Sub",
  "session_name" => "minisub_session",

  // Change this for real security
  "csrf_secret" => getenv("CSRF_SECRET") ?: "4b8f9e2d1a7c6f0e93d5b7a1c2f8e6d4b0a7c9e1f3d5b7a9c1e3f5b7d9a1c3e5",
];
