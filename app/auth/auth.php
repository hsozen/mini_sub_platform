<?php
// File: /app/auth/auth.php
declare(strict_types=1);

if (!function_exists("auth")) {
  function auth(): array {
    $pdo = $GLOBALS["pdo"] ?? null;

    $userId = (int)($_SESSION["user_id"] ?? 0);
    if ($userId <= 0 || !($pdo instanceof PDO)) {
      return ["is_logged_in" => false, "user" => null, "is_admin" => false];
    }

    $stmt = $pdo->prepare("
      SELECT id, username, name, bio, is_paid, is_featured, is_admin, is_active
      FROM users
      WHERE id = :id
      LIMIT 1
    ");
    $stmt->execute([":id" => $userId]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$u || (int)$u["is_active"] !== 1) {
      // session lied or user removed
      unset($_SESSION["user_id"], $_SESSION["user"]);
      return ["is_logged_in" => false, "user" => null, "is_admin" => false];
    }

    $user = [
      "id" => (int)$u["id"],
      "username" => (string)$u["username"],
      "name" => (string)$u["name"],
      "bio" => (string)($u["bio"] ?? ""),
      "is_paid" => (int)$u["is_paid"],
      "is_featured" => (int)$u["is_featured"],
      "is_admin" => (int)$u["is_admin"],
      "is_active" => (int)$u["is_active"],
    ];

    // keep a copy for convenience (optional)
    $_SESSION["user"] = $user;

    return [
      "is_logged_in" => true,
      "user" => $user,
      "is_admin" => ((int)$user["is_admin"] === 1),
    ];
  }
}

if (!function_exists("auth_logout")) {
  function auth_logout(): void {
    unset($_SESSION["user_id"], $_SESSION["user"]);
    if (session_status() === PHP_SESSION_ACTIVE) {
      session_regenerate_id(true);
    }
  }
}
