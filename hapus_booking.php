<?php
require_once("../config/koneksi.php");

$id = $_POST['id'];

mysqli_query($conn,"DELETE FROM booking WHERE id='$id'");

header("Location: dashboard.php");