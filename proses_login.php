<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

require_once("../config/koneksi.php");

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $q = mysqli_query($conn,"
        SELECT * FROM users 
        WHERE username='$username'
    ");

    $data = mysqli_fetch_assoc($q);

    // 🔐 cocokkan password HASH
    if($data && password_verify($password, $data['password'])){

        $_SESSION['login'] = true;
        $_SESSION['id'] = $data['id_user'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['username'] = $data['username']; // ✅ FIX ERROR

        // flag login pertama
        $_SESSION['first_login'] = true;

        if($data['role'] == 'admin'){
            header("Location: ../admin/dashboard.php");
        }else{
            header("Location: ../user/dashboard.php"); // ✅ ke dashboard dulu
        }
        exit;

    } else {
        echo "<script>alert('Username / Password salah!')</script>";
    }
}
?>