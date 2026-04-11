<?php
$host='localhost';
$user='root';
$pass='';
$db='sharestream';

$conn=new mysqli($host,$user,$pass,$db);
if($conn->connect_error){die('DB error');}
