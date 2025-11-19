<?php
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$uid = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));
$name = $user['name'];

// Hapus anggota
if (isset($_GET['hapus'])) {
  $id = (int)$_GET['hapus'];
  mysqli_query($conn, "DELETE FROM users WHERE id=$id AND role='member'");
  header("Location: anggota.php");
  exit;
}

// Ambil data anggota
$anggota = mysqli_query($conn, "SELECT id, name, email, created_at FROM users WHERE role='member' ORDER BY name ASC");
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Data Anggota - Koperasi Satria Manunggal</title>
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
    }

    .profile-img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #fff;
    }
  </style>
</head>

<body>

  <div class="sidebar">
    <h4>KOPMA</h4>
    <div class="text-center mb-4">
      <img src="<?= $user['foto'] ? '../uploads/' . $user['foto'] : '../assets/default.png' ?>" class="profile-img mb-2" alt="Foto Admin">
      <div style="font-weight:600;"><?= htmlspecialchars($user['name']) ?></div>
      <div style="font-size:13px;opacity:0.8;">Admin</div>
    </div>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="simpanan.php">💰 Simpanan</a>
    <a href="transaksi.php">💳 Transaksi</a>
    <a href="anggota.php" class="active">🧑‍💼 Anggota</a>
    <a href="riwayat.php">📄 Riwayat</a>
    <a href="profil.php">👤 Profil</a>
  </div>

  <div class="header">
    <div class="d-flex align-items-center">
      <img src="../assets/logo.png" style="width:35px;height:35px;margin-right:10px;">
      <h5 class="m-0">KOPERASI SATRIA MANUNGGAL</h5>
    </div>

    <div class="d-flex align-items-center">
      <img src="<?= $user['foto'] ? '../uploads/' . $user['foto'] : '../assets/default.png' ?>" alt="Foto Admin">
      <span><?= htmlspecialchars($name) ?> (Admin)</span>
      <a href="../logout.php" class="text-white ms-3">Logout</a>
    </div>
  </div>

  <div class="main-content">
    <h3>Data Anggota</h3>
    <p class="text-muted mb-4">Berisi daftar seluruh anggota koperasi yang terdaftar dalam sistem.</p>

    <div class="card p-3">
      <h5 class="mb-3">Daftar Anggota</h5>
      <table class="table table-bordered bg-white align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Tanggal Bergabung</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1;
          while ($r = mysqli_fetch_assoc($anggota)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($r['name']) ?></td>
              <td><?= htmlspecialchars($r['email']) ?></td>
              <td><?= htmlspecialchars($r['created_at']) ?></td>
              <td class="text-center">
                <a href="?hapus=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus anggota ini?')">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
          <?php if (mysqli_num_rows($anggota) == 0): ?>
            <tr>
              <td colspan="5" class="text-center text-muted">Belum ada anggota terdaftar.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>
