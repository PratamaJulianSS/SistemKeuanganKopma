<?php
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$uid = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));
$name = $user['name'];

// ==== FILTER ====
$where = [];
if (!empty($_GET['user'])) {
  $user_filter = (int)$_GET['user'];
  $where[] = "r.user_id = $user_filter";
}
if (!empty($_GET['aksi'])) {
  $aksi_filter = mysqli_real_escape_string($conn, $_GET['aksi']);
  $where[] = "r.aksi = '$aksi_filter'";
}
if (!empty($_GET['dari']) && !empty($_GET['sampai'])) {
  $dari = $_GET['dari'];
  $sampai = $_GET['sampai'];
  $where[] = "DATE(r.created_at) BETWEEN '$dari' AND '$sampai'";
}
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// ==== QUERY RIWAYAT ====
$list = mysqli_query($conn, "
    SELECT r.*, u.name 
    FROM riwayat r 
    JOIN users u ON r.user_id=u.id 
    $where_sql 
    ORDER BY r.id DESC
");

// ==== DATA UNTUK FILTER ====
$users = mysqli_query($conn, "SELECT id, name FROM users ORDER BY name ASC");

// ==== EKSPOR CSV ====
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment;filename="riwayat_koperasi.csv"');
  $output = fopen("php://output", "w");
  fputcsv($output, ['No', 'Nama', 'Aksi', 'Detail', 'Waktu']);
  $no = 1;
  while ($r = mysqli_fetch_assoc($list)) {
    fputcsv($output, [$no++, $r['name'], $r['aksi'], $r['detail'], $r['created_at']]);
  }
  fclose($output);
  exit;
}
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

    .badge-log {
      padding: 6px 10px;
      border-radius: 8px;
      font-size: 12px;
    }

    .badge-add {
      background: #198754;
      color: #fff;
    }

    .badge-update {
      background: #0dcaf0;
      color: #fff;
    }

    .badge-delete {
      background: #dc3545;
      color: #fff;
    }

    .badge-approve {
      background: #0d6efd;
      color: #fff;
    }
  </style>
</head>

<body>

  <!-- SIDEBAR -->
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
    <a href="anggota.php">🧑‍💼 Anggota</a>
    <a href="riwayat.php" class="active">📄 Riwayat</a>
    <a href="profil.php">👤 Profil</a>
  </div>

  <!-- HEADER -->
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

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <h3>Riwayat Aktivitas</h3>
    <p class="text-muted mb-4">Menampilkan log aktivitas sistem dengan opsi filter dan ekspor data.</p>

    <!-- Filter -->
    <div class="card p-4 mb-4">
      <h5 class="mb-3">🔍 Filter Riwayat</h5>
      <form method="get" class="row g-3">
        <div class="col-md-3">
          <select name="user" class="form-select">
            <option value="">Semua Pengguna</option>
            <?php while ($u = mysqli_fetch_assoc($users)): ?>
              <option value="<?= $u['id'] ?>" <?= isset($_GET['user']) && $_GET['user'] == $u['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['name']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-3">
          <select name="aksi" class="form-select">
            <option value="">Semua Aksi</option>
            <option value="Tambah Simpanan" <?= ($_GET['aksi'] ?? '') == 'Tambah Simpanan' ? 'selected' : '' ?>>Tambah Simpanan</option>
            <option value="Tambah Transaksi" <?= ($_GET['aksi'] ?? '') == 'Tambah Transaksi' ? 'selected' : '' ?>>Tambah Transaksi</option>
            <option value="Setujui" <?= ($_GET['aksi'] ?? '') == 'Setujui' ? 'selected' : '' ?>>Setujui</option>
            <option value="Tolak" <?= ($_GET['aksi'] ?? '') == 'Tolak' ? 'selected' : '' ?>>Tolak</option>
          </select>
        </div>
        <div class="col-md-2">
          <input type="date" name="dari" value="<?= $_GET['dari'] ?? '' ?>" class="form-control" placeholder="Dari">
        </div>
        <div class="col-md-2">
          <input type="date" name="sampai" value="<?= $_GET['sampai'] ?? '' ?>" class="form-control" placeholder="Sampai">
        </div>
        <div class="col-md-2 text-end">
          <button class="btn btn-primary px-4">Terapkan</button>
          <a href="riwayat.php" class="btn btn-secondary">Reset</a>
        </div>
      </form>
    </div>

    <!-- Data Riwayat -->
    <div class="card p-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>📋 Daftar Riwayat</h5>
        <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>" class="btn btn-success btn-sm">⬇️ Ekspor CSV</a>
      </div>
      <table class="table table-bordered bg-white align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>No</th>
            <th>Nama Pengguna</th>
            <th>Aksi</th>
            <th>Detail</th>
            <th>Waktu</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1;
          while ($r = mysqli_fetch_assoc($list)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($r['name']) ?></td>
              <td>
                <?php
                $cls = 'badge-log';
                if (stripos($r['aksi'], 'Tambah') !== false) $cls .= ' badge-add';
                elseif (stripos($r['aksi'], 'Setujui') !== false) $cls .= ' badge-approve';
                elseif (stripos($r['aksi'], 'Tolak') !== false) $cls .= ' badge-delete';
                else $cls .= ' badge-update';
                ?>
                <span class="<?= $cls ?>"><?= htmlspecialchars($r['aksi']) ?></span>
              </td>
              <td><?= htmlspecialchars($r['detail']) ?></td>
              <td><?= htmlspecialchars($r['created_at']) ?></td>
            </tr>
          <?php endwhile; ?>
          <?php if (mysqli_num_rows($list) == 0): ?>
            <tr>
              <td colspan="5" class="text-center text-muted">Tidak ada data riwayat sesuai filter.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>
