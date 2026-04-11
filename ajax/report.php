<?php
require '../config/db.php';
session_start();
$data = json_decode(file_get_contents('php://input'), true);

$uid = $_SESSION['user_id'];
$idea = (int)$data['idea_id'];
$reason = trim($data['reason']);

$stmt = $conn->prepare("INSERT INTO reports (reporter_id,idea_id,reason) VALUES (?,?,?)");
$stmt->bind_param("iis",$uid,$idea,$reason);
$stmt->execute();
