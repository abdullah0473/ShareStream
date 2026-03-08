<?php
require '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

include '../includes/header.php';

$errors = [];
$success = '';

// Defaults (for repopulating form)
$category_id = '';
$title = '';
$summary = '';
$description = '';

// Fetch categories
$categories = [];
$result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $user_id     = $_SESSION['user_id'];
  $category_id = intval($_POST['category_id'] ?? 0);
  $title       = trim($_POST['title'] ?? '');
  $summary     = trim($_POST['summary'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $status      = 'active';  
  $cover_image = null;

  /* =====================
     VALIDATION
  ===================== */
  if ($category_id <= 0) {
    $errors[] = 'Please select a category';
  }

  if (strlen($title) < 5) {
    $errors[] = 'Title must be at least 5 characters';
  }

  if (strlen($summary) < 10) {
    $errors[] = 'Summary must be at least 10 characters';
  }

  if (strlen($description) < 20) {
    $errors[] = 'Description must be at least 20 characters';
  }

  /* =====================
     IMAGE UPLOAD (SAFE)
  ===================== */
  if (!empty($_FILES['cover_image']['name'])) {

    if ($_FILES['cover_image']['error'] !== UPLOAD_ERR_OK) {
      $errors[] = 'Image upload error';
    } else {

      $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg', 'jpeg', 'png', 'webp'];

      if (!in_array($ext, $allowed)) {
        $errors[] = 'Cover image must be jpg, jpeg, png, or webp';
      } else {

        $uploadDir = dirname(__DIR__) . '/uploads/ideas/';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0775, true);
        }

        $cover_image = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['cover_image']['name']);
        $uploadPath = $uploadDir . $cover_image;

        if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadPath)) {
          $errors[] = 'Failed to upload cover image';
        }
      }
    }
  }

  /* =====================
     INSERT
  ===================== */
  if (empty($errors)) {

    $stmt = $conn->prepare(
      "INSERT INTO ideas
      (user_id, category_id, title, summary, description, cover_image, status)
      VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
      "iisssss",
      $user_id,
      $category_id,
      $title,
      $summary,
      $description,
      $cover_image,
      $status
    );

    if ($stmt->execute()) {
      // Success message 
      $success = 'Idea created successfully!';

      // Clear form values
      $category_id = '';
      $title = '';
      $summary = '';
      $description = '';
    }
  }
}
?>

<div class="form-wrapper">
  <h2>Create Idea</h2>

  <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($success) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php foreach ($errors as $e): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($e) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endforeach; ?>

  <form id="createIdeaForm" method="post" enctype="multipart/form-data" novalidate>

    <div class="form-group mb-3">
      <label>Category</label>
      <select id="category_id" name="category_id" class="form-control">
        <option value="">Select Category</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>"
            <?= ($category_id == $cat['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group mb-3">
      <label>Title</label>
      <input
        id="title"
        name="title"
        class="form-control"
        value="<?= htmlspecialchars($title) ?>">
    </div>

    <div class="form-group mb-3">
      <label>Summary</label>
      <textarea
        id="summary"
        name="summary"
        class="form-control"
        rows="3"><?= htmlspecialchars($summary) ?></textarea>
    </div>

    <div class="form-group mb-3">
      <label>Description</label>
      <textarea
        id="description"
        name="description"
        class="form-control"
        rows="6"><?= htmlspecialchars($description) ?></textarea>
    </div>

    <div class="form-group mb-4">
      <label>Cover Image (optional)</label>
      <input
        id="cover_image"
        type="file"
        name="cover_image"
        class="form-control">
    </div>

    <button class="btn btn-primary" type="submit">
      Create Idea
    </button>

  </form>
</div>

<?php include '../includes/footer.php'; ?>