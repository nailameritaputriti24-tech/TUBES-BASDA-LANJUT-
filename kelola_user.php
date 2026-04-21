<?php 
session_start();
require_once("../config/koneksi.php");

/* ================= DATA ================= */
$q = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");

/* TAMBAH USER */
if(isset($_POST['tambah'])){
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    mysqli_query($conn,"INSERT INTO users(username,password,nama,role)
    VALUES('$username','$password','$nama','$role')");

    header("Location: kelola_user.php?msg=add");
    exit;
}

/* TOTAL */
$total_user = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users"));
$total_admin = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users WHERE role='admin'"));
?>

<?php include("layout.php"); ?>

<style>
.page-title{font-size:26px;font-weight:700;margin-bottom:25px;}

.stats{display:flex;gap:15px;margin-bottom:25px;}
.stat{flex:1;background:linear-gradient(135deg,#1e293b,#334155);padding:15px;border-radius:15px;}

.grid{display:grid;grid-template-columns:2fr 1fr;gap:20px;}

.card{background:#1e293b;border-radius:20px;padding:20px;box-shadow:0 10px 25px rgba(0,0,0,0.3);}

.table{width:100%;border-collapse:collapse;}
.table th{padding:10px;color:#94a3b8;text-align:left;}
.table td{padding:12px;border-top:1px solid #334155;}

.badge{padding:6px 12px;border-radius:20px;font-size:12px;}
.admin{background:#22c55e;color:black;}
.user{background:#64748b;color:white;}

.btn{padding:6px 10px;border:none;border-radius:8px;cursor:pointer;}
.btn-delete{background:#ef4444;color:white;}
.btn-edit{background:#3b82f6;color:white;}

.form input,.form select{
width:100%;padding:12px;margin-bottom:10px;border:none;border-radius:10px;background:#0f172a;color:white;
}

.btn-add{width:100%;padding:12px;background:#22c55e;border:none;border-radius:10px;}

#search{width:100%;padding:10px;margin-bottom:15px;border-radius:10px;background:#0f172a;color:white;border:none;}

/* MODAL */
.modal{
display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);
align-items:center;justify-content:center;
}

.modal-box{
background:#1e293b;padding:20px;border-radius:15px;width:300px;
}

/* TOAST */
.toast{
position:fixed;top:20px;right:20px;
background:#22c55e;padding:10px 20px;border-radius:10px;
display:none;
}
</style>

<h2 class="page-title">👤 Kelola User</h2>

<div class="stats">
    <div class="stat">Total User<br><b><?= $total_user ?></b></div>
    <div class="stat">Admin<br><b><?= $total_admin ?></b></div>
</div>

<div class="grid">

<!-- TABLE -->
<div class="card">
    <h3>Daftar User</h3>

    <input type="text" id="search" placeholder="Cari user...">

    <table class="table">
        <tr>
            <th>Nama</th>
            <th>Username</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>

        <?php while($d = mysqli_fetch_assoc($q)){ ?>
        <tr class="row-user">
            <td><?= $d['nama'] ?? '-' ?></td>
            <td><?= $d['username'] ?></td>
            <td>
                <span class="badge <?= $d['role']=='admin'?'admin':'user' ?>">
                    <?= $d['role'] ?>
                </span>
            </td>
            <td>
                <button class="btn btn-edit"
                onclick="editUser(<?= $d['id'] ?>,'<?= $d['nama'] ?>','<?= $d['username'] ?>','<?= $d['role'] ?>')">
                Edit</button>

                <button class="btn btn-delete"
                onclick="hapusUser(<?= $d['id'] ?>)">
                Hapus</button>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<!-- FORM -->
<div class="card">
    <h3>Tambah User</h3>

    <form method="POST" class="form">
        <input type="text" name="nama" placeholder="Nama" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button name="tambah" class="btn-add">+ Tambah User</button>
    </form>
</div>

</div>

<!-- MODAL HAPUS -->
<div class="modal" id="modalHapus">
    <div class="modal-box">
        <h3>Yakin hapus user?</h3><br>
        <button onclick="lanjutHapus()" class="btn btn-delete">Ya</button>
        <button onclick="tutupModal()">Batal</button>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal" id="modalEdit">
    <div class="modal-box">
        <h3>Edit User</h3>

        <form method="POST" action="proses/edit_user.php">
            <input type="hidden" name="id" id="edit_id">

            <input type="text" name="nama" id="edit_nama">
            <input type="text" name="username" id="edit_username">

            <select name="role" id="edit_role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <br>
            <button>Simpan</button>
            <button type="button" onclick="tutupModal()">Batal</button>
        </form>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">Berhasil!</div>

<script>
// SEARCH
document.getElementById('search').addEventListener('keyup',function(){
    let val=this.value.toLowerCase();
    document.querySelectorAll('.row-user').forEach(row=>{
        row.style.display=row.innerText.toLowerCase().includes(val)?'':'none';
    });
});

// HAPUS
let idHapus=null;
function hapusUser(id){
    idHapus=id;
    document.getElementById('modalHapus').style.display='flex';
}
function lanjutHapus(){
    window.location='proses/hapus_user.php?id='+idHapus;
}
function tutupModal(){
    document.getElementById('modalHapus').style.display='none';
    document.getElementById('modalEdit').style.display='none';
}

// EDIT
function editUser(id,nama,username,role){
    document.getElementById('modalEdit').style.display='flex';
    document.getElementById('edit_id').value=id;
    document.getElementById('edit_nama').value=nama;
    document.getElementById('edit_username').value=username;
    document.getElementById('edit_role').value=role;
}

// TOAST
<?php if(isset($_GET['msg'])){ ?>
document.getElementById('toast').style.display='block';
setTimeout(()=>{document.getElementById('toast').style.display='none';},2000);
<?php } ?>
</script>