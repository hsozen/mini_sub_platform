<?php
// File: /api/register.php
declare(strict_types=1);

require __DIR__ . "/../app/bootstrap.php";
require_once __DIR__ . "/../app/auth/auth_actions.php";

$config = $GLOBALS["config"];
$pdo = $GLOBALS["pdo"];

$auth = auth();
if (!empty($auth["is_logged_in"])) {
  redirect("/index.php");
}

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (!csrf_check($config, $_POST["_csrf"] ?? null)) {
    $error = "Invalid form session. Please try again.";
  } else {
    $res = auth_register(
      $pdo,
      (string)($_POST["username"] ?? ""),
      (string)($_POST["fullname"] ?? ""),
      (string)($_POST["password"] ?? "")
    );

    if ($res["ok"]) {
      set_flash("success", "Account created.");
      redirect("/index.php");
    } else {
      $error = (string)($res["error"] ?? "Registration failed.");
    }
  }
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Register Account - Mini Subscription Content Platform</title>

  <link href="https://fonts.googleapis.com" rel="preconnect"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&amp;display=swap" rel="stylesheet"/>

  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary": "#0da5a3",
            "primary-hover": "#0b8f8d",
            "background-light": "#f6f8f8",
            "background-dark": "#102221",
            "surface-light": "#ffffff",
            "surface-dark": "#1a2c2c",
            "border-light": "#dbe6e5",
            "border-dark": "#2a3e3e",
            "text-main": "#111818",
            "text-main-dark": "#ffffff",
            "text-secondary": "#618989",
            "text-secondary-dark": "#94b8b8",
          },
          fontFamily: { "display": ["Inter", "sans-serif"] },
          borderRadius: { "DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px" },
          boxShadow: { "soft": "0 4px 20px -2px rgba(0, 0, 0, 0.05)" }
        },
      },
    }
  </script>

  <style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background-color: rgba(0, 0, 0, 0.1); border-radius: 20px; }
  </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display antialiased min-h-screen flex flex-col transition-colors duration-200">

<header class="w-full px-6 py-4 flex items-center justify-between border-b border-border-light dark:border-border-dark bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-md sticky top-0 z-10">
  <div class="flex items-center gap-3">
    <div class="size-8 flex items-center justify-center rounded-lg bg-primary text-white">
      <span class="material-symbols-outlined text-xl">layers</span>
    </div>
    <h2 class="text-text-main dark:text-text-main-dark text-lg font-bold tracking-tight hidden sm:block">Mini Sub Platform</h2>
  </div>
  <a class="text-sm font-medium text-text-secondary dark:text-text-secondary-dark hover:text-primary dark:hover:text-primary transition-colors" href="/index.php">
    Home
  </a>
</header>

