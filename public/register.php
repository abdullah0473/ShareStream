<?php
require '../config/db.php';
include '../includes/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $name     = trim($_POST['full_name'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $phone    = trim($_POST['phone'] ?? '');
  $city     = trim($_POST['city'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm  = $_POST['confirm_password'] ?? '';

  if (strlen($name) < 3) {
    $errors[] = 'Full name must be at least 3 characters';
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address';
  }

  if (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters';
  }

  if ($confirm === '') {
    $errors[] = 'Please confirm your password';
  }

  if ($password !== $confirm) {
    $errors[] = 'Passwords do not match';
  }

  if (empty($errors)) {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
      "INSERT INTO users (full_name, email, phone, city, password_hash)
       VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssss", $name, $email, $phone, $city, $hash);
    $stmt->execute();

    header('Location: login.php');
    exit;
  }
}
?>

<div class="form-wrapper">
  <h2>Create Account</h2>

  <?php foreach ($errors as $e): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($e) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endforeach; ?>

  <form id="registerForm" method="post" novalidate>
    <div class="form-group">
      <label>Full Name</label>
      <input id="full_name" name="full_name" class="form-control">
    </div>

    <div class="form-group">
      <label>Email</label>
      <input id="email" name="email" class="form-control">
    </div>

    <div class="form-group">
      <label>Phone</label>
      <input id="phone" name="phone" class="form-control">
    </div>

    <div class="form-group">
      <label>City</label>
      <input id="city" name="city" class="form-control">
    </div>

    <div class="form-group">
      <label>Password</label>
      <input id="password" type="password" name="password" class="form-control">
    </div>

    <div class="form-group">
      <label>Confirm Password</label>
      <input id="confirm_password" type="password" name="confirm_password" class="form-control">
    </div>

    <button class="btn-primary" type="submit">Register</button>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
