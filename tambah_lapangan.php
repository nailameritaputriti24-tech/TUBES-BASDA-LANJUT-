<?php
include '../config/koneksi.php';

$nama = $_POST['nama'];
$harga = $_POST['harga'];

mysqli_query($conn, "INSERT INTO lapangan(nama_lapangan, harga_perjam) VALUES('$nama','$harga')");

header("Location: ../admin/kelola_lapangan.php");
?>