<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<style>
body {font-family:Poppins;background:#0f172a;color:white;text-align:center}
form{margin-top:100px}
input{padding:10px;margin:10px;width:200px}
button{padding:10px;background:#22c55e;border:none}
</style>
</head>
<body>

<h2>Register</h2>

<form action="proses_register.php" method="POST">
<input type="text" name="username" placeholder="Username" required><br>
<input type="password" name="password" placeholder="Password" required><br>
<button>Daftar</button>
</form>

</body>
</html>