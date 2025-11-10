<?php
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../login.php");
    exit;
}
$uid = $_SESSION['user_id'];

// tambah transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $jenis = $_POST['jenis'];
    $jumlah = $_POST['jumlah'];
    $metode = $_POST['metode'];
    $note = $_POST['note'];
    $stmt = mysqli_prepare($conn, "INSERT INTO transaksi (user_id, tanggal, jenis, jumlah, metode, note) VALUES (?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "issdss", $uid, $tanggal, $jenis, $jumlah, $metode, $note);
    mysqli_stmt_execute($stmt);
    header("Location: transaksi.php");
    exit;
}
$list = mysqli_query($conn, "SELECT * FROM transaksi WHERE user_id=$uid ORDER BY id DESC");
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Transaksi - Koperasi Satria Manunggal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family:'Poppins',sans-serif; background:#f5f8ff; display:flex; margin:0; }
.sidebar { width:230px; background:linear-gradient(180deg,#0d6efd,#00b4d8); color:white; padding-top:30px; position:fixed; height:100%; }
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
  <a href="transaksi.php" class="active">💳 Transaksi</a>
  <a href="riwayat.php">📄 Riwayat</a>
  <div class="logout">
    <a href="../logout.php" class="btn btn-light btn-sm w-75">Logout</a>
  </div>
</div>

<div class="main-content">
  <h3>Transaksi Anda</h3>
  <form method="post" class="card p-3 mb-4 shadow-sm">
    <div class="row g-2">
      <div class="col-md-2"><input type="date" name="tanggal" class="form-control" required></div>
      <div class="col-md-2"><input type="text" name="jenis" class="form-control" placeholder="Jenis" required></div>
      <div class="col-md-2"><input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required></div>
      <div class="col-md-2"><input type="text" name="metode" class="form-control" placeholder="Metode"></div>
      <div class="col-md-3"><input type="text" name="note" class="form-control" placeholder="Catatan"></div>
      <div class="col-md-1"><button class="btn btn-primary w-100">OK</button></div>
    </div>
  </form>

  <table class="table table-bordered bg-white">
    <thead><tr><th>No</th><th>Tanggal</th><th>Jenis</th><th>Jumlah</th><th>Metode</th><th>Catatan</th></tr></thead>
    <tbody>
    <?php $no=1; while($r=mysqli_fetch_assoc($list)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= e($r['tanggal']) ?></td>
        <td><?= e($r['jenis']) ?></td>
        <td>Rp <?= number_format($r['jumlah'],2,',','.') ?></td>
        <td><?= e($r['metode']) ?></td>
        <td><?= e($r['note']) ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>