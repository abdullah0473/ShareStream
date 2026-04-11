<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../config/config.php';

$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= APP_NAME ?></title>
<meta name="viewport" content="width=device-width,initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<header class="site-header border-bottom">
  <div class="container header-inner d-flex justify-content-between align-items-center">

    <!-- LOGO -->
    <a class="logo fw-bold text-decoration-none" href="<?= BASE_URL ?>public/index.php">
      <?= APP_NAME ?>
    </a>

    <!-- NAV -->
    <nav class="nav-links d-flex align-items-center gap-3">

      <!-- PUBLIC LINKS -->
      <a href="<?= BASE_URL ?>public/index.php">Home</a>
      <a href="<?= BASE_URL ?>public/ideas.php">Explore Ideas</a>

      <?php if ($user_id): ?>

        <!-- USER LINKS -->
        <a href="<?= BASE_URL ?>admin/dashboard.php">Dashboard</a>
        <a href="<?= BASE_URL ?>admin/create_idea.php">Create Idea</a>
        <a href="<?= BASE_URL ?>admin/profile.php">Profile</a>

        <?php if ($user_role === 'admin'): ?>
          <a href="<?= BASE_URL ?>admin/admin_dashboard.php"
             class="text-danger fw-semibold">
            Admin
          </a>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>public/logout.php"
           class="btn btn-sm btn-outline-secondary">
          Logout
        </a>

      <?php else: ?>

        <!-- GUEST LINKS -->
        <a href="<?= BASE_URL ?>public/login.php">Login</a>
        <a href="<?= BASE_URL ?>public/register.php"
           class="btn btn-sm btn-primary">
          Register
        </a>

      <?php endif; ?>

    </nav>
  </div>
</header>
