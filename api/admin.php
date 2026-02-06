<?php
// File: /api/admin.php
declare(strict_types=1);

require __DIR__ . "/../app/bootstrap.php";

$config = $GLOBALS["config"];
$pdo = $GLOBALS["pdo"];
$auth = auth();

if (empty($auth["is_logged_in"])) redirect("/login.php");
if (empty($auth["user"]["is_admin"])) redirect("/index.php");

$flash = get_flash();
$errors = [];
$importSummary = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (!csrf_check($config, $_POST["_csrf"] ?? null)) {
    $errors[] = "Invalid form session. Please refresh and try again.";
  } else {
    $mode = (string)($_POST["mode"] ?? "");

    // --------- USER TOGGLES ----------
    if ($mode === "toggle") {
      $userId = (int)($_POST["user_id"] ?? 0);
      $field = (string)($_POST["field"] ?? "");

      if ($userId <= 0) $errors[] = "Invalid user id.";
      if (!in_array($field, ["is_paid", "is_active", "is_featured"], true)) $errors[] = "Invalid field.";

      if (!$errors) {
        // do not allow editing self admin flag here, but paid/active/featured is ok
        $stmt = $pdo->prepare("UPDATE users SET {$field} = CASE WHEN {$field}=1 THEN 0 ELSE 1 END WHERE id = :id");
        $stmt->execute([":id" => $userId]);

        set_flash("success", "Updated user.");
        redirect("/admin.php");
      }
    }

    // --------- CSV IMPORT ----------
    if ($mode === "import") {
      if (empty($_FILES["csv"]["tmp_name"])) {
        $errors[] = "Please choose a CSV file.";
      } else {
        $tmp = (string)$_FILES["csv"]["tmp_name"];
        $fh = fopen($tmp, "rb");
        if (!$fh) {
          $errors[] = "Could not read uploaded file.";
        } else {
          $header = fgetcsv($fh);
          $expected = ["username", "name", "bio", "is_featured"];

          if (!$header) {
            $errors[] = "CSV is empty.";
          } else {
            $norm = array_map(fn($x) => strtolower(trim((string)$x)), $header);
            if ($norm !== $expected) {
              $errors[] = "CSV header must be exactly: username,name,bio,is_featured";
            }
          }

          $total = 0;
          $inserted = 0;
          $skipped = 0;
          $rowErrors = 0;

          if (!$errors) {
            $ins = $pdo->prepare("
              INSERT INTO users (username, name, password_hash, bio, is_paid, is_featured, is_admin, is_active, created_at)
              VALUES (:username, :name, :password_hash, :bio, 0, :is_featured, 0, 0, :created_at)
            ");

            while (($row = fgetcsv($fh)) !== false) {
              $total++;

              $username = trim((string)($row[0] ?? ""));
              $name = trim((string)($row[1] ?? ""));
              $bio = trim((string)($row[2] ?? ""));
              $isFeaturedRaw = trim((string)($row[3] ?? ""));
              $isFeatured = ($isFeaturedRaw === "1" || strtolower($isFeaturedRaw) === "true") ? 1 : 0;

              if ($username === "" || $name === "") {
                $rowErrors++;
                continue;
              }

              // basic username safety
              if (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $username)) {
                $rowErrors++;
                continue;
              }

              // skip if exists
              $chk = $pdo->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
              $chk->execute([":u" => $username]);
              if ($chk->fetch()) {
                $skipped++;
                continue;
              }

              // Imported users start inactive and without a usable password.
              // They can login only after you add a password system, which we are not adding in this MVP.
              $random = bin2hex(random_bytes(16));
              $hash = password_hash($random, PASSWORD_DEFAULT);

              $ins->execute([
                ":username" => $username,
                ":name" => $name,
                ":password_hash" => $hash,
                ":bio" => $bio,
                ":is_featured" => $isFeatured,
                ":created_at" => date("c"),
              ]);

              $inserted++;
            }

            $importSummary = [
              "total" => $total,
              "inserted" => $inserted,
              "skipped" => $skipped,
              "row_errors" => $rowErrors,
            ];
          }

          fclose($fh);
        }
      }
    }
  }
}

