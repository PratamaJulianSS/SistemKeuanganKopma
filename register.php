<?php
require 'db.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $name = trim($_POST['name']);
    $nta = trim($_POST['nta']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    if (!$username || !$password || !$name) {
        $err = "Username, password, dan nama wajib diisi.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $err = "Username sudah digunakan.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username,password,name,nta,address,phone,role) VALUES (?,?,?,?,?,?, 'member')");
            mysqli_stmt_bind_param($stmt, "ssssss", $username, $hash, $name, $nta, $address, $phone);
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
  background: linear-gradient(135deg, #0d6efd, #00b4d8);
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Poppins', sans-serif;
}
.register-box {
  background: #fff;
  border-radius: 15px;
  padding: 40px 30px;
  width: 450px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  overflow-y: auto;
  max-height: 90vh;
}
h3 {
  text-align: center;
  margin-bottom: 25px;
  color: #0d6efd;
  font-weight: 700;
}
.form-control {
  border-radius: 10px;
}
.btn-register {
  width: 100%;
  border-radius: 10px;
  font-weight: 600;
}
.footer-text {
  text-align: center;
  margin-top: 15px;
}
</style>
</head>
<body>

<div class="register-box">
  <h3>KOPERASI<br>SATRIA MANUNGGAL</h3>

  <?php if($err): ?>
    <div class="alert alert-danger"><?= e($err) ?></div>
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