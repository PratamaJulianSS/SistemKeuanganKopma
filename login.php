<?php
require 'db.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password']; // password polos
  $role = $_POST['role'];

  $stmt = mysqli_prepare($conn, "SELECT id, password, role, name FROM users WHERE username=? AND role=?");
  mysqli_stmt_bind_param($stmt, "ss", $username, $role);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $id, $db_pass, $db_role, $name);

  if (mysqli_stmt_fetch($stmt)) {

    // Tanpa password_verify
    if ($password === $db_pass) {

      $_SESSION['user_id'] = $id;
      $_SESSION['username'] = $username;
      $_SESSION['role'] = $db_role;
      $_SESSION['name'] = $name;

      if ($db_role === 'admin') {
        header("Location: admin/dashboard.php");
      } else {
        header("Location: member/dashboard.php");
      }
      exit;
    } else {
      $err = "Password salah.";
    }
  } else {
    $err = "Akun tidak ditemukan.";
  }
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Login - Koperasi Satria Manunggal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #1900ff, rgb(0, 100, 250));
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
    }

    .login-box {
      background: #fff;
      border-radius: 15px;
      padding: 40px 30px;
      width: 400px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);

      /* ANIMASI HALUS TANPA BLUR */
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .login-box:hover {
      transform: scale(1.04);
      /* membesar sedikit */
      box-shadow: 0 18px 40px rgba(0, 0, 0, 0.32);
    }

    .logo-kopma {
      width: 150px;
      height: 80px;
      object-fit: contain;
      margin-bottom: 5px;
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

    .btn-login {
      width: 100%;
      border-radius: 10px;
      font-weight: 600;
    }

    /* ⭐ Link “Buat Akun” disamakan seperti yang kamu mau */
    .footer-text a {
      text-decoration: none;
      color: #0d6efd;
      font-weight: 500;
    }

    .footer-text a:hover {
      text-decoration: underline;
    }

    .footer-text {
      text-align: center;
      margin-top: 15px;
    }
  </style>
</head>

<body>

  <div class="login-box">
    <div class="text-center mb-3">
      <img src="assets/Logo.png" class="logo-kopma" alt="Logo" />
    </div>


    <h3>KOPERASI<br>SATRIA MANUNGGAL</h3>

    <?php if ($err): ?>
      <div class="alert alert-danger"><?= e($err) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <select name="role" class="form-select" required>
          <option value="member">Login Sebagai Anggota</option>
          <option value="admin">Login Sebagai Admin</option>
        </select>
      </div>

      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
      </div>

      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>

      <button class="btn btn-primary btn-login" type="submit">LOGIN</button>
    </form>

    <div class="footer-text">
      <small>Belum punya akun? <a href="register.php">Buat Akun</a></small>
    </div>
  </div>

</body>

</html>
