<?php 
require '../config/db.php';
include '../includes/header.php';

$sql = "
  SELECT 
    ideas.id, 
    ideas.title, 
    ideas.summary,
    (SELECT COUNT(*) FROM votes WHERE votes.idea_id = ideas.id) AS votes,
    categories.name AS category
  FROM ideas
  JOIN categories ON categories.id = ideas.category_id
  WHERE ideas.status = 'active'
  ORDER BY ideas.created_at DESC
  LIMIT 6
";
$res = $conn->query($sql);
?>

<section class="hero">
  <div class="container hero-content">
    <h1>Ideas rise when people vote</h1>
    <p>Share ideas, support what matters, and help the best ideas reach the top.</p>

    <div class="hero-cta">
      <?php if(!isset($_SESSION['user_id'])): ?>
        <a href="register.php" class="btn-cta">Get Started</a>
      <?php else: ?>
        <a href="ideas.php" class="btn-cta">Explore Ideas</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section container">
  <h2 class="section-title">Top Ideas This Week</h2>

  <?php if($res->num_rows === 0): ?>
    <div class="no-ideas">
      <p>No ideas posted yet. Be the first to share an idea.</p>
    </div>
  <?php else: ?>

    <div class="idea-grid">
      <?php while($row = $res->fetch_assoc()): ?>

        <!-- CLICKABLE IDEA CARD -->
        <a href="idea.php?id=<?= $row['id'] ?>" class="idea-link">
          <article class="idea-card">

            <div class="vote">
              <i class="fa-solid fa-chevron-up"></i>
              <div><?= (int)$row['votes'] ?></div>
            </div>

            <div class="idea-body">
              <span class="tag"><?= htmlspecialchars($row['category']) ?></span>
              <h3><?= htmlspecialchars($row['title']) ?></h3>
              <p><?= htmlspecialchars($row['summary']) ?></p>
            </div>

          </article>
        </a>

      <?php endwhile; ?>
    </div>

  <?php endif; ?>
</section>

<section class="section container">
  <h2 class="section-title">Our Mission</h2>
  <p>
    ShareStream empowers communities to share ideas, discuss openly, and vote on
    solutions that matter most.
  </p>
</section>

<section class="section container">
  <div class="features">
    <div class="feature-card">
      <i class="fa-solid fa-lightbulb"></i>
      <h4>Share Ideas</h4>
      <p>Post ideas easily and reach the community.</p>
    </div>

    <div class="feature-card">
      <i class="fa-solid fa-thumbs-up"></i>
      <h4>Vote & Rank</h4>
      <p>Support ideas you believe in.</p>
    </div>

    <div class="feature-card">
      <i class="fa-solid fa-comments"></i>
      <h4>Discuss</h4>
      <p>Engage in meaningful discussions.</p>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