<main class="flex-1 flex items-center justify-center p-4 sm:p-6">
  <div class="w-full max-w-[420px] bg-surface-light dark:bg-surface-dark rounded-2xl shadow-soft border border-border-light dark:border-border-dark overflow-hidden p-8 animate-in fade-in zoom-in duration-300">

    <div class="text-center mb-8">
      <h1 class="text-2xl font-bold text-text-main dark:text-text-main-dark mb-2">Create account</h1>
      <p class="text-text-secondary dark:text-text-secondary-dark text-sm">Join our community of creators today</p>
    </div>

    <?php if ($flash && ($flash["type"] ?? "") === "success"): ?>
      <div class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 p-3 flex gap-3 items-start">
        <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-[20px] mt-0.5">check_circle</span>
        <div class="text-sm text-green-800 dark:text-green-200">
          <p class="font-medium"><?= h((string)$flash["message"]) ?></p>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="mb-6 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 p-3 flex gap-3 items-start">
        <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-[20px] mt-0.5">error</span>
        <div class="text-sm text-red-800 dark:text-red-200">
          <p class="font-medium">Registration failed</p>
          <p class="text-xs opacity-90 mt-0.5"><?= h($error) ?></p>
        </div>
      </div>
    <?php endif; ?>

    <form action="/register.php" method="POST" class="space-y-5">
      <input type="hidden" name="_csrf" value="<?= h(csrf_token($config)) ?>"/>

      <div class="space-y-1.5">
        <label class="block text-sm font-medium text-text-main dark:text-text-main-dark" for="username">Username</label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-text-secondary dark:text-text-secondary-dark text-[20px]">person</span>
          </div>
          <input class="block w-full h-12 rounded-xl border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-main dark:text-text-main-dark placeholder:text-text-secondary/60 dark:placeholder:text-text-secondary-dark/60 pl-11 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200 outline-none sm:text-sm"
                 id="username" name="username" placeholder="Choose a username" type="text"
                 value="<?= h((string)($_POST["username"] ?? "")) ?>"/>
        </div>
      </div>

      <div class="space-y-1.5">
        <label class="block text-sm font-medium text-text-main dark:text-text-main-dark" for="fullname">Full Name</label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-text-secondary dark:text-text-secondary-dark text-[20px]">badge</span>
          </div>
          <input class="block w-full h-12 rounded-xl border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-main dark:text-text-main-dark placeholder:text-text-secondary/60 dark:placeholder:text-text-secondary-dark/60 pl-11 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200 outline-none sm:text-sm"
                 id="fullname" name="fullname" placeholder="Enter your full name" type="text"
                 value="<?= h((string)($_POST["fullname"] ?? "")) ?>"/>
        </div>
      </div>

      <div class="space-y-1.5">
        <label class="block text-sm font-medium text-text-main dark:text-text-main-dark" for="password">Password</label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-text-secondary dark:text-text-secondary-dark text-[20px]">lock</span>
          </div>
          <input class="block w-full h-12 rounded-xl border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark text-text-main dark:text-text-main-dark placeholder:text-text-secondary/60 dark:placeholder:text-text-secondary-dark/60 pl-11 pr-10 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200 outline-none sm:text-sm"
                 id="password" name="password" placeholder="Create a secure password" type="password"/>
          <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-text-secondary hover:text-primary transition-colors" type="button" aria-label="Toggle password visibility">
            <span class="material-symbols-outlined text-[20px]" id="pw_icon">visibility_off</span>
          </button>
        </div>
        <p class="text-xs text-text-secondary dark:text-text-secondary-dark pl-1">Must be at least 8 characters</p>
      </div>

      <div class="pt-2">
        <button class="w-full flex justify-center items-center gap-2 h-12 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold shadow-sm transition-all duration-200 hover:shadow-md active:scale-[0.98]"
                type="submit">
          <span>Create account</span>
          <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </button>
      </div>
    </form>

    <div class="mt-8 text-center border-t border-border-light dark:border-border-dark pt-6">
      <p class="text-sm text-text-secondary dark:text-text-secondary-dark">
        Already have an account?
        <a class="font-semibold text-primary hover:text-primary-hover hover:underline transition-all ml-1" href="/login.php">Login</a>
      </p>
    </div>
  </div>
</main>

<footer class="py-6 text-center">
  <p class="text-xs text-text-secondary/50 dark:text-text-secondary-dark/50">© 2023 Mini Subscription Content Platform. All rights reserved.</p>
</footer>

<div class="fixed top-0 left-0 w-full h-full pointer-events-none -z-10 overflow-hidden">
  <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-primary/5 rounded-full blur-[100px]"></div>
  <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-primary/5 rounded-full blur-[120px]"></div>
</div>

<script>
  // Toggle password visibility
  const pw = document.getElementById('password');
  const icon = document.getElementById('pw_icon');
  const btn = icon ? icon.closest('button') : null;

  if (btn && pw && icon) {
    btn.addEventListener('click', () => {
      const isPw = pw.getAttribute('type') === 'password';
      pw.setAttribute('type', isPw ? 'text' : 'password');
      icon.textContent = isPw ? 'visibility' : 'visibility_off';
    });
  }
</script>
<script>
  // Simple dark mode toggle logic for demonstration
  /* if (
    localStorage.theme === 'dark' ||
    (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
  ) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }*/
</script>
</body>
</html>
