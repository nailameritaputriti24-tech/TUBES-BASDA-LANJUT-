<?php
require_once("../config/koneksi.php");

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

mysqli_query($conn, "INSERT INTO users (username,password,role) 
VALUES ('$username','$password','user')");

header("Location: login.php");