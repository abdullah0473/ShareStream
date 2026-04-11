<?php
require '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

include '../includes/header.php';

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Fetch user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
  die('User not found');
}

// Defaults
$full_name = $user['full_name'];
$email     = $user['email'];
$phone     = $user['phone'];
$city      = $user['city'];
$bio       = $user['bio'];
$avatar    = $user['avatar'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $full_name = trim($_POST['full_name'] ?? '');
  $phone     = trim($_POST['phone'] ?? '');
  $city      = trim($_POST['city'] ?? '');
  $bio       = trim($_POST['bio'] ?? '');
  $password  = $_POST['password'] ?? '';
  $confirm   = $_POST['confirm_password'] ?? '';

  // Validation (same style as register.php)
  if (strlen($full_name) < 3) {
    $errors[] = 'Full name must be at least 3 characters';
  }

  if ($password !== '') {
    if (strlen($password) < 6) {
      $errors[] = 'Password must be at least 6 characters';
    }
    if ($password !== $confirm) {
      $errors[] = 'Passwords do not match';
    }
  }

  // Avatar upload (optional)
  if (!empty($_FILES['avatar']['name'])) {

    if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
      $errors[] = 'Avatar upload failed';
    } else {

      $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg', 'jpeg', 'png', 'webp'];

      if (!in_array($ext, $allowed)) {
        $errors[] = 'Avatar must be jpg, jpeg, png, or webp';
      } else {

        $uploadDir = dirname(__DIR__) . '/uploads/avatars/';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0775, true);
        }

        $avatarName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['avatar']['name']);
        $uploadPath = $uploadDir . $avatarName;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
          $avatar = $avatarName;
        } else {
          $errors[] = 'Failed to upload avatar';
        }
      }
    }
  }

  if (empty($errors)) {

    if ($password !== '') {
      $hash = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $conn->prepare(
        "UPDATE users SET
          full_name = ?, phone = ?, city = ?, bio = ?, avatar = ?, password_hash = ?
         WHERE id = ?"
      );
      $stmt->bind_param(
        "ssssssi",
        $full_name,
        $phone,
        $city,
        $bio,
        $avatar,
        $hash,
        $user_id
      );

    } else {

      $stmt = $conn->prepare(
        "UPDATE users SET
          full_name = ?, phone = ?, city = ?, bio = ?, avatar = ?
         WHERE id = ?"
      );
      $stmt->bind_param(
        "sssssi",
        $full_name,
        $phone,
        $city,
        $bio,
        $avatar,
        $user_id
      );
    }

    $stmt->execute();
    $success = 'Profile updated successfully';
  }
}
?>

<div class="container my-4">

  <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
      <?= htmlspecialchars($success) ?>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php foreach ($errors as $e): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <?= htmlspecialchars($e) ?>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endforeach; ?>

  <!-- PROFILE INFO -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body text-center">

          <img
            src="<?= $avatar ? BASE_URL . 'uploads/avatars/' . $avatar : BASE_URL . 'assets/img/default-avatar.png' ?>"
            class="rounded-circle mb-3"
            width="120"
            height="120">

          <h5><?= htmlspecialchars($user['full_name']) ?></h5>
          <p class="text-muted mb-1"><?= htmlspecialchars($user['email']) ?></p>
          <p class="small"><?= htmlspecialchars($user['bio'] ?? 'No bio added') ?></p>

        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
          <p><strong>Status:</strong> <?= htmlspecialchars($user['status']) ?></p>
          <p><strong>City:</strong> <?= htmlspecialchars($user['city'] ?? '-') ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? '-') ?></p>
          <p><strong>Joined:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
          <p><strong>Last Login:</strong> <?= htmlspecialchars($user['last_login'] ?? '-') ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- UPDATE FORM -->
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3">Update Profile</h4>

      <form method="post" enctype="multipart/form-data" novalidate>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Full Name</label>
            <input name="full_name" class="form-control" value="<?= htmlspecialchars($full_name) ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label>Phone</label>
            <input name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label>City</label>
            <input name="city" class="form-control" value="<?= htmlspecialchars($city) ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label>Avatar</label>
            <input type="file" name="avatar" class="form-control">
          </div>
        </div>

        <div class="mb-3">
          <label>Bio</label>
          <textarea name="bio" class="form-control" rows="3"><?= htmlspecialchars($bio) ?></textarea>
        </div>

        <hr>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label>New Password (optional)</label>
            <input type="password" name="password" class="form-control">
          </div>

          <div class="col-md-6 mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control">
          </div>
        </div>

        <button class="btn btn-primary">Update Profile</button>
      </form>
    </div>
  </div>

</div>

<?php include '../includes/footer.php'; ?>