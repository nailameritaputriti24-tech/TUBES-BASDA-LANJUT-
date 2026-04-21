<?php
require_once("../../config/koneksi.php");

$id = $_GET['lapangan'];
$tgl = $_GET['tgl'];
$jam = $_GET['jam'];

mysqli_query($conn,"
DELETE FROM blok_jam 
WHERE lapangan_id='$id' 
AND tanggal='$tgl' 
AND jam='$jam'
");

header("Location: ../kelola_booking.php?tanggal=".$tgl);