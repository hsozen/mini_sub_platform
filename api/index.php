<?php
// File: /api/index.php
declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

/**
 * MVP placeholder data.
 * Later we will replace these arrays with real DB queries:
 * - Newest Users: SELECT ... ORDER BY created_at DESC LIMIT 6
 * - Featured Users: SELECT ... WHERE is_featured=1 AND is_active=1 ORDER BY created_at DESC LIMIT 6
 */
$newestUsers = [
  [
    "name" => "Alex Rivera",
    "username" => "arivera",
    "bio" => "Digital artist sharing daily sketches and process videos. I love cyberpunk themes.",
    "avatar" => "https://lh3.googleusercontent.com/aida-public/AB6AXuAXGMcU_KtcOpXgnXlKxe_HiU9_rnX2drhE0MyPkpnc_sPeZ8QvzpAqM2PiBmo_Wmy-aV0YOP3yHBlgDBuOtgyWK0IAu6UL0Z-S-nx-sRNvcmYBqacwLEHTaJZgdOQ4Clp0ay5SbHY3kdZl_Yo7cQsZEHwXXIuPwLBtnajPFvq-rf9hVDEQ0lMEhoaAUKs4H2MsM-A8Jpv88Pwlzpg0tCAcMydzqNtmmlX46ANeYaCe9Gn2P7M1_3qLnaTuAhXwF1vHuhJ-4xgP27s",
    "featured" => false,
  ],
  [
    "name" => "Jordan Lee",
    "username" => "jlee",
    "bio" => "Tech reviewer and vlogger. Unboxing the latest gadgets and giving honest opinions.",
    "avatar" => "https://lh3.googleusercontent.com/aida-public/AB6AXuASTxIrVxg10rwrKjmv7mcH-nV1WD_nhzU5v7l_jQHPqRmN2suhoEZ-p5N1n3_wcnsToHvribffbdIW5HnmdD98LZSr-OfdTPHb77hKSTDi8rzqPZOv-EN94Pfad2_sLD1MsDJrV7S9lJNN483fkMUmRXyJp6OqU0QmF6snH4f4UDNDmw-_IJr-xrYUIZve2qGw6YgA9T-Q_9pQZegMwaigpa66GaENcsiQyFfkvM10Db0meOlIWZYbmPrnuCZl_PbWdmZB8A5E6Ow",
    "featured" => false,
  ],
  [
    "name" => "Casey Smith",
    "username" => "csmith",
    "bio" => "Musician and songwriter. Join my journey composing indie folk music.",
    "avatar" => "https://lh3.googleusercontent.com/aida-public/AB6AXuBDaNCmci_yJH8j-ZUOaWqPWCCgN68TgDpGvq6bvIj9thgA-SlhkhJpx6kDbCTv7kxxShfgp3Pgngs2mcc_27WxhIBXv5swNvnDUl76LkgNM2vX-pHQgOR0AFznYtYdf-T2SIgJBsMJiE9Nl8E1pyKmJGG2FjI_H8WRGA1UKR_s8CrvDkdzooPvr2S7mTerEdUR1G0YHuBRaRWfsXN6iqfdnI0kfDlhVwZcoEgdlYYEdDDG2vrvRQfDX2dicKhD8527oh5viJm_kkI",
    "featured" => false,
  ],
];

