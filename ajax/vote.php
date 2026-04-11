<?php
require '../config/db.php';
session_start();
$data = json_decode(file_get_contents('php://input'), true);

$user = $_SESSION['user_id'] ?? 0;
$idea = (int)$data['idea_id'];

$conn->query("INSERT IGNORE INTO votes (user_id, idea_id) VALUES ($user,$idea)");
$r = $conn->query("SELECT COUNT(*) c FROM votes WHERE idea_id=$idea")->fetch_assoc();

echo json_encode(['success'=>true,'votes'=>$r['c']]);
