<?php
session_start();// ================= SESSION AMAN =================
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

// ================= VALIDASI LOGIN =================
if(!isset($_SESSION['id'])){
    header("Location: ../auth/login.php");
    exit;
}

// ================= KONEKSI =================
require_once("../config/koneksi.php");

$user_id = $_SESSION['id'];
$username = $_SESSION['username'] ?? 'User'; // ✅ anti error

// ================= NOTIF =================
$notif = mysqli_query($conn,"
SELECT * FROM booking 
WHERE user_id='$user_id' 
AND status!='pending'
");

if(!$notif){
    die("Query notif error: " . mysqli_error($conn));
}

// ================= DATA =================
$lapangan = mysqli_query($conn,"SELECT * FROM lapangan");

$booking = mysqli_query($conn,"
SELECT b.*, l.nama 
FROM booking b
JOIN lapangan l ON b.lapangan_id = l.id
WHERE b.user_id='$user_id'
ORDER BY b.id DESC LIMIT 3
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:Poppins;
    background:
        radial-gradient(circle at 20% 20%, #22c55e20, transparent 40%),
        radial-gradient(circle at 80% 30%, #22c55e15, transparent 40%),
        linear-gradient(rgba(2,6,23,0.9),rgba(2,6,23,0.95)),
        url('https://images.unsplash.com/photo-1574629810360-7efbbe195018');
    background-size:cover;
    color:white;
}

.navbar{
    display:flex;
    gap:30px;
    padding:18px 40px;
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(15px);
    border-bottom:1px solid rgba(255,255,255,0.08);
}

.navbar a{
    color:white;
    text-decoration:none;
    font-weight:500;
}

.navbar a:hover{
    color:#22c55e;
}

.header{
    text-align:center;
    padding:80px 20px 50px;
}

.header h1{
    font-size:42px;
    font-weight:700;
    text-shadow:0 0 20px rgba(255,255,255,0.3),
                0 0 40px rgba(34,197,94,0.3);
}

.btn-main{
    background:linear-gradient(135deg,#22c55e,#4ade80);
    border:none;
    padding:14px 35px;
    border-radius:40px;
    cursor:pointer;
    margin-top:20px;
    font-weight:600;
    color:black;
}

.btn-main:hover{
    transform:scale(1.08);
}

.container{
    width:90%;
    margin:auto;
}

.grid{
    display:grid;
    grid-template-columns:1.6fr 1fr;
    gap:30px;
}

.card{
    background:rgba(255,255,255,0.04);
    backdrop-filter:blur(25px);
    border-radius:25px;
    padding:30px;
    border:1px solid rgba(255,255,255,0.08);
    box-shadow:0 20px 60px rgba(0,0,0,0.7);
    transition:0.3s;
}
.card:hover{
    transform:translateY(-8px);
    box-shadow:0 30px 80px rgba(34,197,94,0.25);
}
.lapangan{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
}

.item{
    padding:25px 15px;
    border-radius:20px;
    background:rgba(255,255,255,0.05);
    text-align:center;
    transition:0.3s;
    position:relative;
    overflow:hidden;
}
/* glow effect */
.item::before{
    content:'';
    position:absolute;
    width:200%;
    height:200%;
    background:radial-gradient(circle,#22c55e30,transparent);
    top:-50%;
    left:-50%;
    opacity:0;
    transition:0.4s;
}

.item:hover::before{
    opacity:1;
}

.item:hover{
    transform:translateY(-10px) scale(1.05);
    box-shadow:0 15px 40px rgba(34,197,94,0.25);
}
.price{
    color:#22c55e;
    margin-top:8px;
}

.riwayat-item{
    display:flex;
    justify-content:space-between;
    padding:14px 0;
    border-bottom:1px solid rgba(255,255,255,0.1);
}
/* ===== BUTTON ===== */
.btn-main{
    background:linear-gradient(135deg,#22c55e,#4ade80);
    border:none;
    padding:14px 35px;
    border-radius:40px;
    cursor:pointer;
    margin-top:20px;
    font-weight:600;
    color:black;
    box-shadow:0 0 20px #22c55e60;
    transition:0.3s;
}

.btn-main:hover{
    transform:scale(1.08);
    box-shadow:0 0 40px #22c55e;
}
.btn-small{
    margin-top:20px;
    padding:12px 25px;
    border:none;
    border-radius:30px;
    background:#22c55e;
    cursor:pointer;
    color:black;
}

.notif{
    background:#1e293b;
    border-left:5px solid #22c55e;
    padding:12px;
    margin:10px 40px;
    border-radius:10px;
}

.notif.reject{
    border-left:5px solid red;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="booking.php">📅 Booking</a>
    <a href="riwayat.php">📊 Riwayat</a>
    <a href="../auth/logout.php">🚪 Logout</a>
</div>

<!-- 🔔 NOTIF -->
<?php if($notif && mysqli_num_rows($notif) > 0){ ?>
    <?php while($n = mysqli_fetch_assoc($notif)){ ?>
        <div class="notif <?= ($n['status']=='rejected') ? 'reject' : '' ?>">
            <?php if($n['status']=='approved'){ ?>
                ✅ Booking jam <?= $n['jam'] ?> disetujui admin
            <?php } else { ?>
                ❌ Booking jam <?= $n['jam'] ?> ditolak admin
            <?php } ?>
        </div>
    <?php } ?>
<?php } ?>

<?php
// tandai notif sudah dibaca
mysqli_query($conn,"
UPDATE booking 
SET notif='1' 
WHERE user_id='$user_id'
");
?>

<!-- HEADER -->
<div class="header">
    <h1>⚽ Futsal Booking</h1>
    <p>Selamat datang, <?= $username ?> 👋</p>

    <a href="booking.php">
        <button class="btn-main">Booking Sekarang</button>
    </a>
</div>

<div class="container">

<div class="grid">

<!-- LAPANGAN -->
<div class="card">
    <h2>✨ Lapangan</h2>

    <div class="lapangan">
        <?php while($l = mysqli_fetch_assoc($lapangan)){ ?>
        <div class="item">
            <h3><?= $l['nama'] ?></h3>
            <div class="price">Rp <?= number_format($l['harga']) ?></div>
        </div>
        <?php } ?>
    </div>
</div>

<!-- RIWAYAT -->
<div class="card">
    <h2>📅 Riwayat</h2>

    <?php 
    if(mysqli_num_rows($booking) == 0){
        echo "<p style='color:#94a3b8'>Belum ada booking</p>";
    }else{
        while($b = mysqli_fetch_assoc($booking)){ ?>
        <div class="riwayat-item">
            <span><?= $b['nama'] ?></span>
            <span><?= $b['jam'] ?> (<?= $b['status'] ?>)</span>
        </div>
    <?php } } ?>

    <a href="riwayat.php">
        <button class="btn-small">Lihat Semua</button>
    </a>
</div>

</div>

</div>

</body>
</html>