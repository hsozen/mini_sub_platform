<?php
// File: /app/auth/auth_actions.php
declare(strict_types=1);

function auth_register(PDO $pdo, string $username, string $name, string $password): array {
  $username = trim($username);
  $name = trim($name);

  if ($username === "" || $name === "" || $password === "") {
    return ["ok" => false, "error" => "All fields are required."];
  }

  if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    return ["ok" => false, "error" => "Username must be 3–20 characters and contain only letters, numbers, or underscore."];
  }

  if (mb_strlen($password) < 8) {
    return ["ok" => false, "error" => "Password must be at least 8 characters."];
  }

  // Check duplicate username
  $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
  $stmt->execute([":u" => $username]);
  if ($stmt->fetch()) {
    return ["ok" => false, "error" => "That username is already taken."];
  }

  $hash = password_hash($password, PASSWORD_DEFAULT);
  $now = date("c");

  $stmt = $pdo->prepare("
    INSERT INTO users (username, name, password_hash, bio, is_paid, is_featured, is_admin, is_active, created_at)
    VALUES (:username, :name, :password_hash, :bio, 0, 0, 0, 1, :created_at)
  ");

  $stmt->execute([
    ":username" => $username,
    ":name" => $name,
    ":password_hash" => $hash,
    ":bio" => null,
    ":created_at" => $now,
  ]);

  $id = (int)$pdo->lastInsertId();

  $user = [
    "id" => $id,
    "username" => $username,
    "name" => $name,
    "bio" => null,
    "is_paid" => 0,
    "is_featured" => 0,
    "is_admin" => 0,
    "is_active" => 1,
  ];

  $_SESSION["user"] = $user;

  return ["ok" => true, "user" => $user];
}

function auth_login(PDO $pdo, string $username, string $password): array {
  $username = trim($username);

  if ($username === "" || $password === "") {
    return ["ok" => false, "error" => "Username and password are required."];
  }

  $stmt = $pdo->prepare("
    SELECT id, username, name, bio, password_hash, is_paid, is_featured, is_admin, is_active
    FROM users
    WHERE username = :u
    LIMIT 1
  ");
  $stmt->execute([":u" => $username]);
  $row = $stmt->fetch();

  if (!$row || !password_verify($password, (string)$row["password_hash"])) {
    return ["ok" => false, "error" => "Invalid username or password."];
  }

  if ((int)$row["is_active"] !== 1) {
    return ["ok" => false, "error" => "This account is inactive. Contact admin."];
  }

  $user = [
    "id" => (int)$row["id"],
    "username" => (string)$row["username"],
    "name" => (string)$row["name"],
    "bio" => $row["bio"],
    "is_paid" => (int)$row["is_paid"],
    "is_featured" => (int)$row["is_featured"],
    "is_admin" => (int)$row["is_admin"],
    "is_active" => (int)$row["is_active"],
  ];

  $_SESSION["user"] = $user;

  return ["ok" => true, "user" => $user];
}
