<?php
// File: /app/config.php
declare(strict_types=1);

return [
  // SQLite database file path
  "db_path" => __DIR__ . "/database.sqlite",

  // App settings
  "app_name" => "Mini Sub",
  "session_name" => "minisub_session",

  // Create a strong random string later
  "csrf_secret" => "change-me-please",
];
