<?php
// File: /api/account.php
declare(strict_types=1);

require __DIR__ . "/../app/bootstrap.php";

$config = $GLOBALS["config"];
$pdo = $GLOBALS["pdo"];
$auth = auth();

if (empty($auth["is_logged_in"])) {
  redirect("/login.php");
}

$userId = (int)($auth["user"]["id"] ?? 0);
$stmt = $pdo->prepare("SELECT id, username, name, bio, is_paid, is_admin, is_active, created_at FROM users WHERE id = :id LIMIT 1");
$stmt->execute([":id" => $userId]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$u) {
  // session is stale
  redirect("/login.php");
}

$tierLabel = ((int)$u["is_paid"] === 1) ? "Premium Tier" : "Free Tier";
$statusLabel = ((int)$u["is_active"] === 1) ? (((int)$u["is_paid"] === 1) ? "Active (Paid)" : "Active (Free)") : "Inactive";
$memberSince = !empty($u["created_at"]) ? date("F Y", strtotime((string)$u["created_at"])) : "Unknown";
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>My Account Details</title>

  <link href="https://fonts.googleapis.com" rel="preconnect"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet"/>
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
          },
          fontFamily: { "display": ["Inter", "sans-serif"] },
          borderRadius: {"DEFAULT":"0.5rem","lg":"1rem","xl":"1.5rem","full":"9999px"},
        },
      },
    }
  </script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display flex flex-col min-h-screen text-[#111818] dark:text-white antialiased selection:bg-primary/30">
<header class="sticky top-0 z-50 flex items-center justify-between border-b border-[#e2e8e8] dark:border-[#1e3a39] bg-white/90 dark:bg-[#102221]/90 backdrop-blur-md px-6 py-4 lg:px-10">
  <div class="flex items-center gap-3">
    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
      <span class="material-symbols-outlined text-2xl">grid_view</span>
    </div>
    <h2 class="text-xl font-bold tracking-tight text-[#111818] dark:text-white">Mini Sub</h2>
  </div>

  <nav class="hidden md:flex flex-1 items-center justify-end gap-8">
    <a class="text-sm font-medium text-[#111818] dark:text-gray-200 hover:text-primary transition-colors" href="/index.php">Dashboard</a>
    <a class="text-sm font-medium text-[#111818] dark:text-gray-200 hover:text-primary transition-colors" href="/content_free.php">Explore</a>
    <div class="h-4 w-px bg-gray-300 dark:bg-gray-700"></div>
    <div class="flex items-center gap-6">
      <a class="text-sm font-bold text-primary" href="/account.php">Account</a>
      <a class="group flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white transition-all hover:bg-primary/90 hover:shadow-lg hover:shadow-primary/20"
         href="/logout.php">
        <span class="truncate">Logout</span>
        <span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-1">logout</span>
      </a>
    </div>
  </nav>

  <button class="md:hidden p-2 text-gray-600 dark:text-gray-300" type="button">
    <span class="material-symbols-outlined">menu</span>
  </button>
</header>

<main class="flex-1 flex flex-col items-center justify-center p-4 sm:p-6 lg:p-10">
  <div class="w-full max-w-[640px] flex flex-col gap-6">
    <div class="text-center sm:text-left mb-2">
      <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#111818] dark:text-white mb-2">My Account</h1>
      <p class="text-[#618989] dark:text-[#8ba7a7]">Manage your profile and subscription details.</p>
    </div>

    <div class="bg-white dark:bg-[#162a29] rounded-xl border border-[#e2e8e8] dark:border-[#1e3a39] shadow-sm overflow-hidden">
      <div class="px-6 py-8 sm:px-8 border-b border-[#f0f4f4] dark:border-[#1e3a39] flex flex-col sm:flex-row items-center gap-6">
        <div class="relative group">
          <div class="h-24 w-24 rounded-full bg-gradient-to-br from-[#dbe6e5] to-[#c1d1d0] dark:from-[#2a4544] dark:to-[#1e3a39] p-1 shadow-inner">
            <div class="h-full w-full rounded-full bg-white/40 dark:bg-black/20 border-2 border-white dark:border-[#162a29] flex items-center justify-center">
              <span class="material-symbols-outlined text-3xl text-[#618989]">person</span>
            </div>
          </div>
          <button class="absolute bottom-0 right-0 bg-primary text-white p-1.5 rounded-full shadow-lg hover:bg-primary/90 transition-colors border-2 border-white dark:border-[#162a29]" type="button">
            <span class="material-symbols-outlined text-[16px] block">edit</span>
          </button>
        </div>

        <div class="text-center sm:text-left">
          <h3 class="text-xl font-bold text-[#111818] dark:text-white"><?= h((string)$u["name"]) ?></h3>
          <p class="text-[#618989] dark:text-[#8ba7a7] text-sm mt-1">Member since <?= h($memberSince) ?></p>
          <div class="mt-3 inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary ring-1 ring-inset ring-primary/20">
            <?= h($tierLabel) ?>
          </div>
        </div>

        <div class="sm:ml-auto">
          <button class="text-sm font-medium text-primary hover:text-primary/80 hover:underline" type="button">Edit Profile</button>
        </div>
      </div>

      <div class="divide-y divide-[#f0f4f4] dark:divide-[#1e3a39]">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-5 sm:px-8 hover:bg-[#fafcfc] dark:hover:bg-[#1a302f] transition-colors">
          <div class="flex items-center gap-2 text-[#618989] dark:text-[#8ba7a7] font-medium text-sm">
            <span class="material-symbols-outlined text-[18px]">person</span> Username
          </div>
          <div class="sm:col-span-2 text-[#111818] dark:text-gray-200 text-sm font-medium"><?= h((string)$u["username"]) ?></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-5 sm:px-8 hover:bg-[#fafcfc] dark:hover:bg-[#1a302f] transition-colors">
          <div class="flex items-center gap-2 text-[#618989] dark:text-[#8ba7a7] font-medium text-sm">
            <span class="material-symbols-outlined text-[18px]">badge</span> Full Name
          </div>
          <div class="sm:col-span-2 text-[#111818] dark:text-gray-200 text-sm font-medium"><?= h((string)$u["name"]) ?></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-5 sm:px-8 hover:bg-[#fafcfc] dark:hover:bg-[#1a302f] transition-colors">
          <div class="flex items-center gap-2 text-[#618989] dark:text-[#8ba7a7] font-medium text-sm self-start">
            <span class="material-symbols-outlined text-[18px]">description</span> Bio
          </div>
          <div class="sm:col-span-2 text-[#111818] dark:text-gray-200 text-sm leading-relaxed">
            <?= h((string)($u["bio"] ?? "")) ?>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-5 sm:px-8 hover:bg-[#fafcfc] dark:hover:bg-[#1a302f] transition-colors">
          <div class="flex items-center gap-2 text-[#618989] dark:text-[#8ba7a7] font-medium text-sm">
            <span class="material-symbols-outlined text-[18px]">verified</span> Status
          </div>
          <div class="sm:col-span-2 flex items-center justify-between">
            <span class="text-[#111818] dark:text-gray-200 text-sm font-medium"><?= h($statusLabel) ?></span>
            <a class="text-xs font-bold text-primary hover:text-primary/80 border border-primary/30 rounded px-2 py-1 hover:bg-primary/5 transition-colors"
               href="/content_premium.php">Go to Premium</a>
          </div>
        </div>

        <?php if ((int)$u["is_admin"] === 1): ?>
          <div class="px-6 py-5 sm:px-8">
            <a class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:underline" href="/admin.php">
              <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
              Admin Panel
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="text-center px-4">
      <p class="text-xs text-[#618989] dark:text-[#6c8f8f] bg-[#f0f4f4] dark:bg-[#162a29] inline-block py-2 px-4 rounded-full">
        <span class="material-symbols-outlined text-[14px] align-text-bottom mr-1">info</span>
        Premium is a mock subscription. Admin toggles your paid status.
      </p>
    </div>
  </div>
</main>

<footer class="mt-auto border-t border-[#e2e8e8] dark:border-[#1e3a39] py-8 text-center">
  <p class="text-xs text-[#618989] dark:text-[#587a7a]">© 2023 Mini Subscription Content Platform. All rights reserved.</p>
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
