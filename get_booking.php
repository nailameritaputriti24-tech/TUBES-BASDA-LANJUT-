<?php
require_once("../config/koneksi.php");

$lap = $_GET['lap'];
$tgl = $_GET['tgl'];
$jam = $_GET['jam'];

$q = mysqli_query($conn,"
SELECT b.*, u.username, l.nama as lapangan
FROM booking b
LEFT JOIN users u ON b.user_id = u.id
LEFT JOIN lapangan l ON b.lapangan_id = l.id
WHERE b.lapangan_id='$lap' 
AND b.tanggal='$tgl' 
AND b.jam='$jam'
");

echo json_encode(mysqli_fetch_assoc($q));