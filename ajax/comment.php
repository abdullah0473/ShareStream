<?php
require '../config/db.php';
session_start();
$data = json_decode(file_get_contents('php://input'), true);

$uid = $_SESSION['user_id'];
$idea = (int)$data['idea_id'];
$body = trim($data['body']);

$stmt = $conn->prepare("INSERT INTO comments (idea_id,user_id,body) VALUES (?,?,?)");
$stmt->bind_param("iis",$idea,$uid,$body);
$stmt->execute();

echo "<div class='card mb-3 comment-card'><div class='card-body'><strong>You</strong><p>$body</p></div></div>";
