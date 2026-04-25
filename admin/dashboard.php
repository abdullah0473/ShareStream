<?php
require '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

include '../includes/header.php';

$user_id = $_SESSION['user_id'];

/* USER ROLE */
$stmt = $conn->prepare("SELECT role FROM users WHERE id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$userRole = $stmt->get_result()->fetch_assoc()['role'];
$is_admin = ($userRole === 'admin');

/* USER STATS */
$myIdeas = $conn->query("SELECT COUNT(*) c FROM ideas WHERE user_id=$user_id")->fetch_assoc()['c'];
$myBookmarks = $conn->query("SELECT COUNT(*) c FROM bookmarks WHERE user_id=$user_id")->fetch_assoc()['c'];
$myComments = $conn->query("SELECT COUNT(*) c FROM comments WHERE user_id=$user_id")->fetch_assoc()['c'];
$myVotes = $conn->query("
  SELECT COUNT(*) c FROM votes v
  JOIN ideas i ON i.id=v.idea_id
  WHERE i.user_id=$user_id
")->fetch_assoc()['c'];

/* USER IDEAS */
$userIdeas = $conn->query("
  SELECT i.*,
    (SELECT COUNT(*) FROM votes WHERE idea_id=i.id) votes,
    (SELECT COUNT(*) FROM comments WHERE idea_id=i.id) comments,
    (SELECT COUNT(*) FROM reports WHERE idea_id=i.id) reports,
    (SELECT COUNT(*) FROM bookmarks WHERE idea_id=i.id) bookmarks
  FROM ideas i
  WHERE i.user_id=$user_id
  ORDER BY i.created_at DESC
");

/* USER BOOKMARKS */
$userBookmarks = $conn->query("
  SELECT b.created_at, i.id, i.title
  FROM bookmarks b
  JOIN ideas i ON i.id = b.idea_id
  WHERE b.user_id = $user_id
  ORDER BY b.created_at DESC
");

/* USER REPORTS */
$userReports = $conn->query("
  SELECT r.created_at, r.reason, r.resolved, i.id, i.title
  FROM reports r
  JOIN ideas i ON i.id = r.idea_id
  WHERE r.reporter_id = $user_id
  ORDER BY r.created_at DESC
");

/* USER CHART */
$userChart=[];
$r=$conn->query("
  SELECT DATE(created_at) d, COUNT(*) c
  FROM ideas WHERE user_id=$user_id
  GROUP BY d ORDER BY d
");
while($x=$r->fetch_assoc()) $userChart[]=$x;
?>

<div class="container my-5">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard</h2>
    <?php if($is_admin): ?>
      <a href="admin_dashboard.php" class="btn btn-dark">Admin Dashboard</a>
    <?php endif; ?>
  </div>

  <!-- KPIs -->
  <div class="row g-4 mb-4">
    <?php foreach([
      'My Ideas'=>$myIdeas,
      'Votes'=>$myVotes,
      'Bookmarks'=>$myBookmarks,
      'Comments'=>$myComments
    ] as $k=>$v): ?>
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <h6><?= $k ?></h6>
            <h3><?= $v ?></h3>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- CHART -->
  <div class="card mb-5">
    <div class="card-body">
      <h5>My Ideas Over Time</h5>
      <canvas id="userChart"></canvas>
    </div>
  </div>

  <!-- MY IDEAS -->
  <div class="card mb-5">
    <div class="card-body">
      <h4>My Ideas</h4>
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Title</th><th>Status</th><th>Votes</th>
            <th>Comments</th><th>Reports</th><th>Bookmarks</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($i=$userIdeas->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($i['title']) ?></td>
            <td>
              <span class="badge <?= $i['status']=='active'?'bg-success':'bg-danger' ?>">
                <?= $i['status'] ?>
              </span>
            </td>
            <td><?= $i['votes'] ?></td>
            <td><?= $i['comments'] ?></td>
            <td><?= $i['reports'] ?></td>
            <td><?= $i['bookmarks'] ?></td>
            <td>
              <a href="../public/idea.php?id=<?= $i['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
              <a href="edit_idea.php?id=<?= $i['id'] ?>" class="btn btn-sm btn-outline-warning">Edit</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- MY BOOKMARKS -->
  <div class="card mb-5">
    <div class="card-body">
      <h4>My Bookmarks</h4>

      <table class="table align-middle">
        <thead>
          <tr>
            <th>Idea</th>
            <th>Bookmarked On</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($userBookmarks->num_rows === 0): ?>
            <tr><td colspan="3" class="text-muted">No bookmarks yet.</td></tr>
          <?php endif; ?>

          <?php while($b=$userBookmarks->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($b['title']) ?></td>
              <td><?= date('M d, Y', strtotime($b['created_at'])) ?></td>
              <td>
                <a href="../public/idea.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary">
                  View
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- MY REPORTS -->
  <div class="card mb-5">
    <div class="card-body">
      <h4>My Reports</h4>

      <table class="table align-middle">
        <thead>
          <tr>
            <th>Idea</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Reported On</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($userReports->num_rows === 0): ?>
            <tr><td colspan="5" class="text-muted">You have not reported any ideas.</td></tr>
          <?php endif; ?>

          <?php while($r=$userReports->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($r['title']) ?></td>
              <td><?= htmlspecialchars($r['reason']) ?></td>
              <td>
                <span class="badge <?= $r['resolved']=='yes'?'bg-success':'bg-warning' ?>">
                  <?= $r['resolved']=='yes'?'Resolved':'Pending' ?>
                </span>
              </td>
              <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
              <td>
                <a href="../public/idea.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">
                  View
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('userChart'),{
  type:'line',
  data:{
    labels:<?= json_encode(array_column($userChart,'d')) ?>,
    datasets:[{
      label:'Ideas',
      data:<?= json_encode(array_column($userChart,'c')) ?>,
      borderColor:'#2563eb',
      tension:.3
    }]
  }
});
</script>

<?php include '../includes/footer.php'; ?>