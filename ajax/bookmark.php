<?php
require '../config/db.php';
session_start();
$data = json_decode(file_get_contents('php://input'), true);

$uid = $_SESSION['user_id'];
$idea = (int)$data['idea_id'];

$chk = $conn->query("SELECT id FROM bookmarks WHERE user_id=$uid AND idea_id=$idea");
if ($chk->num_rows) {
  $conn->query("DELETE FROM bookmarks WHERE user_id=$uid AND idea_id=$idea");
  echo json_encode(['bookmarked'=>false]);
} else {
  $conn->query("INSERT INTO bookmarks (user_id,idea_id) VALUES ($uid,$idea)");
  echo json_encode(['bookmarked'=>true]);
}