$featuredUsers = [
  [
    "name" => "Taylor Doe",
    "username" => "tdoe",
    "bio" => "Travel photographer exploring the hidden gems of the world. Prints available for subs!",
    "avatar" => "https://lh3.googleusercontent.com/aida-public/AB6AXuATwx5PmCwhPdHu2dfTumf0vBxnLhswb0XEBa25txZoQ6AP8jhKV_jzf-ZBKlrRtrSdOLXVjaU562FppsKqwmduOiiftXbGW0KVJ5I7hQm9n2PveOyF46UQZ-kszeXf362Wf1z8svobNtlKJlyUIVKcghGILhKL-wok2GkkRiKaY_g0a2TQCxjDQj-5bX8gosO_cTbNB6GKGSYKkkoOsb5tdKfrheMWJmugILQig-wj5ijxIFElILrXvSNIxGPG4a8KZSoTuORFSGw",
    "featured" => true,
    "thumbs" => [
      "https://lh3.googleusercontent.com/aida-public/AB6AXuDYtNjpyGD_BQLIBWyGemSUmKAV9QtrCNeT0I1Aazu_wqu8cqFyjcpoycl_EqKtwA8YVsblnlaLjrbmxI3C_O4MY3CWaJMdD_ot8uHc-isfbUtP6iQbaapuZ4LCYPQ4F4BFBTSKKaUDy-cKa3lgFLMgfEg6ZuMMBMBfzLuBwvgw6_eZ6OK5Ej3F0Dy50lIYOnEiYtCfNUpUVBvCc_Y9X9c2U2rtwxU68V00-zZlszd5T98pEVLeS8qRKxuLTn8wWOD49p3mK1QDQ-g",
      "https://lh3.googleusercontent.com/aida-public/AB6AXuAA9H9PJMmJy4pOlwT2g9nW9fDU5xxRfHnym08Ny4blaP2u_TSeU9cDFxx_Fj1PVFzvioe6ysaxPnB8DMX-Xp68CEnOtLBQv5Jsh_U7iSMySCtqIPze6fyWoiSKViGnXPphECsjmgDJbH0QMk6hdCxRv-t7E2PVM0lT21yNiCmz4BsYEUYIWIKIjjW4vapo58LEn7ocowePBRXw89KDT_C0JOX4F_yrU3Z4E_tRW4iRZdCF5ZN_c8kGPFNbSLLQE6SAQ5jptJ4Q-2o",
      "https://lh3.googleusercontent.com/aida-public/AB6AXuDZcWaNUUT3XkwCJbDvxVDSngo3PPpEqvK9n59wfLLhtflC6koe6qQrkH6dOLb7y5d9cv_o-31klGJH9MEGTVwwttsayCTRPOwKAtQbQT5xPalDzRXwclcVCfpbbuWmuzy86Kbv-UIn73mk54-FkkCSOyK-tDzSTAZ6YT5HGyq5NGh82aJ7eiHJdN0p2b85CnXHvC0LKNiusYrFO0Mqxxaq3bY4I9-lJ08Df-OMcVK7z8ixxCKghctVSb22MAfY_X2F5JfS5eyOk80",
    ],
  ],
  [
    "name" => "Sarah Jenkins",
    "username" => "sarahj_food",
    "bio" => "Culinary expert sharing exclusive recipes and cooking tips. Let's make magic in the kitchen.",
    "avatar" => "https://lh3.googleusercontent.com/aida-public/AB6AXuBxQvGmMydgTwHCkc_9zLRHL5jFAC8oW-Jz7USRNG3BAZGGZcRJ3boSU8eFEHdSX8xMMCPlFhoRMot0wlnTaQBWo1UXIZEMugN7h_VPkpLhFHc6l0bgtOB-oFpjuFKdGXHyRJd0Jpt8X7AdxuNUW9IwaXkCGSHSI0ScsPgjaY_w1x64ELahNCtyNChLl1gpHA-bPI0ODnmgGi1J4inFDMWJxERaMNDtQNeSnDCcb7vqV08auTeUNR-n8rZcxBkM7ne8EG9U-HtUEy0",
    "featured" => true,
    "thumbs" => [
      "https://lh3.googleusercontent.com/aida-public/AB6AXuDukNfkqtEszvQh9Walm3SZmkBORUNbXSahfNzu83skTdYPPBxtKajakxpCtEsG5wWspLKVUPY4kb6gE_eFlNFxUQIXV78Rb6RF4MZ-j6QRii1X6UIp3NW4tM5pZtmeeSufffnZMzCH94Qc0Ubqg4qHx3PZfTI8oZTYrg1ygSnNgtWZAuRGxw2wYszYRh6LbXMbSauRpJUEn_rlIkOAuaVtlwcypdkskPym9mBWxPbDUN6_RjEVk-ysusoZEzxqaE45rBMaVrTcjDw",
      "https://lh3.googleusercontent.com/aida-public/AB6AXuAtAyqVexRVialE2OCi2vUthYenDzwTpSYx-FMQXsF_AqC4Yi2UMqxJVe4ZBuHRI7-Yw_j1Ths8Eaplvu15soLBM5J5ja25f3QmXhFQQtsqcWOlfmWsHWKKWN8yiTo3t6Tnabq_b5vjzp8FhzlVxWNniSKqvxjdtLpdhz1uBD0hJA4KyD3nw07B63FqHYmF72KMCmq7pNBbO0ivyodLp93vKgDkoAKhUD6hMq0xVpHPc8PnsCmlhbfOHyNIC0Ssg-tyPsBASlHAFZ0",
      "https://lh3.googleusercontent.com/aida-public/AB6AXuBCIGoNSUfPCTI35ty6_P_Nuw8YO9fLhOSGCAyWYAAfSSqrnmcwRFzAiO7h3_VqESc8OcHsZUyEUvMRxbx2yrNGkO63ZxHWRFRFWNMGhJSxVaap18jAns-Q16FYEwsG3CELN30ikHb6jbYwxpyxKKxD4l40uvkD8CoDPPdtqgTRwDnvZedudgsd9I49r9u4QW-lLhkIJuoVO5wx2oP1ggz_qoizvokFZ9yiFgdR9luT6fvcx-u0snETqJ8F2dGkriLXNnVPO_VwKco",
    ],
  ],
  [
    "name" => "Michael Brown",
    "username" => "mike_codes",
    "bio" => "Software engineer teaching Python and Web Development. Coding challenges every week.",
    "avatar" => "https://lh3.googleusercontent.com/aida-public/AB6AXuCtPCSCDWICponzJENvInhEWqKZamH-zSg0eL5pkNEgK6lcmif3gfYkTiftHe7ciqxVivOuy5vIli6yLveMLf9jM6FvAmk-s35vPIiELlDtEvCE-0X6Ug9uCfKMYys-WK4Q2yroo-igrFCw6abB5oJU2nGdFHhYUtFNbbW22GNCwUw_Ip5ACvLLG2Yn40rVToDkS0IfW7gnhigF4KRANMdFiHbCu5A0NCgeI6Iq0UaNNdjwy_oHH3NhYibToPQ1xJPE8bk_9OQYTRg",
    "featured" => true,
    "thumbs" => [
      "https://lh3.googleusercontent.com/aida-public/AB6AXuDPv9TwG_KyPj5Og0drwSqN4o_CFLOAkFelInTCURyiPEC1Q3c0pufMoXy9_3d-0FDHJs6rcfBmTB9p983eCjRbOAp7hxtADYdmu0vJW6EFTq1LNg0l5n4DZRyKtTLfeBuofp6rwKdYBopMrpCawMpJbiCGPCvzdj2jleNck_QDW5X4CFdeLXTEB5YQjSZrFNLHaPcvUZUDtYdW9iWQhyz9ue26PY2gaUFfJ2m06iIDBFJBGv-gU46H5EivNVRL38Sm54yo-ggK01c",
      "https://lh3.googleusercontent.com/aida-public/AB6AXuB0ipmI_6r_3oPv2FXxLOt2YGiVG2wEbflY2Paof2jgN9J9ymxkKoDtJiNmVJiPBl9xhBFG-vOiwkKhRg668eXj4iM6dLDnmnCs-9HQvRMyhQnthtEdl-t1M6TdKBWc2DDW7bWwX1DEyZtTBPSS2awVwyiIIaN18UiP-F7Vht-K15QBLTa84K-5YBVgJPrw1dB9Ee9AjopjoMJbmqtYLLW50hwOiRN6BBZNC_GunJtnOKsLYQNCwMi1uGbl9d32nbs2M0QoL5Clgn8",
      "https://lh3.googleusercontent.com/aida-public/AB6AXuA638sCez1SZDJubz9DGMfaqYjRVGTmDwmlfSlYJBzm4c-mYaIZiOT0DmOfFFuKAzZgdDxTX0fr9p8NPYD3FdsSv4tV9rrU0p0-fhiOWW-FwVoinJU8cafl7n0WSt5jqq75SZ3gdH6l0U36YQUhtOVmGiREQmsyITibmK1eqISx0qrslS4YMx3mareY_LgeoONB0KQmYrmd0z2KGvNVJgTzVAIRX33HUTl5gly-mjPmeMlxlmSDqoYbWdM-00jxSIYLadQbcgB7Cgk",
    ],
  ],
];

