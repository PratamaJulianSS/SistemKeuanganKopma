<?php
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../login.php");
    exit;
}
$uid = $_SESSION['user_id'];
$list = mysqli_query($conn, "SELECT * FROM riwayat WHERE user_id=$uid ORDER BY id DESC");
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Riwayat - Koperasi Satria Manunggal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;background:#f5f8ff;display:flex;margin:0;}
.sidebar{width:230px;background:linear-gradient(180deg,#0d6efd,#00b4d8);color:white;padding-top:30px;position:fixed;height:100%;}
.sidebar h4{text-align:center;margin-bottom:30px;font-weight:700;}
.sidebar a{display:block;padding:12px 20px;color:white;text-decoration:none;font-weight:500;transition:0.2s;}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.2);border-radius:8px;}
.logout{position:absolute;bottom:20px;width:100%;text-align:center;}
.main-content{margin-left:230px;padding:30px;flex:1;}
</style>
</head>
<body>

<div class="sidebar">
  <h4>KOPMA</h4>
  <a href="dashboard.php">🏠 Dashboard</a>
  <a href="simpanan.php">💰 Simpanan</a>
  <a href="transaksi.php">💳 Transaksi</a>
  <a href="riwayat.php" class="active">📄 Riwayat</a>
  <div class="logout">
    <a href="../logout.php" class="btn btn-light btn-sm w-75">Logout</a>
  </div>
</div>

<div class="main-content">
  <h3>Riwayat Transaksi</h3>
  <table class="table table-bordered bg-white">
    <thead><tr><th>No</th><th>Aksi</th><th>Detail</th><th>Tanggal</th></tr></thead>
    <tbody>
      <?php $no=1; while($r=mysqli_fetch_assoc($list)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= e($r['aksi']) ?></td>
        <td><?= e($r['detail']) ?></td>
        <td><?= e($r['created_at']) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>