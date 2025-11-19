<?php
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
  header("Location: ../login.php");
  exit;
}
$uid = $_SESSION['user_id'];

// Ambil data user lengkap
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));
$name = $user['name'];

// Ringkasan data anggota
$total_simpanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(jumlah),0) AS total FROM simpanan WHERE user_id=$uid"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE user_id=$uid"))['total'];
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
      margin: 0;
      display: flex;
      min-height: 100vh;
    }

    .header {
      position: fixed;
      top: 0;
      left: 230px;
      right: 0;
      height: 60px;
      background: linear-gradient(90deg, #0d6efd, #00b4d8);
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      z-index: 10;
    }

    .header img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 8px;
      border: 2px solid #fff;
    }

    .sidebar {
      width: 230px;
      background: linear-gradient(180deg, rgb(105, 13, 253), #00b4d8);
      color: white;
      padding-top: 80px;
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

    .sidebar a:hover,
    .sidebar a.active {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 8px;
    }

    .main-content {
      margin-left: 230px;
      margin-top: 60px;
      padding: 30px;
      flex: 1;
    }

    .card {
      border-radius: 15px;
      border: none;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: 0.3s;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .card h5 {
      color: #0d6efd;
      font-weight: 600;
    }

    .card p {
      font-size: 13px;
      color: #6c757d;
      margin: 0;
    }

    .profile-img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #fff;
    }

    .summary-card {
      background: white;
      border-left: 5px solid #0d6efd;
    }
  </style>
</head>

<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4>KOPMA</h4>
    <div class="text-center mb-4">
      <img src="<?= $user['foto'] ? '../uploads/' . $user['foto'] : '../assets/default.png' ?>" class="profile-img mb-2" alt="Foto Anggota">
      <div style="font-weight:600;"><?= htmlspecialchars($user['name']) ?></div>
      <div style="font-size:13px;opacity:0.8;">Member</div>
    </div>
    <a href="dashboard.php" class="active">🏠 Dashboard</a>
    <a href="profil.php">👤 Profil</a>
    <a href="simpanan.php">💰 Simpanan</a>
    <a href="transaksi.php">💳 Transaksi</a>
    <a href="riwayat.php">📄 Riwayat</a>
  </div>

  <!-- Header -->
  <div class="header">
    <div class="d-flex align-items-center">
      <img src="../assets/logo.png" style="width:35px;height:35px;margin-right:10px;">
      <h5 class="m-0">KOPERASI SATRIA MANUNGGAL</h5>
    </div>
    <div class="d-flex align-items-center">
      <img src="<?= $user['foto'] ? '../uploads/' . $user['foto'] : '../assets/default.png' ?>" alt="Foto Anggota">
      <span class="ms-2"><?= htmlspecialchars($name) ?> (Member)</span>
      <a href="../logout.php" class="text-white ms-3">Logout</a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h3>Dashboard Anggota</h3>
    <p class="text-muted mb-4">Selamat datang kembali, <strong><?= htmlspecialchars($name) ?></strong> 👋</p>

    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="card summary-card p-4 text-center">
          <h5>Total Simpanan</h5>
          <h3 class="mt-2 text-muted">Rp <?= number_format($total_simpanan, 2, ',', '.') ?></h3>
          <p>Jumlah seluruh simpanan Anda</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card summary-card p-4 text-center">
          <h5>Total Transaksi</h5>
          <h3 class="mt-2 text-muted"><?= $total_transaksi ?> kali</h3>
          <p>Jumlah transaksi yang Anda lakukan</p>
        </div>
      </div>
    </div>

    <div class="card p-4">
      <h5 class="mb-3">Informasi Umum</h5>
      <p>Melalui dashboard ini Anda dapat melihat dan mengelola data koperasi Anda dengan mudah.
        Gunakan menu di samping untuk mengakses data simpanan, transaksi, riwayat aktivitas, atau memperbarui profil pribadi Anda.</p>
    </div>
  </div>

</body>

</html>
