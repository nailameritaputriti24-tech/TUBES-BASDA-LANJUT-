<?php
session_start();
require_once("../config/koneksi.php");

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn,"SELECT * FROM users WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);

    if($data){
        if(password_verify($password, $data['password'])){

            $_SESSION['login'] = true;
            $_SESSION['id'] = $data['id'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];

            if($data['role']=='admin'){
                header("Location: ../admin/dashboard.php");
            }else{
                header("Location: ../user/dashboard.php");
            }
            exit;

        }else{
            $error = "Password salah";
        }
    }else{
        $error = "Username tidak ditemukan";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Futsal Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

/* ===== BACKGROUND ===== */
body{
    height:100vh;
    overflow:hidden;
    background:#020617;
    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
}

/* ===== PARTICLE CANVAS ===== */
#particles{
    position:fixed;
    inset:0;
    z-index:-1;
}

/* ===== FUTSAL BALL ANIMATION ===== */
.ball{
    position:absolute;
    top:20%;
    left:-100px;
    font-size:40px;
    animation:moveBall 8s linear infinite;
}

@keyframes moveBall{
    0%{left:-100px; transform:rotate(0deg);}
    100%{left:110%; transform:rotate(360deg);}
}

/* ===== CARD ===== */
.card{
    width:350px;
    padding:30px;
    border-radius:20px;
    background:rgba(15,23,42,0.7);
    backdrop-filter:blur(20px);
    border:1px solid rgba(34,197,94,0.2);
    box-shadow:0 0 30px rgba(34,197,94,0.2);
    text-align:center;
    transition:0.3s;
}

/* glow hover */
.card:hover{
    box-shadow:0 0 60px rgba(34,197,94,0.4);
}

/* ===== TITLE ===== */
.title{
    font-size:24px;
    font-weight:700;
    margin-bottom:20px;
}

.title span{
    color:#22c55e;
}

/* ===== INPUT ===== */
.input-group{
    position:relative;
}

.input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:none;
    border-radius:10px;
    background:#0f172a;
    color:white;
}

/* ===== SHOW PASSWORD ===== */
.toggle{
    position:absolute;
    right:10px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
}

/* ===== BUTTON ===== */
.btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,#22c55e,#4ade80);
    font-weight:600;
    cursor:pointer;
    margin-top:10px;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.05);
}

/* ===== REMEMBER ===== */
.remember{
    display:flex;
    align-items:center;
    gap:5px;
    font-size:12px;
    margin-top:5px;
}

/* ===== ERROR SHAKE ===== */
.error{
    animation:shake 0.3s;
}

@keyframes shake{
    0%{transform:translateX(0);}
    25%{transform:translateX(-5px);}
    50%{transform:translateX(5px);}
    75%{transform:translateX(-5px);}
    100%{transform:translateX(0);}
}

.error-text{
    color:#ef4444;
    margin-top:10px;
    font-size:13px;
}
</style>
</head>

<body>

<canvas id="particles"></canvas>
<div class="ball">⚽</div>

<div class="card <?php if(isset($error)) echo 'error'; ?>">

<div class="title">⚽ FUTSAL <span>LOGIN</span></div>

<form method="POST">

<input type="text" name="username" class="input" placeholder="Username" required>

<div class="input-group">
<input type="password" name="password" id="password" class="input" placeholder="Password" required>
<span class="toggle" onclick="togglePass()">👁️</span>
</div>

<div class="remember">
<input type="checkbox" name="remember"> Remember me
</div>

<button name="login" class="btn">Login</button>

</form>

<?php if(isset($error)){ ?>
<div class="error-text"><?= $error ?></div>
<?php } ?>

</div>

<script>
function togglePass(){
    let pass = document.getElementById('password');
    pass.type = pass.type === 'password' ? 'text' : 'password';
}

/* PARTICLES */
const canvas = document.getElementById("particles");
const ctx = canvas.getContext("2d");

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let particles = [];

for(let i=0;i<80;i++){
    particles.push({
        x:Math.random()*canvas.width,
        y:Math.random()*canvas.height,
        r:Math.random()*2,
        d:Math.random()*1
    });
}

function draw(){
    ctx.clearRect(0,0,canvas.width,canvas.height);

    ctx.fillStyle="rgba(34,197,94,0.5)";
    ctx.beginPath();

    particles.forEach(p=>{
        ctx.moveTo(p.x,p.y);
        ctx.arc(p.x,p.y,p.r,0,Math.PI*2,true);
    });

    ctx.fill();

    particles.forEach(p=>{
        p.y += p.d;
        if(p.y>canvas.height){
            p.y=0;
            p.x=Math.random()*canvas.width;
        }
    });
}

setInterval(draw,30);
</script>

</body>
</html>