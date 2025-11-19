<?php
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$uid = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));
$name = $user['name'];

// Ringkasan
$total_anggota   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM users WHERE role='member'"))['jml'];
$total_simpanan  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(jumlah),0) AS total FROM simpanan"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM transaksi"))['jml'];
$total_riwayat   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM riwayat"))['jml'];

// Contoh data bulanan (bisa nanti diganti query nyata)
$bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
$data_simpanan = [1200000, 2200000, 1800000, 3500000, 2700000, 4100000, 3800000, 3200000, 2900000, 4500000, 5000000, 4700000];
$data_transaksi = [10, 15, 12, 25, 18, 22, 19, 21, 17, 23, 24, 28];
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Dashboard Admin - Koperasi Satria Manunggal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f5f8ff;
      margin: 0;
      display: flex;
      min-height: 100vh;
    }

    .header img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 8px;
      border: 2px solid #fff;
    }

    .header h5 {
      line-height: 1;
      font-weight: 600;
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
      cursor: pointer;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .card h5 {
      color: #0d6efd;
      font-weight: 600;
    }

    .card-icon {
      font-size: 38px;
      margin-bottom: 12px;
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

    .header h5 {
      line-height: 1;
    }
  </style>
</head>

<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4>KOPMA</h4>
    <div class="text-center mb-4">
      <img src="<?= $user['foto'] ? '../uploads/' . $user['foto'] : '../assets/default.png' ?>" class="profile-img mb-2" alt="Foto Admin">
      <div style="font-weight:600;"><?= htmlspecialchars($user['name']) ?></div>
      <div style="font-size:13px;opacity:0.8;">Admin</div>
    </div>
    <a href="dashboard.php" class="active">🏠 Dashboard</a>
    <a href="simpanan.php">💰 Simpanan</a>
    <a href="transaksi.php">💳 Transaksi</a>
    <a href="anggota.php">🧑‍💼 Anggota</a>
    <a href="riwayat.php">📄 Riwayat</a>
    <a href="profil.php">👤 Profil</a>
  </div>

  <!-- Header -->
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


  <!-- Main -->
  <div class="main-content">
    <h3>Dashboard Admin</h3>
    <p class="text-muted mb-4">Selamat datang kembali, <strong><?= htmlspecialchars($name) ?></strong> 👋</p>

    <!-- Card Ringkasan -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mb-4">
      <div class="col">
        <a href="anggota.php" class="text-decoration-none text-dark">
          <div class="card p-4 text-center">
            <div class="card-icon">🧑‍💼</div>
            <h5>Total Anggota</h5>
            <h3 class="mt-2 text-muted counter" data-target="<?= $total_anggota ?>">0</h3>
            <p>Jumlah anggota koperasi</p>
          </div>
        </a>
      </div>

      <div class="col">
        <a href="simpanan.php" class="text-decoration-none text-dark">
          <div class="card p-4 text-center">
            <div class="card-icon">💰</div>
            <h5>Total Simpanan</h5>
            <h3 class="mt-2 text-muted counter" data-target="<?= $total_simpanan ?>">0</h3>
            <p>Total simpanan seluruh anggota</p>
          </div>
        </a>
      </div>

      <div class="col">
        <a href="transaksi.php" class="text-decoration-none text-dark">
          <div class="card p-4 text-center">
            <div class="card-icon">💳</div>
            <h5>Total Transaksi</h5>
            <h3 class="mt-2 text-muted counter" data-target="<?= $total_transaksi ?>">0</h3>
            <p>Keseluruhan transaksi</p>
          </div>
        </a>
      </div>

      <div class="col">
        <a href="riwayat.php" class="text-decoration-none text-dark">
          <div class="card p-4 text-center">
            <div class="card-icon">📄</div>
            <h5>Total Riwayat</h5>
            <h3 class="mt-2 text-muted counter" data-target="<?= $total_riwayat ?>">0</h3>
            <p>Catatan aktivitas sistem</p>
          </div>
        </a>
      </div>
    </div>

    <!-- Grafik Chart.js -->
    <div class="card p-4">
      <h5 class="mb-4">📊 Grafik Aktivitas Bulanan</h5>
      <canvas id="grafikKoperasi" height="100"></canvas>
    </div>

    <div class="card p-4 mt-4">
      <h5 class="mb-3">Informasi Sistem</h5>
      <p>Melalui dashboard ini, admin dapat memantau aktivitas koperasi,
        mengelola anggota, simpanan, dan transaksi dengan mudah. Gunakan menu di samping
        untuk navigasi antar fitur koperasi.</p>
    </div>
  </div>

  <!-- Counter Animation -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const counters = document.querySelectorAll('.counter');
      counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        const speed = 40;
        const update = () => {
          const count = +counter.innerText.replace(/[^\d]/g, '');
          const inc = target / 30;
          if (count < target) {
            counter.innerText = Math.ceil(count + inc).toLocaleString('id-ID');
            setTimeout(update, speed);
          } else {
            counter.innerText = target.toLocaleString('id-ID');
          }
        };
        update();
      });
    });
  </script>

  <!-- Chart.js Grafik -->
  <script>
    const ctx = document.getElementById('grafikKoperasi').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($bulan) ?>,
        datasets: [{
            label: 'Total Simpanan (Rp)',
            data: <?= json_encode($data_simpanan) ?>,
            backgroundColor: 'rgba(13,110,253,0.6)',
            borderColor: '#0d6efd',
            borderWidth: 1,
            borderRadius: 5
          },
          {
            label: 'Jumlah Transaksi',
            data: <?= json_encode($data_transaksi) ?>,
            backgroundColor: 'rgba(0,180,216,0.6)',
            borderColor: '#00b4d8',
            borderWidth: 1,
            borderRadius: 5
          }
        ]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: '#333'
            }
          },
          x: {
            ticks: {
              color: '#333'
            }
          }
        },
        plugins: {
          legend: {
            position: 'top'
          },
          title: {
            display: false
          }
        }
      }
    });
  </script>
</body>

</html>
