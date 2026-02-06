<?php
// File: /app/helpers.php
declare(strict_types=1);

function h(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

function redirect(string $to): never {
  header("Location: " . $to);
  exit;
}

function auth(): array {
  return [
    "is_logged_in" => isset($_SESSION["user"]),
    "user" => $_SESSION["user"] ?? null,
    "is_admin" => (bool)($_SESSION["user"]["is_admin"] ?? false),
    "is_paid" => (bool)($_SESSION["user"]["is_paid"] ?? false),
  ];
}

function set_flash(string $type, string $message): void {
  $_SESSION["flash"] = ["type" => $type, "message" => $message];
}

function get_flash(): ?array {
  if (!isset($_SESSION["flash"])) return null;
  $f = $_SESSION["flash"];
  unset($_SESSION["flash"]);
  return $f;
}

function csrf_token(array $config): string {
  if (!isset($_SESSION["_csrf"])) {
    $_SESSION["_csrf"] = bin2hex(random_bytes(16));
  }
  $secret = (string)($config["csrf_secret"] ?? "change-me");
  return hash_hmac("sha256", (string)$_SESSION["_csrf"], $secret);
}

function csrf_check(array $config, ?string $token): bool {
  if (!$token) return false;
  $expected = csrf_token($config);
  return hash_equals($expected, $token);
}
