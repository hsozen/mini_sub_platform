<?php
// File: /api/content_premium.php
declare(strict_types=1);

require __DIR__ . "/../app/bootstrap.php";

$config = $GLOBALS["config"];
$pdo = $GLOBALS["pdo"];
$auth = auth();

$isLoggedIn = !empty($auth["is_logged_in"]);
$userId = (int)($auth["user"]["id"] ?? 0);

$isPaid = false;
$items = []; // IMPORTANT: avoid undefined variable warnings

if ($isLoggedIn && $userId > 0) {
  $stmt = $pdo->prepare("SELECT is_paid, is_active FROM users WHERE id = :id LIMIT 1");
  $stmt->execute([":id" => $userId]);
  $u = $stmt->fetch(PDO::FETCH_ASSOC);

  $isPaid = !empty($u) && (int)($u["is_active"] ?? 0) === 1 && (int)($u["is_paid"] ?? 0) === 1;
}

if ($isPaid) {
  $stmt = $pdo->prepare("
    SELECT id, title, body, created_at
    FROM content
    WHERE is_premium = 1
    ORDER BY id DESC
    LIMIT 50
  ");
  $stmt->execute();
  $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title><?= $isPaid ? "Premium Content" : "Premium Content Locked - Mini Sub" ?></title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
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
            "surface-light": "#ffffff",
            "surface-dark": "#162a29",
          },
          fontFamily: { "display": ["Inter", "sans-serif"] },
          borderRadius: {"DEFAULT":"0.5rem","lg":"1rem","xl":"1.5rem","full":"9999px"},
        },
      },
    }
  </script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 flex flex-col min-h-screen antialiased selection:bg-primary/30">

<header class="sticky top-0 z-50 w-full border-b border-slate-200 dark:border-slate-800 bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-md">
  <div class="px-4 sm:px-10 h-16 flex items-center justify-between max-w-7xl mx-auto">
    <div class="flex items-center gap-3">
      <div class="flex items-center justify-center size-8 rounded-lg bg-primary/10 text-primary">
        <span class="material-symbols-outlined text-xl">diamond</span>
      </div>
      <span class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">Mini Sub</span>
    </div>

    <nav class="hidden md:flex items-center gap-8">
      <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors" href="/index.php">Home</a>
      <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors" href="/content_free.php">Free Content</a>
      <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors" href="/content_premium.php">Premium</a>
      <?php if ($isLoggedIn): ?>
        <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors" href="/account.php">My Account</a>
      <?php endif; ?>
    </nav>

    <div class="flex items-center gap-3">
      <?php if (!$isLoggedIn): ?>
        <a class="hidden sm:flex items-center justify-center h-9 px-4 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
           href="/login.php">Log in</a>
        <a class="flex items-center justify-center h-9 px-4 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-all shadow-sm shadow-primary/20"
           href="/register.php">Sign up</a>
      <?php else: ?>
        <a class="flex items-center justify-center h-9 px-4 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
           href="/account.php">Account</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<?php if (!$isPaid): ?>
  <main class="flex-grow flex items-center justify-center p-4 sm:p-6">
    <div class="w-full max-w-[520px] bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-black/20 rounded-2xl overflow-hidden">
      <div class="flex flex-col items-center pt-12 pb-6 px-8 text-center">
        <div class="size-20 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mb-6 ring-1 ring-slate-100 dark:ring-slate-700">
          <span class="material-symbols-outlined text-[40px] text-slate-400 dark:text-slate-500">lock</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-3">Premium is locked</h2>
        <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 max-w-sm mx-auto leading-relaxed">
          This library unlocks after your account is marked as <span class="font-semibold">Active</span> and <span class="font-semibold">Paid</span>.
        </p>
        <div class="mt-4 px-4 py-2 bg-primary/5 dark:bg-primary/10 rounded-lg border border-primary/10 dark:border-primary/20">
          <p class="text-xs text-primary/80 font-medium">
            For this MVP, an admin toggles access manually.
          </p>
        </div>
      </div>

      <div class="bg-slate-50 dark:bg-[#122322] px-8 py-8 flex flex-col sm:flex-row gap-4 justify-center items-center border-t border-slate-100 dark:border-slate-800/50">
        <?php if (!$isLoggedIn): ?>
          <a class="w-full sm:w-auto min-w-[140px] h-11 flex items-center justify-center gap-2 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary/90 hover:shadow-lg hover:shadow-primary/25 transition-all"
             href="/login.php">
            <span class="material-symbols-outlined text-[18px]">login</span>
            <span>Log in</span>
          </a>
        <?php else: ?>
          <a class="w-full sm:w-auto min-w-[140px] h-11 flex items-center justify-center gap-2 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary/90 hover:shadow-lg hover:shadow-primary/25 transition-all"
             href="/account.php">
            <span class="material-symbols-outlined text-[18px]">person</span>
            <span>My Account</span>
          </a>
        <?php endif; ?>

        <a class="w-full sm:w-auto min-w-[140px] h-11 flex items-center justify-center gap-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-all"
           href="/index.php">
          <span>Go Home</span>
        </a>
      </div>
    </div>
  </main>
<?php else: ?>
  <main class="flex-grow p-6 max-w-5xl mx-auto w-full">
    <div class="mb-8">
      <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Premium Content</h1>
      <p class="text-slate-600 dark:text-slate-300 mt-2">Welcome to the premium library.</p>
    </div>

    <div class="space-y-4">
      <?php if (!$items): ?>
        <div class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl p-6">
          <div class="flex items-start gap-3">
            <div class="mt-0.5 text-primary">
              <span class="material-symbols-outlined">info</span>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-bold text-slate-900 dark:text-white">Premium access is active</h3>
              <p class="text-sm text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                You are successfully upgraded, but there are no premium posts published yet.
                If you are the admin, add premium items to the <span class="font-semibold">content</span> table with <span class="font-semibold">is_premium = 1</span>.
              </p>
              <div class="mt-4 flex flex-col sm:flex-row gap-3">
                <a class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-all"
                   href="/content_free.php">
                  Browse Free Content
                </a>
                <?php if (!empty($auth["user"]["is_admin"])): ?>
                  <a class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-all"
                     href="/admin.php">
                    Go to Admin
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php foreach ($items as $row): ?>
        <?php
          $title = (string)($row["title"] ?? "");
          $body = (string)($row["body"] ?? "");
          $created = (string)($row["created_at"] ?? "");
          $dateLabel = $created ? date("M d, Y", strtotime($created)) : "";
        ?>
        <article class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl p-5">
          <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white"><?= h($title) ?></h2>
            <?php if ($dateLabel): ?>
              <span class="text-xs text-slate-500 dark:text-slate-400"><?= h($dateLabel) ?></span>
            <?php endif; ?>
          </div>
          <p class="text-sm text-slate-600 dark:text-slate-300 mt-2 leading-relaxed"><?= h($body) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </main>
<?php endif; ?>

<footer class="border-t border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark py-8">
  <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
    <p class="text-sm text-slate-500 dark:text-slate-400">© 2024 Mini Subscription Content Platform</p>
    <div class="flex flex-wrap justify-center gap-6">
      <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Privacy Policy</a>
      <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Terms of Service</a>
      <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Support</a>
    </div>
  </div>
</footer>
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
