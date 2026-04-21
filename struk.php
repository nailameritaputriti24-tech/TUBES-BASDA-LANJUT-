<?php
require_once("../config/koneksi.php");

$id = $_GET['id'];

$q = mysqli_query($conn,"
SELECT b.*, l.nama as lapangan 
FROM booking b
JOIN lapangan l ON b.lapangan_id = l.id
WHERE b.id='$id'
");

$d = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html>
<head>
<title>Struk</title>

<style>
body{
    font-family:Poppins;
    background:#0f172a;
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.struk{
    width:350px;
    background:#1e293b;
    padding:20px;
    border-radius:20px;
}

.title{
    text-align:center;
    font-weight:700;
    margin-bottom:15px;
}

.row{
    display:flex;
    justify-content:space-between;
    margin-bottom:8px;
}

.status{
    text-align:center;
    margin-top:15px;
    padding:10px;
    border-radius:10px;
    background:#22c55e;
    color:black;
}
</style>
</head>

<body>

<div class="struk">

<div class="title">STRUK FUTSAL</div>

<div class="row"><span>Nama</span><span><?= $d['nama'] ?></span></div>
<div class="row"><span>Lapangan</span><span><?= $d['lapangan'] ?></span></div>
<div class="row"><span>Tanggal</span><span><?= $d['tanggal'] ?></span></div>
<div class="row"><span>Jam</span><span><?= $d['jam'] ?></span></div>
<div class="row"><span>Pembayaran</span><span><?= $d['bayar'] ?></span></div>

<div class="status">
<?= strtoupper($d['status']) ?>
</div>

</div>

</body>
</html>