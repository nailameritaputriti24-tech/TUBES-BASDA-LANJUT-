<?php
// tampilkan error kalau ada (biar gampang debug, boleh dihapus nanti)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// koneksi (PATH YANG BENAR DARI admin/proses/)
require_once(__DIR__ . "/../../config/koneksi.php");

// validasi id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../kelola_lapangan.php");
    exit;
}

$id = (int) $_GET['id'];

// eksekusi delete
mysqli_query($conn, "DELETE FROM lapangan WHERE id = $id");

// redirect balik
header("Location: ../kelola_lapangan.php");
exit;