-- install.sql
CREATE DATABASE IF NOT EXISTS kopma CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE kopma;

-- users = anggota & admin
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(150) NOT NULL,
  nta VARCHAR(50) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  phone VARCHAR(30) DEFAULT NULL,
  role ENUM('admin','member') NOT NULL DEFAULT 'member',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- simpanan (setoran)
CREATE TABLE simpanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  tanggal DATE NOT NULL,
  jumlah DECIMAL(15,2) NOT NULL,
  note VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- transaksi (misal pinjaman/pembayaran)
CREATE TABLE transaksi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  tanggal DATE NOT NULL,
  jenis VARCHAR(50) NOT NULL,
  jumlah DECIMAL(15,2) NOT NULL,
  metode VARCHAR(50) DEFAULT NULL,
  note VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- riwayat (audit log singkat)
CREATE TABLE riwayat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  aksi VARCHAR(255),
  detail TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- contoh admin default
INSERT INTO users (username, password, name, role)
VALUES ('admin', '{REPLACE_ME}', 'Administrator', 'admin');

-- NOTE:
-- Ganti {REPLACE_ME} dengan hash password (lihat instruksi di README atau gunakan php script untuk register admin)