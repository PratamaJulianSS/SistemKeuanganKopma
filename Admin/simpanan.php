<?php
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$uid = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));
$name = $user['name'];

// Tambah simpanan oleh admin (langsung disetujui)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
  $user_id = (int)$_POST['user_id'];
  $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
  $jumlah = (float)$_POST['jumlah'];
  $metode = $_POST['metode'] ?? '';
  $note   = $_POST['note'] ?? '';
  $status = 'approved';

  $stmt = mysqli_prepare($conn, "INSERT INTO simpanan (user_id,tanggal,jumlah,metode,note,status) VALUES (?,?,?,?,?,?)");
  mysqli_stmt_bind_param($stmt, "isdsss", $user_id, $tanggal, $jumlah, $metode, $note, $status);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  // catat riwayat
  $aksi = "Tambah Simpanan";
  $detail = "Menambahkan simpanan anggota ID $user_id sebesar Rp " . number_format($jumlah, 0, ',', '.');
  $log = mysqli_prepare($conn, "INSERT INTO riwayat (user_id, aksi, detail, created_at) VALUES (?,?,?,NOW())");
  mysqli_stmt_bind_param($log, "iss", $_SESSION['user_id'], $aksi, $detail);
  mysqli_stmt_execute($log);
  mysqli_stmt_close($log);

  header("Location: simpanan.php?sukses=1");
  exit;
}

// Aksi setujui, tolak, hapus
if (isset($_GET['aksi']) && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $aksi = $_GET['aksi'];

  if ($aksi === 'hapus') {
    mysqli_query($conn, "DELETE FROM simpanan WHERE id=$id");
  } elseif ($aksi === 'setujui') {
    mysqli_query($conn, "UPDATE simpanan SET status='approved' WHERE id=$id");
  } elseif ($aksi === 'tolak') {
    mysqli_query($conn, "UPDATE simpanan SET status='rejected' WHERE id=$id");
  }

  header("Location: simpanan.php");
  exit;
}

$anggota = mysqli_query($conn, "SELECT id,name FROM users WHERE role='member' ORDER BY name ASC");
$list = mysqli_query($conn, "SELECT s.*, u.name FROM simpanan s JOIN users u ON s.user_id=u.id ORDER BY s.id DESC");
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Data Simpanan (Admin) - Koperasi Satria Manunggal</title>
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

    .btn-action {
      padding: 4px 10px;
      font-size: 13px;
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
    <a href="simpanan.php" class="active">💰 Simpanan</a>
    <a href="transaksi.php">💳 Transaksi</a>
    <a href="anggota.php">🧑‍💼 Anggota</a>
    <a href="riwayat.php">📄 Riwayat</a>
    <a href="profil.php">👤 Profil</a>
  </div>

  <div class="header">
    <h5>KOPERASI SATRIA MANUNGGAL</h5>
    <div class="d-flex align-items-center">
      <img src="<?= $user['foto'] ? '../uploads/' . $user['foto'] : '../assets/default.png' ?>" alt="Foto Admin">
      <span><?= htmlspecialchars($name) ?> (Admin)</span>
      <a href="../logout.php" class="text-white ms-3">Logout</a>
    </div>
  </div>

  <div class="main-content">
    <h3>💰 Data Simpanan Anggota</h3>
    <p class="text-muted mb-4">Tambah, setujui, tolak, atau hapus data simpanan anggota.</p>

    <?php if (isset($_GET['sukses'])): ?>
      <div class="alert alert-success">✅ Simpanan berhasil ditambahkan.</div>
    <?php endif; ?>

    <!-- Tabel Data -->
    <div class="card p-3 mb-4">
      <h5 class="mb-3">Daftar Simpanan</h5>
      <div class="table-responsive">
        <table class="table table-bordered bg-white mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Tanggal</th>
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
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['tanggal']) ?></td>
                <td>Rp <?= number_format($r['jumlah'], 2, ',', '.') ?></td>
                <td><?= htmlspecialchars($r['metode']) ?></td>
                <td><?= htmlspecialchars($r['note']) ?></td>
                <td>
                  <?php if ($r['status'] == 'approved'): ?>
                    <span class="badge bg-success">Disetujui</span>
                  <?php elseif ($r['status'] == 'rejected'): ?>
                    <span class="badge bg-danger">Ditolak</span>
                  <?php else: ?>
                    <span class="badge bg-warning text-dark">Menunggu</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($r['status'] == 'pending'): ?>
                    <a href="?aksi=setujui&id=<?= $r['id'] ?>" class="btn btn-success btn-sm btn-action">Setujui</a>
                    <a href="?aksi=tolak&id=<?= $r['id'] ?>" class="btn btn-warning btn-sm btn-action text-white">Tolak</a>
                  <?php endif; ?>
                  <a href="?aksi=hapus&id=<?= $r['id'] ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Hapus data ini?')">Hapus</a>
                </td>
              </tr>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($list) == 0): ?>
              <tr>
                <td colspan="8" class="text-center text-muted">Belum ada data simpanan.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tambah Simpanan -->
    <div class="card p-4">
      <h5 class="mb-3">Tambah Simpanan Baru</h5>
      <form method="post" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Anggota</label>
          <select name="user_id" class="form-select" required>
            <option value="">Pilih Anggota</option>
            <?php mysqli_data_seek($anggota, 0);
            while ($a = mysqli_fetch_assoc($anggota)): ?>
              <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="col-md-2">
          <label class="form-label">Jumlah</label>
          <input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required>
        </div>
        <div class="col-md-2">
          <label class="form-label">Metode</label>
          <select name="metode" class="form-select">
            <option value="">Metode</option>
            <option value="Tunai">Tunai</option>
            <option value="Transfer Bank">Transfer Bank</option>
            <option value="QRIS">QRIS</option>
            <option value="E-Wallet">E-Wallet</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Catatan</label>
          <input type="text" name="note" class="form-control" placeholder="Catatan">
        </div>
        <div class="col-12 text-end mt-3">
          <button class="btn btn-primary px-4" name="tambah">💾 Tambah & Setujui</button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>