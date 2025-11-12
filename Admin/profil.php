<?php
require '../db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$name = $_SESSION['name'];

// ambil data admin
$q = mysqli_query($conn, "SELECT * FROM users WHERE id=$uid");
$user = mysqli_fetch_assoc($q);

// proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['name']);
    $username = trim($_POST['username']);
    $nta      = trim($_POST['nta']);
    $telepon  = trim($_POST['telepon']);
    $alamat   = trim($_POST['alamat']);
    $foto     = $user['foto']; // default lama

    // upload foto baru
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "../uploads/";
        $file_name  = time() . "_" . basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];

        if (in_array($file_type, $allowed) && $_FILES["foto"]["size"] < 2000000) {
            move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
            // hapus foto lama
            if ($foto && file_exists("../uploads/" . $foto)) {
                unlink("../uploads/" . $foto);
            }
            $foto = $file_name;
        }
    }

    // update data ke database
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE users SET name=?, username=?, nta=?, telepon=?, alamat=?, foto=?, password=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssssssi", $nama, $username, $nta, $telepon, $alamat, $foto, $password, $uid);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE users SET name=?, username=?, nta=?, telepon=?, alamat=?, foto=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssssi", $nama, $username, $nta, $telepon, $alamat, $foto, $uid);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION['name'] = $nama;
    header("Location: profil.php?update=1");
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Profil Admin - Koperasi Satria Manunggal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {font-family:'Poppins',sans-serif;background:#f5f8ff;margin:0;display:flex;min-height:100vh;}
.header {position:fixed;top:0;left:230px;right:0;height:60px;background:linear-gradient(90deg,#0d6efd,#00b4d8);color:white;display:flex;align-items:center;justify-content:space-between;padding:0 20px;z-index:10;}
.header h5{margin:0;font-weight:600;}
.header a{color:white;text-decoration:none;font-weight:500;transition:0.3s;}
.header a:hover{text-decoration:underline;}
.sidebar{width:230px;background:linear-gradient(180deg,rgb(105,13,253),#00b4d8);color:white;padding-top:80px;position:fixed;height:100%;}
.sidebar h4{text-align:center;margin-bottom:30px;font-weight:700;}
.sidebar a{display:block;padding:12px 20px;color:white;text-decoration:none;font-weight:500;transition:0.2s;}
.sidebar a:hover,.sidebar a.active{background:rgba(255,255,255,0.2);border-radius:8px;}
.main-content{margin-left:230px;margin-top:60px;padding:30px;flex:1;}
.card{border-radius:15px;border:none;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
.profile-pic{width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid #0d6efd;}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h4>KOPMA</h4>
  <a href="dashboard.php">🏠 Dashboard</a>
  <a href="simpanan.php">💰 Simpanan</a>
  <a href="transaksi.php">💳 Transaksi</a>
  <a href="anggota.php">🧑‍💼 Anggota</a>
  <a href="riwayat.php">📄 Riwayat</a>
  <a href="profil.php" class="active">👤 Profil</a>
</div>

<!-- Header -->
<div class="header">
  <h5>KOPERASI SATRIA MANUNGGAL</h5>
  <div>
    <span class="me-3">
      <?php if($user['foto']): ?>
        <img src="../uploads/<?= htmlspecialchars($user['foto']) ?>" style="width:35px;height:35px;border-radius:50%;object-fit:cover;margin-right:6px;">
      <?php else: ?>
        👤
      <?php endif; ?>
      <?= htmlspecialchars($name) ?> (Admin)
    </span>
    <a href="../logout.php">Logout</a>
  </div>
</div>

<!-- Main -->
<div class="main-content">
  <h3>Profil Admin</h3>
  <p class="text-muted mb-4">Perbarui informasi profil Anda di bawah ini.</p>

  <?php if(isset($_GET['update'])): ?>
  <div class="alert alert-success">✅ Profil berhasil diperbarui.</div>
  <?php endif; ?>

  <div class="card p-4">
    <div class="text-center mb-4">
      <img src="<?= $user['foto'] ? '../uploads/'.$user['foto'] : '../assets/default.png' ?>" class="profile-pic mb-2" alt="Foto Profil">
      <div class="text-muted">Foto profil Anda</div>
    </div>

    <form method="post" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Nomor Tanda Anggota (NTA)</label>
        <input type="text" name="nta" value="<?= htmlspecialchars($user['nta'] ?? '') ?>" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">No Telepon / WA</label>
        <input type="text" name="telepon" value="<?= htmlspecialchars($user['telepon'] ?? '') ?>" class="form-control">
      </div>
      <div class="col-md-12">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label">Foto Profil</label>
        <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Password Baru (opsional)</label>
        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
      </div>
      <div class="col-12 text-end mt-3">
        <button class="btn btn-primary px-4">💾 Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>