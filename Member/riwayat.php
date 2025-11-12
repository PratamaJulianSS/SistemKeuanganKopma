<?php
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
  header("Location: ../login.php");
  exit;
}

$uid = $_SESSION['user_id'];
$name = $_SESSION['name'];

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));

// Ambil daftar riwayat aktivitas
$list = mysqli_query($conn, "SELECT * FROM riwayat WHERE user_id=$uid ORDER BY id DESC");
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Riwayat Aktivitas - Koperasi Satria Manunggal</title>
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

    .header h5 {
      margin: 0;
      font-weight: 600;
    }

    .header .user-info {
      display: flex;
      align-items: center;
    }

    .header .user-info img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 8px;
      border: 2px solid #fff;
    }

    .header a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: 0.3s;
    }

    .header a:hover {
      text-decoration: underline;
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

    .sidebar .profile-sidebar {
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar .profile-sidebar img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #fff;
      margin-bottom: 8px;
    }

    .sidebar .profile-sidebar .name {
      font-weight: 600;
    }

    .sidebar .profile-sidebar .role {
      font-size: 13px;
      opacity: 0.8;
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
    }
  </style>
</head>

<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4>KOPMA</h4>
    <div class="profile-sidebar">
      <img src="<?= $user['foto'] ? '../uploads/' . $user['foto'] : '../assets/default.png' ?>" alt="Foto Profil">
      <div class="name"><?= htmlspecialchars($user['name']) ?></div>
      <div class="role">Member</div>
    </div>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="profil.php">👤 Profil</a>
    <a href="simpanan.php">💰 Simpanan</a>
    <a href="transaksi.php">💳 Transaksi</a>
    <a href="riwayat.php" class="active">📄 Riwayat</a>
  </div>

  <!-- Header -->
  <div class="header">
    <h5>KOPERASI SATRIA MANUNGGAL</h5>
    <div class="user-info">
      <img src="<?= $user['foto'] ? '../uploads/' . $user['foto'] : '../assets/default.png' ?>" alt="Foto Profil">
      <span><?= htmlspecialchars($name) ?> (Member)</span>
      <a href="../logout.php" class="ms-3">Logout</a>
    </div>
  </div>

  <!-- Main -->
  <div class="main-content">
    <h3>Riwayat Aktivitas</h3>
    <p class="text-muted mb-4">Berikut adalah daftar seluruh aktivitas Anda di sistem koperasi.</p>

    <div class="card p-3">
      <table class="table table-bordered bg-white mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Aksi</th>
            <th>Detail</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1;
          while ($r = mysqli_fetch_assoc($list)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($r['aksi']) ?></td>
              <td><?= htmlspecialchars($r['detail']) ?></td>
              <td><?= htmlspecialchars($r['created_at']) ?></td>
            </tr>
          <?php endwhile; ?>
          <?php if (mysqli_num_rows($list) == 0): ?>
            <tr>
              <td colspan="4" class="text-center text-muted">Belum ada aktivitas yang tercatat.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>
