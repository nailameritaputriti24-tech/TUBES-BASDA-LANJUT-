<?php
session_start();
require_once(__DIR__ . "/../config/koneksi.php");

// ================= TAMBAH LAPANGAN =================
if(isset($_POST['tambah'])){
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];

    if($nama != "" && $harga != ""){
        mysqli_query($conn,"
        INSERT INTO lapangan(nama,harga)
        VALUES('$nama','$harga')
        ");
    }

    header("Location: kelola_lapangan.php");
    exit;
}
?>

<?php include("layout.php"); ?>

<style>
.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.user{
    background:#1e293b;
    padding:8px 15px;
    border-radius:10px;
}

.stats{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:15px;
    margin-bottom:20px;
}

.stat{
    background:#1e293b;
    padding:15px;
    border-radius:15px;
}

.form{
    display:flex;
    gap:10px;
    background:#1e293b;
    padding:15px;
    border-radius:15px;
    margin-bottom:20px;
}

input{
    flex:1;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#0f172a;
    color:white;
}

button{
    padding:12px 20px;
    border:none;
    border-radius:10px;
    cursor:pointer;
}

.btn-add{
    background:#22c55e;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
    gap:20px;
}

.card{
    background:#1e293b;
    border-radius:15px;
    overflow:hidden;
}

.card img{
    width:100%;
    height:150px;
    object-fit:cover;
}

.content{
    padding:15px;
}

.title{
    font-weight:600;
}

.price{
    color:#22c55e;
    margin-top:5px;
}

.btn-delete{
    margin-top:10px;
    width:100%;
    background:#ef4444;
    color:white;
}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.6);
    justify-content:center;
    align-items:center;
}

.modal-box{
    background:#1e293b;
    padding:20px;
    border-radius:15px;
    text-align:center;
}
</style>

<div class="main-content">

<div class="top">
    <h2>⚽ Kelola Lapangan</h2>
    <div class="user">Online</div>
</div>

<div class="stats">
    <div class="stat">Total Lapangan <br><b>
        <?= mysqli_num_rows(mysqli_query($conn,"SELECT * FROM lapangan")) ?>
    </b></div>
    <div class="stat">Total Booking <br><b>
        <?= mysqli_num_rows(mysqli_query($conn,"SELECT * FROM booking")) ?>
    </b></div>
    <div class="stat">Users <br><b>
        <?= mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users")) ?>
    </b></div>
</div>

<form method="POST" class="form">
    <input type="text" name="nama" placeholder="Nama Lapangan" required>
    <input type="number" name="harga" placeholder="Harga" required>
    <button class="btn-add" name="tambah">Tambah</button>
</form>

<div class="grid">

<?php
$q = mysqli_query($conn,"SELECT * FROM lapangan ORDER BY id DESC");
while($d = mysqli_fetch_assoc($q)){
?>

<div class="card">
<img src="https://images.unsplash.com/photo-1556056504-5c7696c4c28d">

<div class="content">
<div class="title"><?= $d['nama'] ?></div>
<div class="price">Rp <?= number_format($d['harga']) ?></div>

<button class="btn-delete" onclick="hapus(<?= $d['id'] ?>)">Hapus</button>
</div>
</div>

<?php } ?>

</div>

</div>

<!-- MODAL -->
<div class="modal" id="modal">
    <div class="modal-box">
        <p>Yakin hapus data?</p>
        <br>
        <button onclick="lanjut()">Ya</button>
        <button onclick="tutup()">Batal</button>
    </div>
</div>

<script>
let idHapus = null;

function hapus(id){
    idHapus = id;
    document.getElementById('modal').style.display='flex';
}

function tutup(){
    document.getElementById('modal').style.display='none';
}

function lanjut(){
    window.location = "proses/hapus_lapangan.php?id="+idHapus;
}
</script>

</div>
</body>
</html>