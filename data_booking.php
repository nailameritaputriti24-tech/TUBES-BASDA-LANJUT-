<?php
session_start();
require_once("../config/koneksi.php");

// ================= ACTION =================
// 🔥 HAPUS BOOKING
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];

    mysqli_query($conn,"DELETE FROM booking WHERE id='$id'");

    header("Location: data_booking.php");
    exit;
}
// APPROVE
if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    mysqli_query($conn,"UPDATE booking SET status='approved' WHERE id='$id'");
    header("Location: data_booking.php");
    exit;
}

// REJECT
if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    mysqli_query($conn,"UPDATE booking SET status='rejected' WHERE id='$id'");
    header("Location: data_booking.php");
    exit;
}

// ================= DATA =================
$q = mysqli_query($conn,"
SELECT b.*, l.nama as lapangan
FROM booking b
JOIN lapangan l ON b.lapangan_id = l.id
ORDER BY b.id DESC
");
?>

<?php include("layout.php"); ?>

<style>

/* TITLE */
.page-title{
    font-size:28px;
    font-weight:700;
    margin-bottom:25px;
}

/* CARD */
.card{
    background:linear-gradient(145deg,#0f172a,#020617);
    border-radius:25px;
    padding:25px;
    box-shadow:0 20px 60px rgba(0,0,0,0.7);
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

/* HEADER */
th{
    text-align:left;
    padding:16px;
    font-size:13px;
    color:#94a3b8;
}

/* ROW */
td{
    padding:16px;
    border-top:1px solid rgba(255,255,255,0.05);
}

/* HOVER */
tr:hover{
    background:rgba(255,255,255,0.03);
}

/* STATUS */
.badge{
    padding:6px 14px;
    border-radius:999px;
    font-size:12px;
    font-weight:500;
}

.pending{
    background:#facc15;
    color:black;
}

.approved{
    background:#22c55e;
    color:white;
}

.rejected{
    background:#ef4444;
    color:white;
}

/* BUTTON */
.btn{
    padding:6px 14px;
    border-radius:10px;
    text-decoration:none;
    color:white;
    font-size:12px;
    margin-right:6px;
    transition:0.2s;
}

.approve{
    background:#22c55e;
}

.reject{
    background:#ef4444;
}

.btn:hover{
    transform:scale(1.05);
    opacity:0.9;
}
.delete{
    background:#dc2626;
}
/* POPUP BACKGROUND */
.popup-bg{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.7);
    backdrop-filter:blur(5px);
    justify-content:center;
    align-items:center;
    z-index:999;
}

/* BOX */
.popup-box{
    background:linear-gradient(145deg,#0f172a,#020617);
    padding:30px;
    border-radius:20px;
    width:320px;
    text-align:center;
    animation:zoomIn 0.3s ease;
    box-shadow:0 20px 60px rgba(0,0,0,0.8);
}

/* ANIMASI */
@keyframes zoomIn{
    from{transform:scale(0.7);opacity:0;}
    to{transform:scale(1);opacity:1;}
}

/* BUTTON */
.popup-btn{
    margin-top:20px;
    display:flex;
    justify-content:center;
    gap:10px;
}

.btn-yes{
    background:#ef4444;
    border:none;
    padding:10px 15px;
    border-radius:10px;
    color:white;
    cursor:pointer;
}

.btn-no{
    background:#334155;
    border:none;
    padding:10px 15px;
    border-radius:10px;
    color:white;
    cursor:pointer;
}

.btn-yes:hover{opacity:0.8;}
.btn-no:hover{opacity:0.8;}
</style>

<div class="main-content">

<h2 class="page-title">📊 Data Booking</h2>

<div class="card">

<table>

<thead>
<tr>
<th>Nama</th>
<th>Lapangan</th>
<th>Tanggal</th>
<th>Jam</th>
<th>Status</th>
<th>Bukti</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<?php while($b = mysqli_fetch_assoc($q)){ ?>
<tr>

<td><?= $b['nama'] ?></td>
<td><?= $b['lapangan'] ?></td>
<td><?= $b['tanggal'] ?></td>
<td><?= $b['jam'] ?></td>

<td>
<span class="badge <?= $b['status'] ?>">
<?= ucfirst($b['status']) ?>
</span>
</td>
<td>
<?php if($b['bukti']){ ?>
    
    <?php 
    $file = "../upload/".$b['bukti'];
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    ?>

    <?php if(in_array($ext, ['jpg','jpeg','png'])){ ?>
        <img src="<?= $file ?>" width="60" style="border-radius:8px;cursor:pointer"
        onclick="previewImage('<?= $file ?>')">
    <?php } else { ?>
        <a href="<?= $file ?>" target="_blank" class="btn">Lihat File</a>
    <?php } ?>

<?php } else { ?>
    <span style="color:#64748b;">Tidak ada</span>
<?php } ?>
</td>

<td>

<?php if($b['status'] == 'pending'){ ?>
<a class="btn approve" href="?approve=<?= $b['id'] ?>">Approve</a>
<a class="btn reject" href="?reject=<?= $b['id'] ?>">Reject</a>
<?php } ?>

<a class="btn delete" href="javascript:void(0)" onclick="openPopup(<?= $b['id'] ?>)">
Hapus
</a>

</td>

</tr>
<?php } ?>
<div id="modalImg" style="
display:none;
position:fixed;
top:0;left:0;
width:100%;height:100%;
background:#000000cc;
justify-content:center;
align-items:center;
z-index:999;
">
    <img id="imgPreview" style="max-width:80%;border-radius:15px;">
</div>

<script>
function previewImage(src){
    document.getElementById('modalImg').style.display='flex';
    document.getElementById('imgPreview').src = src;
}

document.getElementById('modalImg').onclick = function(){


</script>
</tbody>

</table>

</div>

</div>
</tbody>
</table>

</div>
</div>

<!-- 🔥 POPUP HAPUS -->
<div id="popupHapus" class="popup-bg">
  <div class="popup-box">
    <h3>⚠️ Hapus Data</h3>
    <p>Yakin mau hapus data ini?</p>

    <div class="popup-btn">
      <button onclick="confirmHapus()" class="btn-yes">Ya, Hapus</button>
      <button onclick="closePopup()" class="btn-no">Batal</button>
    </div>
  </div>
</div>

<!-- 🔥 MODAL PREVIEW GAMBAR -->
<div id="modalImg" style="
display:none;
position:fixed;
top:0;left:0;
width:100%;height:100%;
background:#000000cc;
justify-content:center;
align-items:center;
z-index:999;
">
    <img id="imgPreview" style="max-width:80%;border-radius:15px;">
</div>

<script>
// 🔥 PREVIEW GAMBAR
function previewImage(src){
    document.getElementById('modalImg').style.display='flex';
    document.getElementById('imgPreview').src = src;
}

document.getElementById('modalImg').onclick = function(){
    this.style.display='none';
}

// 🔥 POPUP HAPUS
let deleteId = null;

function openPopup(id){
    deleteId = id;
    document.getElementById('popupHapus').style.display = 'flex';
}

function closePopup(){
    document.getElementById('popupHapus').style.display = 'none';
}

function confirmHapus(){
    window.location = "?hapus=" + deleteId;
}
</script>