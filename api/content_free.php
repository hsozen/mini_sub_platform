<?php
// File: /public/content_free.php
declare(strict_types=1);

require __DIR__ . "/../app/bootstrap.php";

$config = $GLOBALS["config"];
$pdo = $GLOBALS["pdo"];
$auth = auth();

$stmt = $pdo->prepare("
  SELECT id, title, body, created_at
  FROM content
  WHERE is_premium = 0
  ORDER BY id DESC
  LIMIT 50
");
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Free Content - Mini Subscription</title>

  <link href="https://fonts.googleapis.com" rel="preconnect"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>

  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary": "#0da5a3",
            "primary-dark": "#0a8583",
            "background-light": "#f6f8f8",
            "background-dark": "#102221",
            "surface-light": "#ffffff",
            "surface-dark": "#162e2c",
            "text-main": "#111818",
            "text-secondary": "#618989",
            "border-light": "#E2E8F0",
            "border-dark": "#1f403e",
          },
          fontFamily: { "display": ["Inter", "sans-serif"] },
          borderRadius: {"DEFAULT":"0.5rem","lg":"1rem","xl":"1.5rem","full":"9999px"},
        },
      },
    }
  </script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-text-main dark:text-gray-100 min-h-screen flex flex-col overflow-x-hidden antialiased">
