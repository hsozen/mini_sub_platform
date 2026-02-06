<?php
// File: /public/login.php
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
    $res = auth_login(
      $pdo,
      (string)($_POST["username"] ?? ""),
      (string)($_POST["password"] ?? "")
    );

    if ($res["ok"]) {
      set_flash("success", "Logged in.");
      redirect("/index.php");
    } else {
      $error = (string)($res["error"] ?? "Login failed.");
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
  <title>Login to Platform</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary": "#0da5a3",
            "background-light": "#f6f8f8",
            "background-dark": "#102221",
          },
          fontFamily: { "display": ["Inter", "sans-serif"] },
          borderRadius: {"DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px"},
        },
      },
    }
  </script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display min-h-screen flex flex-col antialiased">

<nav class="w-full px-6 py-4 flex items-center justify-between absolute top-0 left-0 z-10">
  <div class="flex items-center gap-2 text-slate-900 dark:text-white">
    <div class="size-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
      <span class="material-symbols-outlined text-[20px]">layers</span>
    </div>
    <span class="font-bold text-sm tracking-tight hidden sm:block">Mini Subscription Content Platform</span>
  </div>
</nav>

<main class="flex-1 flex items-center justify-center p-4 sm:p-6 relative z-0">

  <div class="w-full max-w-[420px] bg-white dark:bg-[#182827] rounded-xl shadow-xl border border-slate-100 dark:border-slate-800 overflow-hidden relative">
    <div class="h-1.5 w-full bg-primary"></div>

    <div class="p-8 pt-10">
      <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight mb-2">Welcome back</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Please enter your details to sign in.</p>
      </div>

      <?php if ($flash && ($flash["type"] ?? "") === "success"): ?>
        <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30 flex items-start gap-3">
          <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-[20px] mt-0.5">check_circle</span>
          <div class="flex-1">
            <h3 class="text-sm font-semibold text-green-800 dark:text-green-300"><?= h((string)$flash["message"]) ?></h3>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30 flex items-start gap-3">
          <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-[20px] mt-0.5">error</span>
          <div class="flex-1">
            <h3 class="text-sm font-semibold text-red-800 dark:text-red-300">Login Failed</h3>
            <p class="text-sm text-red-600 dark:text-red-400 mt-1"><?= h($error) ?></p>
          </div>
        </div>
      <?php endif; ?>

      <form action="/login.php" class="space-y-5" method="POST">
        <input type="hidden" name="_csrf" value="<?= h(csrf_token($config)) ?>"/>

        <div class="space-y-1.5">
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="username">Username</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              <span class="material-symbols-outlined text-[20px]">person</span>
            </div>
            <input class="block w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-[#121f1e] text-slate-900 dark:text-white pl-10 pr-3 py-2.5 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors duration-200 ease-in-out placeholder:text-slate-400"
                   id="username" name="username" placeholder="Enter your username" type="text"
                   value="<?= h((string)($_POST["username"] ?? "")) ?>"/>
          </div>
        </div>

        <div class="space-y-1.5">
          <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="password">Password</label>
          </div>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              <span class="material-symbols-outlined text-[20px]">lock</span>
            </div>

            <input
              class="block w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-[#121f1e] text-slate-900 dark:text-white pl-10 pr-10 py-2.5 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors duration-200 ease-in-out placeholder:text-slate-400"
              id="password"
              name="password"
              placeholder="••••••••"
              type="password"
            />

            <button
              type="button"
              id="pw_toggle"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-primary transition-colors"
              aria-label="Show password"
            >
              <span class="material-symbols-outlined text-[20px]" id="pw_icon">visibility_off</span>
            </button>
          </div>
        </div>

        <button class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-[#0b8f8d] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200"
                type="submit">
          Login
        </button>
      </form>

      <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
        <p class="text-sm text-slate-600 dark:text-slate-400">
          Need an account?
          <a class="font-semibold text-primary hover:text-[#0b8f8d] transition-colors" href="/register.php">Register</a>
        </p>
        <p class="text-xs mt-3">
          <a class="text-slate-400 hover:text-primary transition-colors" href="/index.php">Back to Home</a>
        </p>
      </div>
    </div>
  </div>

  <div class="fixed bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-primary/5 to-transparent -z-10 pointer-events-none"></div>
</main>
<script>
  const pw = document.getElementById('password');
  const btn = document.getElementById('pw_toggle');
  const icon = document.getElementById('pw_icon');

  if (pw && btn && icon) {
    btn.addEventListener('click', () => {
      const isPw = pw.getAttribute('type') === 'password';
      pw.setAttribute('type', isPw ? 'text' : 'password');
      icon.textContent = isPw ? 'visibility' : 'visibility_off';
      btn.setAttribute('aria-label', isPw ? 'Hide password' : 'Show password');
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
