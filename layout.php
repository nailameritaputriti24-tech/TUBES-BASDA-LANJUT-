<div class="navbar">
    <div class="logo">⚽ FUTSAL</div>

    <div class="menu">
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="booking.php">📅 Booking</a>
        <a href="riwayat.php">📊 Riwayat</a>
        <a href="../auth/logout.php">🚪 Logout</a>
    </div>
</div>

<style>
/* NAVBAR */
.navbar{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:70px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 40px;
    background:rgba(2,6,23,0.8);
    backdrop-filter:blur(15px);
    border-bottom:1px solid rgba(255,255,255,0.08);
    z-index:999;
}

/* LOGO */
.logo{
    font-weight:700;
    font-size:20px;
}

/* MENU */
.menu a{
    margin:0 15px;
    color:white;
    text-decoration:none;
    font-weight:500;
    transition:0.3s;
}

.menu a:hover{
    color:#22c55e;
}

/* 🔥 BIAR KONTEN GA KETIMPA NAVBAR */
body{
    padding-top:80px;
}
</style>