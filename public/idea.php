<?php
require '../config/db.php';
session_start();
include '../includes/header.php';

$idea_id = intval($_GET['id'] ?? 0);
if ($idea_id <= 0) die('Invalid idea');

$user_id = $_SESSION['user_id'] ?? null;

/* =====================
   FETCH IDEA
===================== */
$stmt = $conn->prepare("
  SELECT i.*, u.full_name, u.avatar,
         c.name AS category_name, c.color
  FROM ideas i
  JOIN users u ON u.id = i.user_id
  JOIN categories c ON c.id = i.category_id
  WHERE i.id = ? AND i.status = 'active'
");
$stmt->bind_param("i", $idea_id);
$stmt->execute();
$idea = $stmt->get_result()->fetch_assoc();
if (!$idea) die('Idea not found');

/* =====================
   VIEW COUNT
===================== */
$conn->query("UPDATE ideas SET views = views + 1 WHERE id = $idea_id");

/* =====================
   VOTES COUNT
===================== */
$votes = $conn->query(
  "SELECT COUNT(*) c FROM votes WHERE idea_id = $idea_id"
)->fetch_assoc()['c'];

/* =====================
   BOOKMARK STATUS
===================== */
$bookmarked = false;
if ($user_id) {
  $bookmarked = $conn->query(
    "SELECT id FROM bookmarks WHERE user_id=$user_id AND idea_id=$idea_id"
  )->num_rows > 0;
}

/* =====================
   REPORT STATUS (IMPORTANT)
===================== */
$userReported = false;
if ($user_id) {
  $rq = $conn->prepare(
    "SELECT id, reason, created_at FROM reports
     WHERE reporter_id = ? AND idea_id = ?"
  );
  $rq->bind_param("ii", $user_id, $idea_id);
  $rq->execute();
  $reportedRow = $rq->get_result()->fetch_assoc();
  $userReported = (bool)$reportedRow;
}

/* =====================
   COMMENTS
===================== */
$comments = $conn->query("
  SELECT c.*, u.full_name, u.avatar
  FROM comments c
  JOIN users u ON u.id = c.user_id
  WHERE c.idea_id = $idea_id
  ORDER BY c.created_at DESC
");
?>

<div class="container my-5">

  <!-- IDEA CARD -->
  <div class="card shadow-sm mb-4">

    <?php if ($idea['cover_image']): ?>
      <div class="idea-cover">
        <img src="<?= BASE_URL ?>uploads/ideas/<?= $idea['cover_image'] ?>">
      </div>
    <?php endif; ?>

    <div class="card-body">

      <span class="badge mb-2" style="background:<?= $idea['color'] ?>;color:#fff">
        <?= htmlspecialchars($idea['category_name']) ?>
      </span>

      <h2><?= htmlspecialchars($idea['title']) ?></h2>
      <p class="text-muted"><?= htmlspecialchars($idea['summary']) ?></p>

      <div class="d-flex align-items-center mt-3">
        <img
          src="<?= $idea['avatar']
            ? BASE_URL.'uploads/avatars/'.$idea['avatar']
            : BASE_URL.'assets/img/default-avatar.png' ?>"
          class="rounded-circle me-2"
          width="40" height="40">

        <strong><?= htmlspecialchars($idea['full_name']) ?></strong>

        <span class="text-muted ms-3">
          <?= date('M d, Y', strtotime($idea['created_at'])) ?>
        </span>
      </div>
    </div>
  </div>

  <!-- USER REPORTED CARD -->
  <?php if ($userReported): ?>
    <div class="card border-warning mb-4">
      <div class="card-body bg-warning-subtle">
        <h6 class="text-warning mb-1">You reported this idea</h6>
        <p class="mb-1"><?= htmlspecialchars($reportedRow['reason']) ?></p>
        <small class="text-muted">
          Reported on <?= date('M d, Y', strtotime($reportedRow['created_at'])) ?>
        </small>
      </div>
    </div>
  <?php endif; ?>

  <!-- ACTION BAR -->
  <div class="d-flex flex-wrap gap-3 mb-4">

    <!-- VOTE -->
    <button id="voteBtn" class="btn btn-outline-success">
      👍 <span id="voteCount"><?= $votes ?></span>
    </button>

    <!-- BOOKMARK -->
    <?php if ($user_id): ?>
      <button id="bookmarkBtn" class="btn btn-outline-primary">
        <?= $bookmarked ? '★ Bookmarked' : '☆ Bookmark' ?>
      </button>
    <?php endif; ?>

    <!-- REPORT -->
    <?php if ($user_id && !$userReported): ?>
      <button
        class="btn btn-outline-danger"
        data-bs-toggle="modal"
        data-bs-target="#reportModal">
        Report
      </button>
    <?php endif; ?>

  </div>

  <!-- DESCRIPTION -->
  <div class="card mb-5">
    <div class="card-body">
      <?= nl2br(htmlspecialchars($idea['description'])) ?>
    </div>
  </div>

  <!-- COMMENTS -->
  <h4 class="mb-3">Comments</h4>

  <?php if ($user_id): ?>
    <form id="commentForm" class="mb-4">
      <textarea name="body" class="form-control mb-2"
        placeholder="Write a comment..." required></textarea>
      <button class="btn btn-primary">Post Comment</button>
    </form>
  <?php else: ?>
    <p><a href="login.php">Login</a> to comment.</p>
  <?php endif; ?>

  <div id="comments">
    <?php while ($c = $comments->fetch_assoc()): ?>
      <div class="card mb-3 comment-card">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <img
              src="<?= $c['avatar']
                ? BASE_URL.'uploads/avatars/'.$c['avatar']
                : BASE_URL.'assets/img/default-avatar.png' ?>"
              class="rounded-circle me-2"
              width="35" height="35">

            <strong><?= htmlspecialchars($c['full_name']) ?></strong>

            <small class="text-muted ms-2">
              <?= date('M d, Y', strtotime($c['created_at'])) ?>
            </small>
          </div>

          <p class="mb-0"><?= nl2br(htmlspecialchars($c['body'])) ?></p>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

</div>

<!-- REPORT MODAL -->
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="reportForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Report Idea</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <textarea name="reason" class="form-control"
          placeholder="Reason for reporting..." required></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger">Submit Report</button>
      </div>
    </form>
  </div>
</div>

<script>
  const IDEA_ID = <?= $idea_id ?>;
  const IS_LOGGED_IN = <?= $user_id ? 'true' : 'false' ?>;
</script>
<script src="<?= BASE_URL ?>assets/js/idea.js"></script>

<style>
.idea-cover {
  width: 100%;
  max-height: 420px;
  overflow: hidden;
}
.idea-cover img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.comment-card {
  background: #f8fafc;
  border-left: 4px solid <?= $idea['color'] ?>;
}
</style>

<?php include '../includes/footer.php'; ?>
