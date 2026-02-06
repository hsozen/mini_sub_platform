<?php
// File: /public/setup.php
declare(strict_types=1);

require __DIR__ . "/../app/bootstrap.php";

$config = $GLOBALS["config"];
$pdo = $GLOBALS["pdo"];

header("Content-Type: text/plain; charset=utf-8");

echo "DB path from config:\n" . $config["db_path"] . "\n\n";
echo "Realpath:\n" . (realpath($config["db_path"]) ?: "(file does not exist yet)") . "\n\n";

$pdo->exec("DROP TABLE IF EXISTS users;");
$pdo->exec("DROP TABLE IF EXISTS content;");

$pdo->exec("
  CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    password_hash TEXT NOT NULL,
    bio TEXT,
    is_paid INTEGER NOT NULL DEFAULT 0,
    is_featured INTEGER NOT NULL DEFAULT 0,
    is_admin INTEGER NOT NULL DEFAULT 0,
    is_active INTEGER NOT NULL DEFAULT 1,
    created_at TEXT NOT NULL
  );
");

$pdo->exec("
  CREATE TABLE content (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    body TEXT NOT NULL,
    is_premium INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL
  );
");

$adminPass = password_hash("admin123", PASSWORD_DEFAULT);
$stmt = $pdo->prepare("
  INSERT INTO users (username, name, password_hash, bio, is_paid, is_featured, is_admin, is_active, created_at)
  VALUES (:username, :name, :password_hash, :bio, 1, 0, 1, 1, :created_at)
");
$stmt->execute([
  ":username" => "admin",
  ":name" => "Admin",
  ":password_hash" => $adminPass,
  ":bio" => "Admin account",
  ":created_at" => date("c"),
]);

echo "Tables created.\n\n";
echo "Tables in DB:\n";
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;")->fetchAll();
foreach ($tables as $t) {
  echo "- " . $t["name"] . "\n";
}
echo "\nAdmin login: admin / admin123\n";
