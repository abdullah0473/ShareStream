<?php 
require '../config/db.php';
include '../includes/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';

  $stmt = $conn->prepare("
    SELECT id, password_hash 
    FROM users 
    WHERE email=? AND status='active'
  ");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($u = $res->fetch_assoc()) {

    if (password_verify($pass, $u['password_hash'])) {

      // Set session
      $_SESSION['user_id'] = $u['id'];
  
      // Update last login
      $update = $conn->prepare(
        "UPDATE users SET last_login = NOW() WHERE id = ?"
      );
      $update->bind_param("i", $u['id']);
      $update->execute();

      header('Location: index.php');
      exit;
    }
  }

  $errors[] = 'Invalid email or password';
}
?>

<div class="form-wrapper">
  <h2>Login</h2>

  <?php foreach ($errors as $e): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($e) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endforeach; ?>

  <form method="post" novalidate>

    <div class="form-group">
      <label>Email</label>
      <input id="email" name="email" class="form-control" required>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input id="password" type="password" name="password" class="form-control" required>
    </div>

    <button class="btn-primary" type="submit">Login</button>

  </form>
</div>

<?php include '../includes/footer.php'; ?>
