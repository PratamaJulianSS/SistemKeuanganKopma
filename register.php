<?php
require 'db.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password']; // TANPA HASH
  $name = trim($_POST['name']);
  $nta = trim($_POST['nta']);
  $address = trim($_POST['address']);
  $phone = trim($_POST['phone']);

  if (!$username || !$password || !$name) {
    $err = "Username, password, dan nama wajib diisi.";
  } else {

    // Cek username
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username=?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
      $err = "Username sudah digunakan.";
    } else {
      // TANPA password_hash
      $stmt = mysqli_prepare($conn, "INSERT INTO users (username,password,name,nta,address,phone,role) VALUES (?,?,?,?,?,?, 'member')");
      mysqli_stmt_bind_param($stmt, "ssssss", $username, $password, $name, $nta, $address, $phone);

      if (mysqli_stmt_execute($stmt)) {
        header("Location: login.php?registered=1");
        exit;
      } else {
        $err = "Gagal menyimpan data.";
      }
    }
  }
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Registrasi - Koperasi Satria Manunggal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #1900ff, #0064fa);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
    }

    /* Card register */
    .register-box {
      background: #fff;
      border-radius: 15px;
      padding: 40px 30px;
      width: 450px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      max-height: 90vh;
      overflow-y: auto;
      transition: 0.3s ease;
    }

    /* Hover effect – membesar halus */
    .register-box:hover {
      transform: scale(1.02);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
    }

    .logo-kopma {
      width: 20px;
      height: 20px;
      object-fit: contain;
      margin-bottom: 5px;
    }

    /* Title */
    h3 {
      text-align: center;
      margin-bottom: 25px;
      color: #0d6efd;
      font-weight: 700;
    }

    /* Input glow saat fokus */
    .form-control {
      border-radius: 10px;
      transition: 0.2s;
    }

    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 8px rgba(13, 110, 253, 0.5);
    }

    /* Tombol register */
    .btn-register {
      width: 100%;
      border-radius: 10px;
      font-weight: 600;
      padding: 10px;
      transition: 0.25s;
    }

    .btn-register:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
    }

    /* Footer */
    .footer-text {
      text-align: center;
      margin-top: 15px;
    }

    .logo-register {
      width: 90px;
      height: 60px;
      object-fit: contain;
    }


    .footer-text a {
      text-decoration: none;
      color: #0d6efd;
      font-weight: 500;
    }

    .footer-text a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>

  <div class="register-box">

    <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
      <img src="assets/logo.png" class="logo-register" alt="Logo">
      <div class="text-primary fw-bold text-center" style="font-size: 22px; line-height: 1.2;">
        KOPERASI <br> SATRIA MANUNGGAL
      </div>
    </div>


    <?php if ($err): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <form method="post">

      <div class="mb-2">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
      </div>

      <div class="mb-2">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>

      <div class="mb-2">
        <input type="text" name="name" class="form-control" placeholder="Nama Lengkap" required>
      </div>

      <div class="mb-2">
        <input type="text" name="nta" class="form-control" placeholder="Nomor Tanda Anggota (NTA)">
      </div>

      <div class="mb-2">
        <input type="text" name="phone" class="form-control" placeholder="No Telepon / WA">
      </div>

      <div class="mb-2">
        <textarea name="address" class="form-control" placeholder="Alamat"></textarea>
      </div>

      <button class="btn btn-primary btn-register" type="submit">Buat Akun</button>
    </form>

    <div class="footer-text">
      <small>Sudah punya akun? <a href="login.php">Login di sini</a></small>
    </div>
  </div>

</body>

</html>
