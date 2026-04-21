<?php 
session_start();
require_once("../config/koneksi.php");

if(!isset($_SESSION['id'])){
    header("Location: ../auth/login.php");
    exit;
}

date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['id'];
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

// DATA
$lapangan = mysqli_query($conn,"SELECT * FROM lapangan");
$qBooking = mysqli_query($conn,"SELECT * FROM booking WHERE tanggal='$tanggal'");

$dataBooking = [];
while($b = mysqli_fetch_assoc($qBooking)){
    $dataBooking[] = $b;
}

// BOOKING
if(isset($_POST['booking'])){
    $file = time().'_'.$_FILES['bukti']['name'];
    move_uploaded_file($_FILES['bukti']['tmp_name'], "../upload/".$file);

    mysqli_query($conn,"
        INSERT INTO booking(user_id,lapangan_id,tanggal,jam,status,nama,hp,bayar,catatan,bukti)
        VALUES(
        '$user_id',
        '$_POST[lapangan_id]',
        '$tanggal',
        '$_POST[jam]',
        'pending',
        '$_POST[nama]',
        '$_POST[hp]',
        '$_POST[bayar]',
        '$_POST[catatan]',
        '$file')
    ");

    echo "<script>
    localStorage.setItem('notif_booking','1');
    window.location='booking.php?tanggal=$tanggal';
    </script>";
}

// HELPER
function getBooking($data,$lap,$jam){
    foreach($data as $d){
        if($d['lapangan_id']==$lap && $d['jam']==$jam){
            return $d;
        }
    }
    return null;
}

function isExpired($tanggal,$jam){
    $end = explode('-', $jam)[1];
    $end = str_replace('.', ':', $end);
    $waktu = strtotime($tanggal.' '.$end);
    return time() >= $waktu;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Booking</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
body{margin:0;font-family:Poppins;background:#020617;color:white;}
.navbar{display:flex;justify-content:space-between;padding:20px 50px;background:#0f172a;}
.menu{display:flex;gap:30px;}
.menu a{color:#cbd5f5;text-decoration:none;}
.menu a:hover{color:#22c55e;}
.title{padding:30px 50px;font-size:26px;}

.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;padding:30px 50px;}

.card{
    background:rgba(15,23,42,0.7);
    backdrop-filter:blur(12px);
    padding:18px;
    border-radius:22px;
}

.jam{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;}

.slot{
    padding:12px;
    border-radius:12px;
    text-align:center;
    font-size:13px;
}

.available{background:#22c55e;color:black;cursor:pointer;}
.booked{background:#ef4444;}
.mine{background:#a855f7;}
.pending{background:orange;color:black;}
.expired{background:#1e293b;color:#64748b;}
</style>
</head>

<body>

<div class="navbar">
    <div>⚽ FUTSAL</div>
    <div class="menu">
        <a href="dashboard.php">Dashboard</a>
        <a href="booking.php">Booking</a>
        <a href="riwayat.php">Riwayat</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="title">Booking Lapangan</div>

<div class="grid">

<?php while($l = mysqli_fetch_assoc($lapangan)){ ?>
<div class="card">

<b><?= $l['nama'] ?></b>

<div class="jam">

<?php
$jam_query = mysqli_query($conn,"SELECT * FROM jam WHERE lapangan_id='".$l['id']."'");

while($j = mysqli_fetch_assoc($jam_query)):

$bk = getBooking($dataBooking,$l['id'],$j['jam']);
$exp = isExpired($tanggal,$j['jam']);
?>

<?php if($exp){ ?>
<div class="slot expired"><?= $j['jam'] ?></div>

<?php } elseif($bk){ ?>

    <?php if($bk['status']=='pending'){ ?>
        <div class="slot pending"><?= $j['jam'] ?></div>
    <?php } elseif($bk['user_id']==$user_id){ ?>
        <div class="slot mine"><?= $j['jam'] ?></div>
    <?php } else { ?>
        <div class="slot booked"><?= $j['jam'] ?></div>
    <?php } ?>

<?php } else { ?>

<div class="slot available" onclick="openForm('<?= $l['id'] ?>','<?= $j['jam'] ?>')">
<?= $j['jam'] ?>
</div>

<?php } ?>

<?php endwhile; ?>

</div>
</div>
<?php } ?>

</div>

<!-- MODAL -->
<div id="modal" style="display:none;position:fixed;inset:0;background:#000a;justify-content:center;align-items:center;">
<div style="background:#020617;padding:25px;border-radius:20px;width:320px;">

<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="lapangan_id" id="lapangan_id">
<input type="hidden" name="jam" id="jam">

<input type="text" name="nama" placeholder="Nama" required>
<input type="text" name="hp" placeholder="No HP" required>

<select name="bayar" required>
<option value="">Metode Bayar</option>
<option value="dp">DP</option>
<option value="lunas">Lunas</option>
</select>

<input type="file" name="bukti" required>
<textarea name="catatan"></textarea>

<button name="booking">Kirim</button>
</form>

<button onclick="closeForm()">Tutup</button>

</div>
</div>

<script>
function openForm(lap,jam){
    document.getElementById('modal').style.display='flex';
    document.getElementById('lapangan_id').value=lap;
    document.getElementById('jam').value=jam;
}
function closeForm(){
    document.getElementById('modal').style.display='none';
}
</script>

</body>
</html>