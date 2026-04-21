<?php
include '../config/koneksi.php';
session_start();

$user = $_SESSION['id_user'];
$lapangan = $_POST['lapangan'];
$tanggal = $_POST['tanggal'];
$jam = $_POST['jam'];
$durasi = $_POST['durasi'];

mysqli_query($conn, "CALL tambah_booking('$user','$lapangan','$tanggal','$jam','$durasi')");

header("Location: ../user/riwayat.php");
?>