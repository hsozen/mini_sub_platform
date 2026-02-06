<?php
// File: /app/db.php
declare(strict_types=1);

function db(array $config): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $dbPath = (string)($config["db_path"] ?? "");
  if ($dbPath === "") {
    throw new RuntimeException("DB path not configured.");
  }

  $pdo = new PDO("sqlite:" . $dbPath, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);

  // Good defaults for SQLite
  $pdo->exec("PRAGMA foreign_keys = ON;");
  $pdo->exec("PRAGMA journal_mode = WAL;");

  // -----------------------------
  // Schema: users
  // -----------------------------
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      username TEXT NOT NULL UNIQUE,
      name TEXT NOT NULL,
      password_hash TEXT NOT NULL,
      bio TEXT NOT NULL DEFAULT '',
      is_paid INTEGER NOT NULL DEFAULT 0,
      is_featured INTEGER NOT NULL DEFAULT 0,
      is_admin INTEGER NOT NULL DEFAULT 0,
      is_active INTEGER NOT NULL DEFAULT 1,
      created_at TEXT NOT NULL
    )
  ");

  // -----------------------------
  // Schema: content
  // -----------------------------
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS content (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      title TEXT NOT NULL,
      body TEXT NOT NULL,
      is_premium INTEGER NOT NULL DEFAULT 0,
      created_at TEXT NOT NULL
    )
  ");

  // -----------------------------
  // Seed content (only once)
  // -----------------------------
  $contentCount = (int)$pdo->query("SELECT COUNT(*) FROM content")->fetchColumn();
  if ($contentCount === 0) {
    $stmt = $pdo->prepare("
      INSERT INTO content (title, body, is_premium, created_at)
      VALUES (:title, :body, :is_premium, :created_at)
    ");

    $now = date("c");
    $seed = [
      ["Welcome: Free Post 1", "This is free content.", 0],
      ["Free Post 2", "Another free article.", 0],
      ["Premium Post 1", "This is premium-only content.", 1],
      ["Premium Post 2", "Another premium post.", 1],
    ];

    foreach ($seed as [$title, $body, $isPremium]) {
      $stmt->execute([
        ":title" => $title,
        ":body" => $body,
        ":is_premium" => (int)$isPremium,
        ":created_at" => $now,
      ]);
    }
  }

  return $pdo;
}
