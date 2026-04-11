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

$user_id = $_SESSION['user_id'];

// Validate idea id
$idea_id = intval($_GET['id'] ?? 0);
if ($idea_id <= 0) {
  die('Invalid idea');
}

/* =====================
   FETCH IDEA (OWNER ONLY)
===================== */
$stmt = $conn->prepare(
  "SELECT * FROM ideas WHERE id = ? AND user_id = ?"
);
$stmt->bind_param("ii", $idea_id, $user_id);
$stmt->execute();
$idea = $stmt->get_result()->fetch_assoc();

if (!$idea) {
  die('Idea not found or access denied');
}

// Defaults (pre-fill form)
$category_id = $idea['category_id'];
$title = $idea['title'];
$summary = $idea['summary'];
$description = $idea['description'];
$cover_image = $idea['cover_image'];

/* =====================
   FETCH CATEGORIES
===================== */
$categories = [];
$result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
  }
}

/* =====================
   FORM SUBMIT
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $category_id = intval($_POST['category_id'] ?? 0);
  $title       = trim($_POST['title'] ?? '');
  $summary     = trim($_POST['summary'] ?? '');
  $description = trim($_POST['description'] ?? '');
  // consistent with create
  $status      = 'active'; 
  $new_image   = $cover_image;

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
     IMAGE UPLOAD (OPTIONAL)
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

        $new_image = time() . '_' . preg_replace(
          '/[^a-zA-Z0-9._-]/',
          '',
          $_FILES['cover_image']['name']
        );

        $uploadPath = $uploadDir . $new_image;

        if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadPath)) {
          $errors[] = 'Failed to upload cover image';
        } else {
          // Delete old image
          if ($cover_image && file_exists($uploadDir . $cover_image)) {
            unlink($uploadDir . $cover_image);
          }
        }
      }
    }
  }

  /* =====================
     UPDATE
  ===================== */
  if (empty($errors)) {

    $stmt = $conn->prepare(
      "UPDATE ideas SET
        category_id = ?,
        title = ?,
        summary = ?,
        description = ?,
        cover_image = ?,
        status = ?
       WHERE id = ? AND user_id = ?"
    );

    $stmt->bind_param(
      "isssssii",
      $category_id,
      $title,
      $summary,
      $description,
      $new_image,
      $status,
      $idea_id,
      $user_id
    );

    if ($stmt->execute()) {
      $success = 'Idea updated successfully!';
      $cover_image = $new_image;
    }
  }
}
?>

<div class="form-wrapper">
  <h2>Edit Idea</h2>

  <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
      <?= htmlspecialchars($success) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php foreach ($errors as $e): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <?= htmlspecialchars($e) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endforeach; ?>

  <form id="editIdeaForm" method="post" enctype="multipart/form-data" novalidate>

    <div class="form-group mb-3">
      <label>Category</label>
      <select name="category_id" class="form-control">
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
      <input name="title" class="form-control" value="<?= htmlspecialchars($title) ?>">
    </div>

    <div class="form-group mb-3">
      <label>Summary</label>
      <textarea name="summary" class="form-control" rows="3"><?= htmlspecialchars($summary) ?></textarea>
    </div>

    <div class="form-group mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control" rows="6"><?= htmlspecialchars($description) ?></textarea>
    </div>

    <div class="form-group mb-4">
      <label>Cover Image (optional)</label>
      <?php if ($cover_image): ?>
        <div class="mb-2">
          <img src="<?= BASE_URL ?>uploads/ideas/<?= $cover_image ?>" height="80">
        </div>
      <?php endif; ?>
      <input type="file" name="cover_image" class="form-control">
    </div>

    <button class="btn btn-primary" type="submit">
      Update Idea
    </button>

  </form>
</div>

<?php include '../includes/footer.php'; ?>