$auth = auth(); // from bootstrap helpers
$isLoggedIn = (bool)($auth["is_logged_in"] ?? false);
$isAdmin = (bool)($auth["user"]["is_admin"] ?? false);

// Active nav styling (matches your original HTML)
$path = parse_url($_SERVER["REQUEST_URI"] ?? "", PHP_URL_PATH) ?: "/index.php";
$isHome = ($path === "/" || $path === "/index.php");
$isFree = ($path === "/content_free.php");
$isPremium = ($path === "/content_premium.php");
$isAdminPage = ($path === "/admin.php");

$clsActive = "text-sm font-medium hover:text-primary transition-colors text-[#111818] dark:text-gray-200";
$clsIdle = "text-sm font-medium hover:text-primary transition-colors text-gray-500 dark:text-gray-400";
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Home - Discover Creators</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&amp;display=swap" rel="stylesheet"/>

  <!-- Material Symbols -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

  <!-- Tailwind Config -->
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
          fontFamily: {
            "display": ["Inter", "sans-serif"]
          },
          borderRadius: {
            "DEFAULT": "0.5rem",
            "lg": "1rem",
            "xl": "1.5rem",
            "full": "9999px"
          },
        },
      },
    }
  </script>

  <style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
  </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#111818] dark:text-gray-100 font-display min-h-screen flex flex-col overflow-x-hidden transition-colors duration-200">

