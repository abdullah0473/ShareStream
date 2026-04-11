<?php
require '../config/db.php';
include '../includes/header.php';

/* =====================
   FETCH CATEGORIES
===================== */
$categories = [];
$res = $conn->query("SELECT id, name, icon, color FROM categories ORDER BY name");
while ($row = $res->fetch_assoc()) {
  $categories[] = $row;
}

/* =====================
   FILTER INPUTS
===================== */
$search = trim($_GET['q'] ?? '');
$selectedCategories = $_GET['categories'] ?? [];
if (!is_array($selectedCategories)) {
  $selectedCategories = [];
}

/* =====================
   BUILD QUERY
===================== */
$sql = "
  SELECT i.*, c.name AS category_name, c.icon, c.color
  FROM ideas i
  JOIN categories c ON c.id = i.category_id
  WHERE i.status = 'active'
";

$params = [];
$types = '';

if ($search !== '') {
  $sql .= " AND (i.title LIKE ? OR i.summary LIKE ?)";
  $params[] = "%$search%";
  $params[] = "%$search%";
  $types .= "ss";
}

if (!empty($selectedCategories)) {
  $placeholders = implode(',', array_fill(0, count($selectedCategories), '?'));
  $sql .= " AND i.category_id IN ($placeholders)";
  foreach ($selectedCategories as $cid) {
    $params[] = (int)$cid;
    $types .= "i";
  }
}

$sql .= " ORDER BY i.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$ideas = $stmt->get_result();
?>

<div class="container my-5">

  <!-- HEADER -->
  <div class="mb-4">
    <h2 class="fw-bold">Explore Ideas</h2>
    <p class="text-muted">Discover ideas shared by the community</p>
  </div>

  <!-- FILTER BAR -->
  <form method="get" id="ideaFilterForm" class="mb-4 position-relative">

    <div class="card shadow-sm">
      <div class="card-body">

        <div class="row g-3 align-items-center">

          <!-- SEARCH -->
          <div class="col-md-5">
            <input
              type="text"
              name="q"
              class="form-control form-control-lg"
              placeholder="Search ideas..."
              value="<?= htmlspecialchars($search) ?>">
          </div>

          <!-- CATEGORY SEARCH -->
          <div class="col-md-5 position-relative">
            <input
              type="text"
              id="categorySearch"
              class="form-control form-control-lg"
              placeholder="Add category…"
              autocomplete="off">
            <div id="categoryResults" class="list-group position-absolute w-100 d-none" style="z-index:1000"></div>
          </div>

          <!-- ACTIONS -->
          <div class="col-md-2 d-grid">
            <button class="btn btn-primary btn-lg">Apply</button>
            <a href="ideas.php" class="btn btn-link mt-1">Reset</a>
          </div>

        </div>

        <!-- SELECTED CATEGORIES -->
        <?php if (!empty($selectedCategories)): ?>
          <div class="mt-3 d-flex flex-wrap gap-2">
            <?php foreach ($categories as $cat): ?>
              <?php if (in_array($cat['id'], $selectedCategories)): ?>
                <span class="badge rounded-pill px-3 py-2"
                  style="background: <?= $cat['color'] ?>20; color: <?= $cat['color'] ?>">
                  <?= htmlspecialchars($cat['name']) ?>
                  <input type="hidden" name="categories[]" value="<?= $cat['id'] ?>">
                </span>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </form>

  <!-- IDEAS GRID -->
  <div class="row g-4">

    <?php if ($ideas->num_rows === 0): ?>
      <div class="col-12">
        <div class="alert alert-info">
          No ideas found. Try a different search or category.
        </div>
      </div>
    <?php endif; ?>

    <?php while ($idea = $ideas->fetch_assoc()): ?>
      <div class="col-md-6 col-lg-4">

        <a href="idea.php?id=<?= $idea['id'] ?>" class="text-decoration-none text-dark">

          <div class="card h-100 shadow-sm idea-card">

            <?php if ($idea['cover_image']): ?>
              <img
                src="<?= BASE_URL ?>uploads/ideas/<?= $idea['cover_image'] ?>"
                class="card-img-top"
                style="height:180px; object-fit:cover;">
            <?php endif; ?>

            <div class="card-body">

              <span class="badge mb-2"
                style="background: <?= $idea['color'] ?>; color:#fff;">
                <?= htmlspecialchars($idea['category_name']) ?>
              </span>

              <h5 class="mt-2"><?= htmlspecialchars($idea['title']) ?></h5>

              <p class="text-muted">
                <?= htmlspecialchars(mb_strimwidth($idea['summary'], 0, 120, '...')) ?>
              </p>

            </div>

            <div class="card-footer bg-white d-flex justify-content-between">
              <small class="text-muted">
                <i class="fa-regular fa-eye"></i> <?= (int)$idea['views'] ?>
              </small>
              <small class="text-muted">
                <?= date('M d, Y', strtotime($idea['created_at'])) ?>
              </small>
            </div>

          </div>

        </a>

      </div>
    <?php endwhile; ?>

  </div>
</div>

<script>
  window.IDEA_CATEGORIES = <?= json_encode($categories) ?>;
</script>
<script src="<?= BASE_URL ?>assets/js/ideas.js"></script>

<style>
.idea-card {
  border-radius: 14px;
  transition: all .25s ease;
}
.idea-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 30px rgba(0,0,0,.12);
}
</style>

<?php include '../includes/footer.php'; ?>