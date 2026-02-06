<?php
// File: /app/middleware.php
declare(strict_types=1);

function require_login(): void {
  if (!isset($_SESSION["user"])) {
    set_flash("error", "Please log in.");
    redirect("/login.php");
  }
}

function require_admin(): void {
  require_login();
  if (empty($_SESSION["user"]["is_admin"])) {
    set_flash("error", "Admin access required.");
    redirect("/index.php");
  }
}
