<?php
session_start();

if(!isset($_SESSION['login']) || $_SESSION['role'] != 'user'){
    header("Location: ../auth/login.php");
    exit;
}

require_once("../config/koneksi.php");

$user_id = $_SESSION['id'];

// 🔥 HAPUS BOOKING
if(isset($_POST['hapus'])){
    $id = $_POST['id'];

    mysqli_query($conn,"DELETE FROM booking WHERE id='$id' AND user_id='$user_id'");
    
    echo "<script>alert('Booking berhasil dihapus');</script>";
    echo "<script>window.location='riwayat.php'</script>";
    exit;
}

// ambil data
$data = mysqli_query($conn,"
SELECT b.*, l.nama as lapangan 
FROM booking b
JOIN lapangan l ON b.lapangan_id = l.id
WHERE b.user_id='$user_id'
ORDER BY b.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Riwayat Booking</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:Poppins;
    background:linear-gradient(rgba(2,6,23,0.9),rgba(2,6,23,0.95)),
    url('https://images.unsplash.com/photo-1574629810360-7efbbe195018');
    background-size:cover;
    color:white;
}

/* CURSOR */
#cursor-glow{
    position:fixed;
    width:300px;
    height:300px;
    background:radial-gradient(circle,#22c55e55,transparent);
    border-radius:50%;
    pointer-events:none;
    transform:translate(-50%,-50%);
    filter:blur(70px);
}

/* NAVBAR */
.navbar{
    display:flex;
    gap:20px;
    padding:15px 30px;
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(10px);
}

.navbar a{
    color:white;
    text-decoration:none;
    font-weight:500;
}

.navbar a:hover{
    color:#22c55e;
}

/* HEADER */
.header{
    padding:40px;
    text-align:center;
}

/* CARD */
.container{
    width:90%;
    margin:auto;
}

.card{
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(20px);
    border-radius:20px;
    padding:20px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

th{
    text-align:left;
    padding:12px;
    color:#94a3b8;
}

td{
    padding:12px;
    border-bottom:1px solid rgba(255,255,255,0.1);
}

/* STATUS */
.status{
    padding:6px 12px;
    border-radius:10px;
    font-size:12px;
    font-weight:600;
}

.pending{background:orange;}
.approved{background:#22c55e;color:black;}
.rejected{background:#ef4444;}

/* BUTTON */
.btn{
    border:none;
    padding:8px 12px;
    border-radius:10px;
    cursor:pointer;
}

.btn-hapus{
    background:#ef4444;
    color:white;
}

/* EMPTY */
.empty{
    text-align:center;
    padding:20px;
    color:#94a3b8;
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
    <a href="struk.php?id=<?= $d['id'] ?>" target="_blank">
    <button>Download Struk</button>
</a>
</div>

<div id="cursor-glow"></div>

<div class="header">
    <h1>📅 Riwayat Booking</h1>
    <p>Halo <?= $_SESSION['username'] ?> 👋</p>
</div>

<div class="container">
<div class="card">

<table>
<tr>
    <th>Lapangan</th>
    <th>Tanggal</th>
    <th>Jam</th>
    <th>Bayar</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php 
if(mysqli_num_rows($data) == 0){
    echo "<tr><td colspan='6' class='empty'>Belum ada booking 😴</td></tr>";
}else{
    while($d = mysqli_fetch_assoc($data)){ 
    
    $bayar = $d['bayar'] ?? '-';
?>
<tr>
    <td><?= $d['lapangan'] ?></td>
    <td><?= $d['tanggal'] ?></td>
    <td><?= $d['jam'] ?></td>

    <!-- BAYAR FIX -->
    <td>
        <?php if($bayar == 'dp'){ ?>
            <span style="color:orange;">DP</span>
        <?php } elseif($bayar == 'lunas'){ ?>
            <span style="color:#22c55e;">Lunas</span>
        <?php } else { ?>
            -
        <?php } ?>
    </td>

    <!-- STATUS -->
    <td>
        <span class="status <?= $d['status'] ?>">
            <?= $d['status'] ?>
        </span>
    </td>

    <!-- HAPUS -->
    <td>
        <form method="POST" onsubmit="return confirm('Yakin hapus booking ini?')">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">
            <button class="btn btn-hapus" name="hapus">Hapus</button>
        </form>
    </td>

</tr>

<?php } } ?>

</table>

</div>
</div>

<script>
const glow = document.getElementById("cursor-glow");

document.addEventListener("mousemove", e=>{
    glow.style.left = e.clientX + "px";
    glow.style.top = e.clientY + "px";
});
</script>

</body>
</html>