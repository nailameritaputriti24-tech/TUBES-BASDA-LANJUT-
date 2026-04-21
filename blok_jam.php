<?php
require_once("../../config/koneksi.php");

$id = $_GET['lapangan'];
$tgl = $_GET['tgl'];
$jam = $_GET['jam'];

// biar gak double
$cek = mysqli_query($conn,"
SELECT * FROM blok_jam 
WHERE lapangan_id='$id' 
AND tanggal='$tgl' 
AND jam='$jam'
");

if(mysqli_num_rows($cek) == 0){
    mysqli_query($conn,"
    INSERT INTO blok_jam(lapangan_id,tanggal,jam)
    VALUES('$id','$tgl','$jam')
    ");
}

header("Location: ../kelola_booking.php?tanggal=".$tgl);