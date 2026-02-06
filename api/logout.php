<?php
// File: /api/logout.php
declare(strict_types=1);

require __DIR__ . "/../app/bootstrap.php";

session_destroy();
redirect("/login.php");
