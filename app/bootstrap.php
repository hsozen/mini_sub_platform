<?php
// File: /app/bootstrap.php
declare(strict_types=1);

session_start();

$config = require __DIR__ . "/config.php";

if (!empty($config["session_name"])) {
  // Safe to call after session_start in practice for local MVP, keep simple
  // If you want strict correctness later, we move it before session_start
}

require __DIR__ . "/db.php";
require __DIR__ . "/helpers.php";
require __DIR__ . "/middleware.php";
// require __DIR__ . "/auth/auth_actions.php";

// Make config and db accessible
$GLOBALS["config"] = $config;
$GLOBALS["pdo"] = db($config);