<!-- Navbar -->
<header class="sticky top-0 z-50 w-full border-b border-[#f0f4f4] dark:border-[#1E3332] bg-white/80 dark:bg-background-dark/80 backdrop-blur-md">
  <div class="px-4 md:px-10 py-3 flex items-center justify-between max-w-[1200px] mx-auto">
    <!-- Logo -->
    <div class="flex items-center gap-3">
      <div class="size-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
        <span class="material-symbols-outlined text-2xl">subscriptions</span>
      </div>
      <h2 class="text-lg font-bold leading-tight tracking-tight text-[#111818] dark:text-white">Mini Sub</h2>
    </div>

    <!-- Desktop Nav -->
    <nav class="hidden md:flex items-center gap-8">
      <a class="<?= $isHome ? $clsActive : $clsIdle ?>" href="/index.php">Home</a>
      <a class="<?= $isFree ? $clsActive : $clsIdle ?>" href="/content_free.php">Free Content</a>
      <a class="<?= $isPremium ? $clsActive : $clsIdle ?>" href="/content_premium.php">Premium Content</a>
      <?php if ($isAdmin): ?>
        <a class="<?= $isAdminPage ? $clsActive : $clsIdle ?>" href="/admin.php">Admin Panel</a>
      <?php endif; ?>
    </nav>

    <!-- Auth Buttons -->
    <div class="flex items-center gap-3">
      <?php if (!$isLoggedIn): ?>
        <a href="/login.php" class="hidden md:flex items-center justify-center rounded-xl h-9 px-4 bg-transparent border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1A2C2C] text-[#111818] dark:text-white text-sm font-bold transition-all">
          Login
        </a>
        <a href="/register.php" class="flex items-center justify-center rounded-xl h-9 px-4 bg-primary hover:bg-teal-600 text-white text-sm font-bold shadow-sm shadow-primary/30 transition-all">
          Register
        </a>
      <?php else: ?>
        <a href="/account.php" class="hidden md:flex items-center justify-center rounded-xl h-9 px-4 bg-transparent border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-[#1A2C2C] text-[#111818] dark:text-white text-sm font-bold transition-all">
          Account
        </a>
        <a href="/logout.php" class="flex items-center justify-center rounded-xl h-9 px-4 bg-primary hover:bg-teal-600 text-white text-sm font-bold shadow-sm shadow-primary/30 transition-all">
          Logout
        </a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- Main Layout -->
