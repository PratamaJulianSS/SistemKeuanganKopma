<?php
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../login.php");
    exit;
}
$uid = $_SESSION['user_id'];
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(jumlah),0) AS total FROM simpanan WHERE user_id=$uid"))['total'];
$trans = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE user_id=$uid"))['total'];
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Dashboard Anggota - Koperasi Satria Manunggal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f5f8ff;
  display: flex;
  min-height: 100vh;
  margin: 0;
}
.sidebar {
  width: 230px;
  background: linear-gradient(180deg,rgb(20, 0, 255), #00b4d8);
  color: white;
  padding-top: 30px;
  position: fixed;
  height: 100%;
}
.sidebar h4 {
  text-align: center;
  margin-bottom: 30px;
  font-weight: 700;
}
.sidebar a {
  display: block;
  padding: 12px 20px;
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: 0.2s;
}
.sidebar a:hover, .sidebar a.active {
  background: rgba(255,255,255,0.2);
  border-radius: 8px;
}
.logout {
  position: absolute;
  bottom: 20px;
  width: 100%;
  text-align: center;
}
.main-content {
  margin-left: 230px;
  padding: 30px;
  flex: 1;
}
.card {
  border-radius: 15px;
  border: none;
  transition: 0.3s;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
.card h5 {
  color: #0d6efd;
}
</style>
</head>
<body>

<div class="sidebar">
  <h4>KOPMA</h4>
  <a href="dashboard.php" class="active">🏠 Dashboard</a>
  <a href="simpanan.php">💰 Simpanan</a>
  <a href="transaksi.php">💳 Transaksi</a>
  <a href="riwayat.php">📄 Riwayat</a>
  <div class="logout">
    <a href="../logout.php" class="btn btn-light btn-sm w-75">Logout</a>
  </div>
</div>

<div class="main-content">
  <h3>Dashboard Anggota</h3>
  <p class="text-muted mb-4">Selamat datang, <strong><?= e($_SESSION['name']) ?></strong></p>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="card p-3 text-center shadow-sm">
        <h5>Total Simpanan</h5>
        <p class="display-6 text-muted">Rp <?= number_format($total,2,',','.') ?></p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3 text-center shadow-sm">
        <h5>Jumlah Transaksi</h5>
        <p class="display-6 text-muted"><?= $trans ?> kali</p>
      </div>
    </div>
  </div>
</div>

</body>
</html>