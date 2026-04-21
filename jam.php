<?php
session_start();
require_once("../config/koneksi.php");

// 🔥 TAMBAH JAM
if(isset($_POST['tambah'])){
    $lapangan_id = $_POST['lapangan_id'];
    $jam = trim($_POST['jam']);

    // VALIDASI FORMAT JAM (basic)
    if(!preg_match("/^\d{2}:\d{2}-\d{2}:\d{2}$/", $jam)){
        echo "<script>alert('Format jam salah! contoh: 20:00-21:00');</script>";
    } else {

        // CEK DUPLIKAT
        $cek = mysqli_query($conn,"
            SELECT * FROM jam 
            WHERE lapangan_id='$lapangan_id' AND jam='$jam'
        ");

        if(mysqli_num_rows($cek) > 0){
            echo "<script>alert('Jam sudah ada!');</script>";
        } else {
            mysqli_query($conn,"
                INSERT INTO jam(lapangan_id, jam)
                VALUES('$lapangan_id','$jam')
            ");
            header("Location: jam.php");
            exit;
        }
    }
}

// 🔥 HAPUS JAM
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn,"DELETE FROM jam WHERE id='$id'");
    header("Location: jam.php");
    exit;
}

// 🔥 AMBIL DATA
$lapangan = mysqli_query($conn,"SELECT * FROM lapangan");

$jam = mysqli_query($conn,"
SELECT j.*, l.nama 
FROM jam j
JOIN lapangan l ON j.lapangan_id = l.id
ORDER BY l.nama ASC, j.jam ASC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Kelola Jam</title>

<style>
body{
    margin:0;
    font-family:Poppins;
    background:#020617;
    color:white;
    display:flex;
}

/* SIDEBAR */
.sidebar{
    width:220px;
    background:#020617;
    padding:20px;
}

.sidebar h2{
    margin-bottom:20px;
}

.sidebar a{
    display:block;
    padding:12px;
    margin-bottom:10px;
    border-radius:10px;
    text-decoration:none;
    color:white;
    background:#0f172a;
    transition:0.2s;
}

.sidebar a:hover{
    background:#1e293b;
}

.sidebar a.active{
    background:#22c55e;
    color:black;
}

/* MAIN */
.main{
    flex:1;
    padding:30px;
}

/* FORM */
.form{
    background:#0f172a;
    padding:20px;
    border-radius:15px;
    margin-bottom:30px;
    display:flex;
    gap:10px;
    align-items:center;
}

select,input{
    padding:10px;
    border-radius:8px;
    border:none;
    outline:none;
}

button{
    padding:10px 15px;
    border:none;
    border-radius:8px;
    background:#22c55e;
    cursor:pointer;
    font-weight:600;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:#0f172a;
    border-radius:15px;
    overflow:hidden;
}

th,td{
    padding:12px;
}

th{
    background:#020617;
    text-align:left;
}

tr{
    border-bottom:1px solid rgba(255,255,255,0.05);
}

tr:hover{
    background:#1e293b;
}

.delete{
    background:red;
    padding:6px 10px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    font-size:12px;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>⚽ FUTSAL</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="booking.php">Booking</a>
    <a href="jam.php" class="active">Kelola Jam</a>
</div>

<!-- MAIN -->
<div class="main">

<h2>⏱ Tambah Jam Lapangan</h2>

<!-- FORM -->
<form method="POST" class="form">

<select name="lapangan_id" required>
<option value="">Pilih Lapangan</option>
<?php while($l = mysqli_fetch_assoc($lapangan)){ ?>
<option value="<?= $l['id'] ?>"><?= $l['nama'] ?></option>
<?php } ?>
</select>

<input type="text" name="jam" placeholder="contoh: 20:00-21:00" required>

<button name="tambah">+ Tambah</button>

</form>

<!-- TABLE -->
<h3>📋 Daftar Jam</h3>

<table>
<tr>
<th>Lapangan</th>
<th>Jam</th>
<th>Aksi</th>
</tr>

<?php while($j = mysqli_fetch_assoc($jam)){ ?>
<tr>
<td><?= $j['nama'] ?></td>
<td><?= $j['jam'] ?></td>
<td>
<a class="delete" onclick="return confirm('Hapus jam ini?')" href="?hapus=<?= $j['id'] ?>">Hapus</a>
</td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>