// Fetch users for table
$stmt = $pdo->prepare("
  SELECT id, username, name, is_paid, is_active, is_featured
  FROM users
  ORDER BY id DESC
  LIMIT 200
");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Admin Management Panel</title>

  <link href="https://fonts.googleapis.com" rel="preconnect"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet"/>
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

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white min-h-screen flex flex-col font-display antialiased selection:bg-primary/30">
<div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
  <div class="layout-container flex h-full grow flex-col">

    <header class="sticky top-0 z-10 flex items-center justify-between whitespace-nowrap border-b border-solid border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md px-4 sm:px-10 py-3">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center rounded-lg bg-primary/10 p-2 text-primary">
          <span class="material-symbols-outlined">admin_panel_settings</span>
        </div>
        <h2 class="text-slate-900 dark:text-white text-lg font-bold leading-tight tracking-tight">Admin Panel</h2>
      </div>
      <div class="flex flex-1 justify-end gap-4 sm:gap-8">
        <div class="hidden md:flex items-center gap-6">
          <a class="text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors text-sm font-medium leading-normal" href="/index.php">Dashboard</a>
          <a class="text-primary text-sm font-medium leading-normal" href="/admin.php">Users</a>
          <a class="text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors text-sm font-medium leading-normal" href="/content_free.php">Content</a>
          <a class="text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors text-sm font-medium leading-normal" href="/account.php">Account</a>
        </div>
        <div class="bg-center bg-no-repeat bg-cover rounded-full size-10 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-center">
          <span class="material-symbols-outlined text-slate-500">person</span>
        </div>
      </div>
    </header>

    <main class="flex-1 flex flex-col px-4 sm:px-10 py-8 mx-auto w-full max-w-[1200px]">
      <h1 class="text-slate-900 dark:text-white tracking-tight text-3xl font-bold leading-tight mb-8">Admin Panel</h1>

      <?php if ($flash && ($flash["type"] ?? "") === "success"): ?>
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-900/50 dark:bg-green-900/20 text-sm text-green-800 dark:text-green-200">
          <?= h((string)$flash["message"]) ?>
        </div>
      <?php endif; ?>

      <?php if ($errors): ?>
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-900/20 text-sm text-red-800 dark:text-red-200">
          <div class="font-bold mb-1">Error</div>
          <ul class="list-disc pl-5">
            <?php foreach ($errors as $e): ?>
              <li><?= h((string)$e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if ($importSummary): ?>
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-900/50 dark:bg-green-900/20 text-sm text-green-800 dark:text-green-200">
          Imported: <?= (int)$importSummary["inserted"] ?>,
          Skipped(existing): <?= (int)$importSummary["skipped"] ?>,
          Row errors: <?= (int)$importSummary["row_errors"] ?>,
          Total rows: <?= (int)$importSummary["total"] ?>
        </div>
      <?php endif; ?>

      <div class="grid gap-8">

        <section class="flex flex-col rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-6 border-b border-slate-100 dark:border-slate-800/50">
            <div>
              <h3 class="text-slate-900 dark:text-white text-lg font-bold leading-tight">User Management</h3>
              <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Manage user subscriptions and status</p>
            </div>
          </div>

          <div class="@container w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                  <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-[20%]">Username</th>
                  <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-[20%]">Name</th>
                  <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-[15%]">Paid Status</th>
                  <th class="p-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-[15%]">Active</th>
                  <th class="p-4 text-xs font-semibold text-center text-slate-500 dark:text-slate-400 uppercase tracking-wider w-[10%]">Featured</th>
                  <th class="p-4 text-xs font-semibold text-right text-slate-500 dark:text-slate-400 uppercase tracking-wider w-[20%]">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php foreach ($users as $u): ?>
                  <?php
                    $paid = (int)$u["is_paid"] === 1;
                    $active = (int)$u["is_active"] === 1;
                    $featured = (int)$u["is_featured"] === 1;
                  ?>
                  <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="p-4 text-sm font-medium text-slate-900 dark:text-white"><?= h((string)$u["username"]) ?></td>
                    <td class="p-4 text-sm text-slate-600 dark:text-slate-300"><?= h((string)$u["name"]) ?></td>
                    <td class="p-4">
                      <?php if ($paid): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Paid</span>
                      <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">Free</span>
                      <?php endif; ?>
                    </td>
                    <td class="p-4">
                      <?php if ($active): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary dark:bg-primary/20 dark:text-primary-300">Active</span>
                      <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">Inactive</span>
                      <?php endif; ?>
                    </td>

                    <td class="p-4 text-center">
                      <form method="POST" action="/admin.php">
                        <input type="hidden" name="_csrf" value="<?= h(csrf_token($config)) ?>">
                        <input type="hidden" name="mode" value="toggle">
                        <input type="hidden" name="user_id" value="<?= (int)$u["id"] ?>">
                        <input type="hidden" name="field" value="is_featured">
                        <input onchange="this.form.submit()" <?= $featured ? "checked" : "" ?>
                               class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-800"
                               type="checkbox"/>
                      </form>
                    </td>

                    <td class="p-4 text-right">
                      <div class="flex items-center justify-end gap-2">
                        <form method="POST" action="/admin.php" class="inline">
                          <input type="hidden" name="_csrf" value="<?= h(csrf_token($config)) ?>">
                          <input type="hidden" name="mode" value="toggle">
                          <input type="hidden" name="user_id" value="<?= (int)$u["id"] ?>">
                          <input type="hidden" name="field" value="is_paid">
                          <button class="text-xs font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-primary transition-colors" type="submit">Toggle Paid</button>
                        </form>
                        <span class="text-slate-300 dark:text-slate-700">|</span>
                        <form method="POST" action="/admin.php" class="inline">
                          <input type="hidden" name="_csrf" value="<?= h(csrf_token($config)) ?>">
                          <input type="hidden" name="mode" value="toggle">
                          <input type="hidden" name="user_id" value="<?= (int)$u["id"] ?>">
                          <input type="hidden" name="field" value="is_active">
                          <button class="text-xs font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-primary transition-colors" type="submit">Toggle Active</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 px-6 py-3">
            <p class="text-sm text-slate-500 dark:text-slate-400">Showing up to 200 users</p>
            <div class="flex gap-2">
              <button class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1 text-sm font-medium text-slate-600 dark:text-slate-300 disabled:opacity-50" disabled type="button">Previous</button>
              <button class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1 text-sm font-medium text-slate-600 dark:text-slate-300 disabled:opacity-50" disabled type="button">Next</button>
            </div>
          </div>
        </section>

        <section class="flex flex-col rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
          <div class="flex flex-col gap-1 p-6 border-b border-slate-100 dark:border-slate-800/50">
            <h3 class="text-slate-900 dark:text-white text-lg font-bold leading-tight">CSV Import</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Bulk import users via CSV file</p>
          </div>

          <div class="p-6">
            <form method="POST" action="/admin.php" enctype="multipart/form-data">
              <input type="hidden" name="_csrf" value="<?= h(csrf_token($config)) ?>">
              <input type="hidden" name="mode" value="import">

              <div class="relative group cursor-pointer flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 py-12 px-6 text-center transition-all hover:border-primary hover:bg-primary/5 dark:hover:border-primary dark:hover:bg-primary/10">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 group-hover:text-primary transition-colors">
                  <span class="material-symbols-outlined text-2xl">cloud_upload</span>
                </div>
                <p class="mt-4 text-sm font-semibold text-slate-900 dark:text-white">
                  <span class="text-primary">Click to upload</span> CSV
                </p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">CSV only (MAX 5MB)</p>
                <input name="csv" accept=".csv" class="absolute inset-0 cursor-pointer opacity-0" type="file" required>
              </div>

              <div class="mt-6 flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-6">
                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                  <span class="material-symbols-outlined text-base">info</span>
                  <span>Header must be: username,name,bio,is_featured</span>
                </div>
                <div class="flex gap-3">
                  <button class="flex items-center gap-2 rounded-lg bg-primary px-5 py-2 text-sm font-medium text-white shadow-sm shadow-primary/25 hover:bg-primary/90 transition-all" type="submit">
                    Import Users
                  </button>
                </div>
              </div>
            </form>

          </div>
        </section>

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
  }*/
</script>
</body>
</html>