<main class="flex-1 w-full max-w-[960px] mx-auto px-4 py-8 md:py-12">

  <!-- Page Header -->
  <div class="mb-10 text-center md:text-left">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[#111818] dark:text-white mb-3">Discover creators</h1>
    <p class="text-lg text-gray-500 dark:text-gray-400 max-w-2xl">
      Find and support your favorite content creators. Join the community and unlock exclusive content.
    </p>
  </div>

  <!-- Section: Newest Users -->
  <section class="mb-12">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-[#111818] dark:text-white flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">new_releases</span>
        Newest Users
      </h2>
      <a class="text-sm font-medium text-primary hover:text-teal-700 dark:hover:text-teal-400" href="#">View all</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <?php foreach ($newestUsers as $u): ?>
        <div class="group bg-white dark:bg-[#182828] border border-[#E2E8F0] dark:border-[#2A3838] rounded-xl p-5 hover:shadow-md transition-all duration-300">
          <div class="flex items-start gap-4 mb-3">
            <img
              alt="Portrait of <?= htmlspecialchars($u["name"], ENT_QUOTES, "UTF-8") ?>"
              class="size-10 rounded-full object-cover ring-2 ring-white dark:ring-[#182828]"
              src="<?= htmlspecialchars($u["avatar"], ENT_QUOTES, "UTF-8") ?>"
            />
            <div class="flex flex-col">
              <h3 class="font-bold text-[#111818] dark:text-white leading-snug group-hover:text-primary transition-colors">
                <?= htmlspecialchars($u["name"], ENT_QUOTES, "UTF-8") ?>
              </h3>
              <span class="text-xs font-medium text-gray-400">@<?= htmlspecialchars($u["username"], ENT_QUOTES, "UTF-8") ?></span>
            </div>
          </div>
          <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed">
            <?= htmlspecialchars($u["bio"], ENT_QUOTES, "UTF-8") ?>
          </p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Section: Featured Users -->
  <section>
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-[#111818] dark:text-white flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">star</span>
        Featured Users
      </h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <?php foreach ($featuredUsers as $u): ?>
        <div class="group relative bg-white dark:bg-[#182828] border border-primary/30 dark:border-primary/20 rounded-xl p-5 shadow-sm hover:shadow-lg hover:shadow-primary/5 transition-all duration-300">
          <div class="absolute -top-3 -right-3">
            <span class="bg-primary text-white text-[10px] uppercase font-bold px-2 py-1 rounded-full shadow-sm flex items-center gap-1">
              <span class="material-symbols-outlined text-[12px]">verified</span> Featured
            </span>
          </div>

          <div class="flex items-start gap-4 mb-3">
            <img
              alt="Portrait of <?= htmlspecialchars($u["name"], ENT_QUOTES, "UTF-8") ?>"
              class="size-10 rounded-full object-cover ring-2 ring-primary/20"
              src="<?= htmlspecialchars($u["avatar"], ENT_QUOTES, "UTF-8") ?>"
            />
            <div class="flex flex-col">
              <h3 class="font-bold text-[#111818] dark:text-white leading-snug group-hover:text-primary transition-colors">
                <?= htmlspecialchars($u["name"], ENT_QUOTES, "UTF-8") ?>
              </h3>
              <span class="text-xs font-medium text-gray-400">@<?= htmlspecialchars($u["username"], ENT_QUOTES, "UTF-8") ?></span>
            </div>
          </div>

          <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed">
            <?= htmlspecialchars($u["bio"], ENT_QUOTES, "UTF-8") ?>
          </p>

          <?php if (!empty($u["thumbs"])): ?>
            <div class="mt-4 flex gap-2">
              <?php foreach ($u["thumbs"] as $t): ?>
                <div
                  class="h-16 flex-1 rounded-lg bg-gray-100 dark:bg-[#121e1e] bg-cover bg-center"
                  style="background-image: url('<?= htmlspecialchars($t, ENT_QUOTES, "UTF-8") ?>')"
                ></div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

</main>

<!-- Footer -->
<footer class="border-t border-[#f0f4f4] dark:border-[#1E3332] bg-white dark:bg-background-dark py-10 mt-10">
  <div class="max-w-[960px] mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-6">
    <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 text-sm">
      <span>© 2023 Mini Sub Platform.</span>
    </div>
    <div class="flex gap-6">
      <a class="text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">
        <span class="material-symbols-outlined">adb</span>
      </a>
      <a class="text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">
        <span class="material-symbols-outlined">post_add</span>
      </a>
      <a class="text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">
        <span class="material-symbols-outlined">alternate_email</span>
      </a>
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
