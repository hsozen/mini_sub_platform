<?php
// File: /api/app.php
declare(strict_types=1);

$path = parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH) ?: "/";

$routes = [
  "/" => "index.php",
  "/index.php" => "index.php",

  "/login.php" => "login.php",
  "/register.php" => "register.php",
  "/logout.php" => "logout.php",
  "/account.php" => "account.php",

  "/content_free.php" => "content_free.php",
  "/content_premium.php" => "content_premium.php",
  "/admin.php" => "admin.php",
];

// If you request a direct /api/*.php url, normalize it too.
if (str_starts_with($path, "/api/")) {
  $maybe = substr($path, 4); // remove "/api"
  if ($maybe === "") $maybe = "/";
  $path = $maybe;
}

$file = $routes[$path] ?? null;

if (!$file) {
  http_response_code(404);
  echo "404 Not Found";
  exit;
}

// IMPORTANT: include the existing page file from /api
require __DIR__ . "/" . $file;
