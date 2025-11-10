<?php
require 'db.php';

// Jika belum login, arahkan ke login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Jika sudah login, arahkan sesuai role
if ($_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit;
} else {
    header("Location: member/dashboard.php");
    exit;
}
?>