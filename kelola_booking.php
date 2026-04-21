<?php 
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

require_once("../config/koneksi.php");
date_default_timezone_set('Asia/Jakarta');

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');


// ================= TAMBAH JAM =================
if(isset($_POST['tambah_jam'])){
    $lap = $_POST['lapangan_id'];
    $jam = $_POST['jam'];

    if($lap != "" && $jam != ""){
        mysqli_query($conn,"
        INSERT INTO jam (lapangan_id, jam)
        VALUES ('$lap','$jam')
        ");
    }

    header("Location: kelola_booking.php?tanggal=$tanggal");
    exit;
}


// ================= HAPUS JAM =================
if(isset($_GET['hapus_jam'])){
    $lap = $_GET['lap'];
    $jam = $_GET['jam'];

    mysqli_query($conn,"
    DELETE FROM jam 
    WHERE lapangan_id='$lap' AND jam='$jam'
    ");

    header("Location: kelola_booking.php?tanggal=$tanggal");
    exit;
}


// ================= DATA =================

// notif
$notif = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM booking WHERE tanggal = CURDATE()
"));

$pending = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM booking WHERE status='pending'
"));

// lapangan
$lapangan = mysqli_query($conn,"SELECT * FROM lapangan");

// booking
$q = mysqli_query($conn,"SELECT * FROM booking WHERE tanggal='$tanggal'");
$data_booking = [];

while($b = mysqli_fetch_assoc($q)){
    $data_booking[] = $b['lapangan_id']."|".$b['jam'];
}

// blok
$q2 = mysqli_query($conn,"SELECT * FROM blok_jam WHERE tanggal='$tanggal'");
$data_blok = [];

while($b = mysqli_fetch_assoc($q2)){
    $data_blok[] = $b['lapangan_id']."|".$b['jam'];
}

// tutup hari
$tutup = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM blok_hari WHERE tanggal='$tanggal'
"));

// pendapatan
$total = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(l.harga) as total
FROM booking b
JOIN lapangan l ON b.lapangan_id = l.id
WHERE b.tanggal='$tanggal'
"));

// lapangan terlaris
$ramai = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT lapangan_id, COUNT(*) as total 
FROM booking 
GROUP BY lapangan_id 
ORDER BY total DESC LIMIT 1
"));
?>

<?php include("layout.php"); ?>

<style>
.page-title{font-size:26px;font-weight:700;margin-bottom:20px;}
.dashboard{display:grid;grid-template-columns:3fr 1fr;gap:20px;}
.top{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:15px;}
.card{background:#1e293b;border-radius:20px;padding:15px;}
.money{background:linear-gradient(135deg,#1e293b,#334155);padding:15px;border-radius:15px;margin-bottom:15px;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;}
.jam{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-top:10px;}

.slot{padding:10px;border-radius:10px;font-size:12px;text-align:center;transition:0.2s;}
.available{background:#22c55e;color:black;}
.booked{background:#ef4444;color:white;}
.blocked{background:#64748b;color:white;}
.closed{background:#334155;color:#94a3b8;}

.slot.available:hover{
    cursor:pointer;
    background:#facc15 !important;
    color:black;
}

.side{display:flex;flex-direction:column;gap:15px;}
.progress{background:#334155;border-radius:10px;height:10px;overflow:hidden;}
.bar{height:10px;background:#22c55e;}
.badge{background:#22c55e;padding:5px 10px;border-radius:10px;font-size:12px;}
</style>

<h2 class="page-title">📅 Booking Lapangan</h2>

<div class="dashboard">

<div>

<div class="top">
<form>
<input type="date" name="tanggal" value="<?= $tanggal ?>">
<button>Lihat</button>
</form>

<form method="POST" style="display:flex;gap:8px;">
<select name="lapangan_id" required>
<option value="">Pilih Lapangan</option>
<?php 
$lap2 = mysqli_query($conn,"SELECT * FROM lapangan");
while($lp = mysqli_fetch_assoc($lap2)){
echo "<option value='".$lp['id']."'>".$lp['nama']."</option>";
}
?>
</select>

<input type="text" name="jam" placeholder="20:00-21:00" required>
<button name="tambah_jam">+ Jam</button>
</form>
</div>

<div class="money">
💰 Pendapatan: Rp <?= number_format($total['total'] ?? 0) ?>
</div>

<div class="grid">

<?php while($l = mysqli_fetch_assoc($lapangan)){ ?>

<div class="card">

<img src="https://images.unsplash.com/photo-1556056504-5c7696c4c28d" 
style="width:100%;height:140px;object-fit:cover;border-radius:10px;">

<div style="margin-top:10px;">
<b><?= $l['nama'] ?></b><br>
<span style="color:#22c55e;">Rp <?= number_format($l['harga']) ?></span>

<?php if($ramai && $ramai['lapangan_id']==$l['id']){ ?>
<div style="color:gold;">🔥 Paling Ramai</div>
<?php } ?>

<div class="jam">

<?php 
$jam_query = mysqli_query($conn,"
SELECT * FROM jam 
WHERE lapangan_id='".$l['id']."' 
ORDER BY jam ASC
");

while($j = mysqli_fetch_assoc($jam_query)){

$key = $l['id']."|".$j['jam'];
$isBooked = in_array($key,$data_booking);
$isBlocked = in_array($key,$data_blok);

// 🔥 FIX JAM LEWAT
$jam_range = explode('-', $j['jam']);
$jam_end = $jam_range[1];

$waktu_slot = strtotime($tanggal . ' ' . $jam_end . ':00');
$sekarang = time();

$isLewat = $sekarang > $waktu_slot;


if($tutup || $isLewat){
echo "<div class='slot closed'>{$j['jam']}<br>Closed</div>";
}
elseif($isBooked){

$status_q = mysqli_query($conn,"
SELECT status FROM booking 
WHERE lapangan_id='".$l['id']."' 
AND jam='".$j['jam']."' 
AND tanggal='$tanggal'
LIMIT 1
");

$status_data = mysqli_fetch_assoc($status_q);
$status = $status_data['status'];

if($status == 'pending'){
echo "<div class='slot blocked' style='background:orange;color:black;'>{$j['jam']}<br>Pending</div>";
}else{
echo "<div class='slot booked'>{$j['jam']}<br>Booked</div>";
}

}
elseif($isBlocked){
echo "<div class='slot blocked'>{$j['jam']}<br>Blocked</div>";
}
else{
echo "<div ondblclick=\"hapusJam('".$l['id']."','".$j['jam']."')\" class='slot available'>{$j['jam']}<br>Available</div>";
}

}
?>

</div>
</div>

</div>

<?php } ?>

</div>
</div>

<!-- RIGHT -->
<div class="side">

<div class="card">
🔥 Booking hari ini  
<h2><?= $notif ?></h2>

<div style="margin-top:10px;color:orange;">
🟡 Pending: <?= $pending ?>
</div>
</div>

<div class="card">
📊 Progress Booking  
<div class="progress">
<div class="bar" style="width:50%"></div>
</div>
</div>

<div class="card">
⭐ Status Sistem  
<div class="badge">Online</div>
</div>

</div>

</div>

<script>
function hapusJam(lap, jam){
if(confirm("Yakin hapus jam ini?")){
window.location = "?hapus_jam=1&lap="+lap+"&jam="+jam;
}
}
</script>