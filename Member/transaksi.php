<?php
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
  header("Location: ../login.php");
  exit;
}

$uid = $_SESSION['user_id'];
$name = $_SESSION['name'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));

// === Tambah Transaksi ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
  $jenis   = trim($_POST['jenis'] ?? '');
  $jumlah  = (float)($_POST['jumlah'] ?? 0);
  $metode  = trim($_POST['metode'] ?? '');
  $note    = trim($_POST['note'] ?? '');
  $status  = 'pending';

  $stmt = mysqli_prepare($conn, "INSERT INTO transaksi (user_id, tanggal, jenis, jumlah, metode, note, status) VALUES (?,?,?,?,?,?,?)");
  mysqli_stmt_bind_param($stmt, "issdsss", $uid, $tanggal, $jenis, $jumlah, $metode, $note, $status);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  // Catat riwayat
  $aksi = "Tambah Transaksi";
  $detail = "Menambahkan transaksi jenis $jenis sebesar Rp " . number_format($jumlah, 0, ',', '.');
  $r = mysqli_prepare($conn, "INSERT INTO riwayat (user_id, aksi, detail, created_at) VALUES (?,?,?,NOW())");
  mysqli_stmt_bind_param($r, "iss", $uid, $aksi, $detail);
  mysqli_stmt_execute($r);
  mysqli_stmt_close($r);

  header("Location: transaksi.php");
  exit;
}

// === Hapus Transaksi (hanya pending) ===
if (isset($_GET['hapus'])) {
  $id = (int)$_GET['hapus'];
  $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transaksi WHERE id=$id AND user_id=$uid"));
  if ($cek && $cek['status'] == 'pending') {
    mysqli_query($conn, "DELETE FROM transaksi WHERE id=$id AND user_id=$uid");
    $aksi = "Hapus Transaksi";
    $detail = "Menghapus transaksi ID $id (status pending)";
    mysqli_query($conn, "INSERT INTO riwayat (user_id, aksi, detail, created_at) VALUES ($uid,'$aksi','$detail',NOW())");
  }
  header("Location: transaksi.php");
  exit;
}

// Ambil Data Transaksi
$list = mysqli_query($conn, "SELECT * FROM transaksi WHERE user_id=$uid ORDER BY id DESC");
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Transaksi Anggota - Koperasi Satria Manunggal</title>
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
      margin-left: 10px;
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

    .badge-wait {
      background: #ffd966;
      color: #333;
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
    <a href="transaksi.php" class="active">💳 Transaksi</a>
    <a href="riwayat.php">📄 Riwayat</a>
  </div>

  <!-- Header -->
  <div class="header">
    <h5>KOPERASI SATRIA MANUNGGAL</h5>
    <div class="user-info">
      <img src="<?= $user['foto'] ? '../uploads/' . $user['foto'] : '../assets/default.png' ?>" alt="Foto Profil">
      <span><?= htmlspecialchars($name) ?></span>
      <a href="../logout.php">Logout</a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h3>Transaksi Anggota</h3>
    <p class="text-muted mb-4">Tambah transaksi baru atau lihat riwayat transaksi Anda.</p>

    <!-- Form tambah -->
    <div class="card p-4 mb-4">
      <h5 class="mb-3">Tambah Transaksi</h5>
      <form method="post" class="row g-2">
        <div class="col-md-3"><input type="date" name="tanggal" class="form-control" required></div>
        <div class="col-md-3"><input type="text" name="jenis" class="form-control" placeholder="Jenis Transaksi" required></div>
        <div class="col-md-2"><input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required></div>
        <div class="col-md-2">
          <select name="metode" class="form-select">
            <option value="">Metode Pembayaran</option>
            <option value="Tunai">Tunai</option>
            <option value="Transfer Bank">Transfer Bank</option>
            <option value="QRIS">QRIS</option>
            <option value="E-Wallet">E-Wallet</option>
          </select>
        </div>
        <div class="col-md-2"><input type="text" name="note" class="form-control" placeholder="Catatan"></div>
        <div class="col-md-12 text-end mt-3"><button class="btn btn-primary">💾 Kirim (Menunggu)</button></div>
      </form>
    </div>

    <!-- Daftar transaksi -->
    <div class="card p-3">
      <h5 class="mb-3">Daftar Transaksi</h5>
      <table class="table table-bordered bg-white mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jenis</th>
            <th>Jumlah</th>
            <th>Metode</th>
            <th>Catatan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1;
          while ($r = mysqli_fetch_assoc($list)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($r['tanggal']) ?></td>
              <td><?= htmlspecialchars($r['jenis']) ?></td>
              <td>Rp <?= number_format($r['jumlah'], 2, ',', '.') ?></td>
              <td><?= htmlspecialchars($r['metode']) ?></td>
              <td><?= htmlspecialchars($r['note']) ?></td>
              <td>
                <?php if ($r['status'] == 'approved'): ?>
                  <span class="badge bg-success">Disetujui</span>
                <?php elseif ($r['status'] == 'rejected'): ?>
                  <span class="badge bg-danger">Ditolak</span>
                <?php else: ?>
                  <span class="badge badge-wait">Menunggu</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <?php if ($r['status'] == 'pending'): ?>
                  <a href="transaksi.php?hapus=<?= (int)$r['id'] ?>" onclick="return confirm('Hapus transaksi ini?')" class="btn btn-sm btn-outline-danger">Hapus</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>
