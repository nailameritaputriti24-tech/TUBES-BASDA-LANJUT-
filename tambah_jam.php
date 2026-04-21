<?php
require_once("../../config/koneksi.php");

$lapangan_id = $_POST['lapangan_id'];
$tanggal = $_POST['tanggal'];
$jam = $_POST['jam'];

// cek biar gak double
$cek = mysqli_query($conn,"
SELECT * FROM blok_jam 
WHERE lapangan_id='$lapangan_id' 
AND tanggal='$tanggal' 
AND jam='$jam'
");

if(mysqli_num_rows($cek) == 0){
    mysqli_query($conn,"
    INSERT INTO blok_jam(lapangan_id,tanggal,jam)
    VALUES('$lapangan_id','$tanggal','$jam')
    ");
}

header("Location: ../kelola_booking.php?tanggal=".$tanggal);