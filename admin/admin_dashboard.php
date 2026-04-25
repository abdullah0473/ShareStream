<?php
require '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$user_id=$_SESSION['user_id'];

/* VERIFY ADMIN */
$stmt=$conn->prepare("SELECT role FROM users WHERE id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
if($stmt->get_result()->fetch_assoc()['role']!=='admin'){
  header('Location: dashboard.php'); exit;
}

include '../includes/header.php';

/* TOGGLE STATUS */
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['toggle_id'])){
  $id=(int)$_POST['toggle_id'];
  $conn->query("UPDATE ideas SET status=IF(status='active','removed','active') WHERE id=$id");
}

/* STATS */
$totalIdeas=$conn->query("SELECT COUNT(*) c FROM ideas")->fetch_assoc()['c'];
$activeIdeas=$conn->query("SELECT COUNT(*) c FROM ideas WHERE status='active'")->fetch_assoc()['c'];
$removedIdeas=$conn->query("SELECT COUNT(*) c FROM ideas WHERE status='removed'")->fetch_assoc()['c'];
$totalReports=$conn->query("SELECT COUNT(*) c FROM reports")->fetch_assoc()['c'];

/* IDEAS */
$ideas=$conn->query("
  SELECT i.*,u.full_name,
  (SELECT COUNT(*) FROM reports WHERE idea_id=i.id) reports
  FROM ideas i JOIN users u ON u.id=i.user_id
  ORDER BY i.created_at DESC
");

/* BOOKMARKS */
$bookmarks=$conn->query("
  SELECT b.*,u.full_name,i.title
  FROM bookmarks b
  JOIN users u ON u.id=b.user_id
  JOIN ideas i ON i.id=b.idea_id
");

/* REPORTS */
$reports=$conn->query("
  SELECT r.*,u.full_name,i.title
  FROM reports r
  JOIN users u ON u.id=r.reporter_id
  JOIN ideas i ON i.id=r.idea_id
  ORDER BY r.created_at DESC
");
?>

<div class="container my-5">

  <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

  <h2 class="mb-4">Admin Dashboard</h2>

  <!-- KPIs -->
  <div class="row g-4 mb-4">
    <?php foreach([
      'Total Ideas'=>$totalIdeas,
      'Active'=>$activeIdeas,
      'Removed'=>$removedIdeas,
      'Reports'=>$totalReports
    ] as $k=>$v): ?>
      <div class="col-md-3"><div class="card"><div class="card-body"><h6><?= $k ?></h6><h3><?= $v ?></h3></div></div></div>
    <?php endforeach; ?>
  </div>

  <!-- IDEAS TABLE -->
  <div class="card mb-5">
    <div class="card-body">
      <h4>Ideas Moderation</h4>
      <table class="table align-middle">
        <thead><tr><th>Title</th><th>User</th><th>Status</th><th>Reports</th><th>Actions</th></tr></thead>
        <tbody>
        <?php while($i=$ideas->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($i['title']) ?></td>
            <td><?= htmlspecialchars($i['full_name']) ?></td>
            <td><?= $i['status'] ?></td>
            <td><?= $i['reports'] ?></td>
            <td>
              <a href="../public/idea.php?id=<?= $i['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
              <form method="post" class="d-inline">
                <input type="hidden" name="toggle_id" value="<?= $i['id'] ?>">
                <button class="btn btn-sm btn-outline-danger">Toggle</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- BOOKMARKS -->
  <div class="card mb-5">
    <div class="card-body">
      <h4>Bookmarks</h4>
      <table class="table">
        <thead><tr><th>User</th><th>Idea</th><th>Date</th></tr></thead>
        <tbody>
          <?php while($b=$bookmarks->fetch_assoc()): ?>
            <tr>
              <td><?= $b['full_name'] ?></td>
              <td><?= $b['title'] ?></td>
              <td><?= $b['created_at'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- REPORTS -->
  <div class="card">
    <div class="card-body">
      <h4>Reports</h4>
      <table class="table table-striped">
        <thead><tr><th>Idea</th><th>User</th><th>Reason</th><th>Date</th></tr></thead>
        <tbody>
          <?php while($r=$reports->fetch_assoc()): ?>
            <tr>
              <td><?= $r['title'] ?></td>
              <td><?= $r['full_name'] ?></td>
              <td><?= htmlspecialchars($r['reason']) ?></td>
              <td><?= $r['created_at'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php include '../includes/footer.php'; ?>