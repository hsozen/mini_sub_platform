<?php
declare(strict_types=1);

function migrate_and_seed(PDO $pdo): void {
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      username TEXT UNIQUE NOT NULL,
      name TEXT NOT NULL,
      password_hash TEXT NOT NULL,
      bio TEXT NOT NULL DEFAULT '',
      is_paid INTEGER NOT NULL DEFAULT 0,
      is_featured INTEGER NOT NULL DEFAULT 0,
      is_admin INTEGER NOT NULL DEFAULT 0,
      is_active INTEGER NOT NULL DEFAULT 1,
      created_at TEXT NOT NULL
    );
  ");

  $pdo->exec("
    CREATE TABLE IF NOT EXISTS content (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      title TEXT NOT NULL,
      body TEXT NOT NULL,
      is_premium INTEGER NOT NULL DEFAULT 0,
      created_at TEXT NOT NULL
    );
  ");

  // seed admin if missing
  $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin' LIMIT 1");
  $stmt->execute();
  if (!$stmt->fetch()) {
    $hash = password_hash("admin12345", PASSWORD_DEFAULT);
    $now = date("c");

    $ins = $pdo->prepare("
      INSERT INTO users (username, name, password_hash, bio, is_paid, is_featured, is_admin, is_active, created_at)
      VALUES ('admin', 'Admin', :hash, '', 1, 1, 1, 1, :now)
    ");
    $ins->execute([":hash" => $hash, ":now" => $now]);
  }

  // seed content if empty
  $c = $pdo->query("SELECT COUNT(*) AS c FROM content")->fetch();
  if ((int)($c["c"] ?? 0) === 0) {
    $now = date("c");
    $ins = $pdo->prepare("INSERT INTO content (title, body, is_premium, created_at) VALUES (:t,:b,:p,:c)");

    $ins->execute([":t"=>"Welcome (Free)", ":b"=>"This is a free post seeded on startup.", ":p"=>0, ":c"=>$now]);
    $ins->execute([":t"=>"Premium Starter", ":b"=>"This is a premium post. Toggle paid to access.", ":p"=>1, ":c"=>$now]);
  }
}