<div class="relative flex flex-col w-full min-h-screen">
  <div class="layout-container flex h-full grow flex-col">
    <header class="sticky top-0 z-50 w-full bg-white/90 dark:bg-surface-dark/90 backdrop-blur-md border-b border-solid border-border-light dark:border-border-dark px-6 py-3 transition-colors duration-200">
      <div class="max-w-[960px] mx-auto flex items-center justify-between whitespace-nowrap">
        <div class="flex items-center gap-4 text-text-main dark:text-white">
          <div class="size-8 flex items-center justify-center text-primary">
            <svg class="size-6" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
              <g>
                <path clip-rule="evenodd" d="M24 0.757355L47.2426 24L24 47.2426L0.757355 24L24 0.757355ZM21 35.7574V12.2426L9.24264 24L21 35.7574Z" fill="currentColor" fill-rule="evenodd"></path>
              </g>
            </svg>
          </div>
          <h2 class="text-lg font-bold leading-tight tracking-[-0.015em]">Mini Subscription</h2>
        </div>

        <div class="flex flex-1 justify-end gap-8">
          <div class="hidden md:flex items-center gap-6">
            <a class="text-text-main dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary transition-colors" href="/index.php">Home</a>
            <a class="text-text-main dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary transition-colors" href="/content_free.php">Free Content</a>
            <a class="text-text-main dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary transition-colors" href="/content_premium.php">Premium</a>
            <?php if (!empty($auth["is_logged_in"])): ?>
              <a class="text-text-main dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary transition-colors" href="/account.php">My Account</a>
            <?php endif; ?>
          </div>

          <?php if (empty($auth["is_logged_in"])): ?>
            <a class="flex min-w-[84px] items-center justify-center overflow-hidden rounded-xl h-10 px-5 bg-primary hover:bg-primary-dark transition-colors text-white text-sm font-bold leading-normal tracking-[0.015em]"
               href="/login.php">Sign In</a>
          <?php else: ?>
            <a class="flex min-w-[84px] items-center justify-center overflow-hidden rounded-xl h-10 px-5 bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark hover:bg-gray-50 dark:hover:bg-surface-dark/80 transition-colors text-text-main dark:text-white text-sm font-bold leading-normal tracking-[0.015em]"
               href="/account.php">Account</a>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <main class="flex-1 flex flex-col items-center py-10 px-4 sm:px-6">
      <div class="w-full max-w-[960px] flex flex-col gap-8">
        <div class="flex flex-col gap-3">
          <h1 class="text-text-main dark:text-white text-4xl font-black leading-tight tracking-[-0.033em]">Free Content</h1>
          <p class="text-text-secondary dark:text-gray-400 text-lg font-normal leading-normal max-w-2xl">
            Explore our curated collection of free articles, resources, and insights available to everyone.
          </p>
        </div>

        <div class="w-full rounded-xl bg-[#0da5a3]/10 dark:bg-[#0da5a3]/20 border border-[#0da5a3]/20 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex gap-4 items-start">
            <div class="text-primary mt-1">
              <span class="material-symbols-outlined text-[28px]">stars</span>
            </div>
            <div class="flex flex-col gap-1">
              <h3 class="text-text-main dark:text-white text-base font-bold leading-tight">Unlock Full Access</h3>
              <p class="text-text-secondary dark:text-gray-300 text-sm font-normal leading-normal">
                Premium access is a mock toggle in this MVP. Ask the admin to activate your account.
              </p>
            </div>
          </div>
          <a class="shrink-0 text-primary hover:text-primary-dark font-bold text-sm flex items-center gap-1 group" href="/content_premium.php">
            Learn more
            <span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-1">arrow_forward</span>
          </a>
        </div>

        <div class="flex flex-col gap-0 border-t border-border-light dark:border-border-dark mt-4">
          <?php if (!$items): ?>
            <div class="py-10 text-sm text-text-secondary dark:text-gray-400">No free content yet.</div>
          <?php endif; ?>

          <?php foreach ($items as $row): ?>
            <?php
              $title = (string)($row["title"] ?? "");
              $body = (string)($row["body"] ?? "");
              $created = (string)($row["created_at"] ?? "");
              $dateLabel = $created ? date("M d, Y", strtotime($created)) : "";
            ?>
            <article class="group flex flex-col sm:flex-row gap-6 py-8 border-b border-border-light dark:border-border-dark hover:bg-gray-50 dark:hover:bg-surface-dark/50 transition-colors rounded-lg px-2 -mx-2">
              <div class="flex-1 flex flex-col gap-2">
                <div class="flex items-center gap-2 mb-1">
                  <span class="bg-gray-100 dark:bg-gray-800 text-text-secondary dark:text-gray-400 text-xs px-2 py-1 rounded-full font-medium uppercase tracking-wide">Free</span>
                  <?php if ($dateLabel): ?>
                    <span class="text-text-secondary dark:text-gray-500 text-xs font-normal"><?= h($dateLabel) ?></span>
                  <?php endif; ?>
                </div>
                <h2 class="text-text-main dark:text-white text-xl font-bold leading-tight group-hover:text-primary transition-colors cursor-default">
                  <?= h($title) ?>
                </h2>
                <p class="text-text-secondary dark:text-gray-400 text-sm sm:text-base font-normal leading-relaxed line-clamp-2">
                  <?= h($body) ?>
                </p>
                <div class="mt-2">
                  <span class="text-text-main dark:text-gray-300 text-sm font-medium flex items-center gap-1">
                    Read Article
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                  </span>
                </div>
              </div>

              <div class="w-full sm:w-48 aspect-[3/2] sm:aspect-square rounded-lg overflow-hidden shrink-0 bg-gray-100 dark:bg-gray-800 relative">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: linear-gradient(135deg, rgba(13,165,163,0.18), rgba(0,0,0,0));"></div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <div class="flex justify-center mt-4 mb-12">
          <button class="flex min-w-[120px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-6 border border-border-light dark:border-border-dark bg-white dark:bg-surface-dark text-text-main dark:text-white text-sm font-medium hover:bg-gray-50 dark:hover:bg-surface-dark/80 transition-colors" type="button">
            Load More
          </button>
        </div>
      </div>
    </main>
  </div>
</div>
<script>
  // Simple dark mode toggle logic for demonstration
  /* if (
    localStorage.theme === 'dark' ||
    (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
  ) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  } */
</script>
</body>
</html>
