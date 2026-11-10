-- ============================================================
-- KostFind - database/kost_find.sql
-- Jalankan lewat phpMyAdmin (tab Import) atau: mysql -u root < kost_find.sql
-- Sumber kriteria: Form_Penilaian_Kost.csv (Google Form penelitian)
-- ============================================================
CREATE DATABASE IF NOT EXISTS kost_find CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kost_find;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS nilai_kost, kost_fasilitas, opsi_nilai, kriteria, fasilitas, kost, admin;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE admin (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,            -- hasil password_hash()
  nama VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE kost (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(120) NOT NULL,
  alamat VARCHAR(255) NOT NULL,
  tipe_kost ENUM('Khusus Perempuan','Khusus Laki-Laki','Campuran') NOT NULL,  -- "Tipe Kost" di form
  harga_bulan INT UNSIGNED NOT NULL,         -- "Harga kost per bulan"
  jarak_meter INT UNSIGNED NOT NULL,         -- "Jarak kost ke kampus"
  deskripsi TEXT NULL,
  foto VARCHAR(255) NULL,
  status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE kriteria (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(5) NOT NULL UNIQUE,           -- C01, C02, ...
  nama VARCHAR(100) NOT NULL,
  kelompok VARCHAR(30) NOT NULL,             -- Harga, Jarak, Fasilitas, Keamanan, dst.
  tipe ENUM('benefit','cost') NOT NULL,
  bobot DECIMAL(5,2) NOT NULL,               -- persen; total semua kriteria = 100
  kolom_kost VARCHAR(20) NULL                -- jika nilainya diambil dari kolom tabel kost
) ENGINE=InnoDB;

-- Pilihan jawaban Google Form + skor numeriknya (dipakai form admin & form user)
CREATE TABLE opsi_nilai (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kriteria_id INT UNSIGNED NOT NULL,
  label VARCHAR(60) NOT NULL,
  skor TINYINT UNSIGNED NOT NULL,
  FOREIGN KEY (kriteria_id) REFERENCES kriteria(id) ON DELETE CASCADE,
  UNIQUE (kriteria_id, skor)
) ENGINE=InnoDB;

-- Matriks keputusan (Xij) untuk kriteria berskala (bukan harga/jarak)
CREATE TABLE nilai_kost (
  kost_id INT UNSIGNED NOT NULL,
  kriteria_id INT UNSIGNED NOT NULL,
  nilai DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (kost_id, kriteria_id),
  FOREIGN KEY (kost_id) REFERENCES kost(id) ON DELETE CASCADE,
  FOREIGN KEY (kriteria_id) REFERENCES kriteria(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Master fasilitas tambahan (informasi, tidak ikut dihitung SAW)
CREATE TABLE fasilitas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(80) NOT NULL UNIQUE,
  ikon VARCHAR(40) NOT NULL DEFAULT 'fa-circle-check'
) ENGINE=InnoDB;

CREATE TABLE kost_fasilitas (
  kost_id INT UNSIGNED NOT NULL,
  fasilitas_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (kost_id, fasilitas_id),
  FOREIGN KEY (kost_id) REFERENCES kost(id) ON DELETE CASCADE,
  FOREIGN KEY (fasilitas_id) REFERENCES fasilitas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Admin awal: username 'admin', password 'admin123' (SEGERA GANTI)
INSERT INTO admin (username,password,nama) VALUES ('admin','$2y$10$cYHO61JJiOi/srz4NXCHu.9g.ZFBaCU0tBzHzpiojWj.eOK/F4Yxi','Administrator KostFind');

-- Kriteria (urutan id = urutan pertanyaan di Google Form). BOBOT = PLACEHOLDER, ubah di menu Kriteria.
INSERT INTO kriteria (id,kode,nama,kelompok,tipe,bobot,kolom_kost) VALUES
(1,'C01','Harga kost per bulan (Rp)','Harga','cost',16.00,'harga_bulan'),
(2,'C02','Jarak kost ke kampus (meter)','Jarak','cost',11.00,'jarak_meter'),
(3,'C03','Tipe kamar','Tipe Kamar','benefit',5.00,NULL),
(4,'C04','AC/Kipas','Fasilitas','benefit',2.00,NULL),
(5,'C05','Kamar Mandi','Fasilitas','benefit',2.00,NULL),
(6,'C06','Dapur','Fasilitas','benefit',2.00,NULL),
(7,'C07','Wi-Fi','Fasilitas','benefit',2.00,NULL),
(8,'C08','Air','Fasilitas','benefit',2.00,NULL),
(9,'C09','Listrik','Fasilitas','benefit',2.00,NULL),
(10,'C10','Tempat Tidur','Fasilitas','benefit',2.00,NULL),
(11,'C11','Lemari','Fasilitas','benefit',2.00,NULL),
(12,'C12','Meja Belajar','Fasilitas','benefit',2.00,NULL),
(13,'C13','Tempat Parkir','Fasilitas','benefit',2.00,NULL),
(14,'C14','Tempat Menjemur Pakaian','Fasilitas','benefit',2.00,NULL),
(15,'C15','Peralatan Dapur','Fasilitas','benefit',2.00,NULL),
(16,'C16','CCTV','Keamanan','benefit',4.00,NULL),
(17,'C17','Penjaga/security','Keamanan','benefit',4.00,NULL),
(18,'C18','Sistem akses masuk bangunan','Keamanan','benefit',4.00,NULL),
(19,'C19','Kebersihan lingkungan kost','Kebersihan','benefit',2.00,NULL),
(20,'C20','Kebersihan kamar kost','Kebersihan','benefit',2.00,NULL),
(21,'C21','Kebersihan kamar mandi','Kebersihan','benefit',2.00,NULL),
(22,'C22','Kebersihan dapur','Kebersihan','benefit',2.00,NULL),
(23,'C23','Pengelolaan sampah','Kebersihan','benefit',2.00,NULL),
(24,'C24','Kenyamanan kamar','Kenyamanan','benefit',2.00,NULL),
(25,'C25','Tingkat kebisingan','Kenyamanan','benefit',2.00,NULL),
(26,'C26','Ventilasi dan sirkulasi udara','Kenyamanan','benefit',2.00,NULL),
(27,'C27','Pencahayaan kamar','Kenyamanan','benefit',2.00,NULL),
(28,'C28','Kenyamanan untuk belajar','Kenyamanan','benefit',2.00,NULL),
(29,'C29','Kenyamanan lingkungan sekitar','Kenyamanan','benefit',2.00,NULL),
(30,'C30','Fleksibilitas peraturan kost','Peraturan','benefit',2.00,NULL),
(31,'C31','Menerima tamu','Peraturan','benefit',2.00,NULL),
(32,'C32','Tamu boleh menginap','Peraturan','benefit',2.00,NULL),
(33,'C33','Jam malam','Peraturan','benefit',2.00,NULL),
(34,'C34','Memelihara hewan','Peraturan','benefit',2.00,NULL);

INSERT INTO opsi_nilai (kriteria_id,label,skor) VALUES
(3,'Kamar mandi luar',1),
(3,'Kamar mandi dalam',2),
(4,'Tidak sama sekali',1),
(4,'Ada tetapi kurang baik',2),
(4,'Ada dan Baik',3),
(4,'Ada dan Sangat Baik',4),
(5,'Tidak sama sekali',1),
(5,'Ada tetapi kurang baik',2),
(5,'Ada dan Baik',3),
(5,'Ada dan Sangat Baik',4),
(6,'Tidak sama sekali',1),
(6,'Ada tetapi kurang baik',2),
(6,'Ada dan Baik',3),
(6,'Ada dan Sangat Baik',4),
(7,'Tidak sama sekali',1),
(7,'Ada tetapi kurang baik',2),
(7,'Ada dan Baik',3),
(7,'Ada dan Sangat Baik',4),
(8,'Tidak sama sekali',1),
(8,'Ada tetapi kurang baik',2),
(8,'Ada dan Baik',3),
(8,'Ada dan Sangat Baik',4),
(9,'Tidak sama sekali',1),
(9,'Ada tetapi kurang baik',2),
(9,'Ada dan Baik',3),
(9,'Ada dan Sangat Baik',4),
(10,'Tidak sama sekali',1),
(10,'Ada tetapi kurang baik',2),
(10,'Ada dan Baik',3),
(10,'Ada dan Sangat Baik',4),
(11,'Tidak sama sekali',1),
(11,'Ada tetapi kurang baik',2),
(11,'Ada dan Baik',3),
(11,'Ada dan Sangat Baik',4),
(12,'Tidak sama sekali',1),
(12,'Ada tetapi kurang baik',2),
(12,'Ada dan Baik',3),
(12,'Ada dan Sangat Baik',4),
(13,'Tidak sama sekali',1),
(13,'Ada tetapi kurang baik',2),
(13,'Ada dan Baik',3),
(13,'Ada dan Sangat Baik',4),
(14,'Tidak sama sekali',1),
(14,'Ada tetapi kurang baik',2),
(14,'Ada dan Baik',3),
(14,'Ada dan Sangat Baik',4),
(15,'Tidak sama sekali',1),
(15,'Ada tetapi kurang baik',2),
(15,'Ada dan Baik',3),
(15,'Ada dan Sangat Baik',4),
(16,'Tidak ada',1),
(16,'Ada di beberapa area',2),
(16,'Ada di seluruh area',3),
(17,'Tidak ada',1),
(17,'Pada jam tertentu',2),
(17,'24 Jam',3),
(18,'Tidak ada sistem khusus',1),
(18,'Kunci gerbang biasa',2),
(18,'Access card/kunci pribadi',3),
(19,'Sangat Buruk',1),
(19,'Buruk',2),
(19,'Cukup',3),
(19,'Baik',4),
(19,'Sangat Baik',5),
(20,'Sangat Buruk',1),
(20,'Buruk',2),
(20,'Cukup',3),
(20,'Baik',4),
(20,'Sangat Baik',5),
(21,'Sangat Buruk',1),
(21,'Buruk',2),
(21,'Cukup',3),
(21,'Baik',4),
(21,'Sangat Baik',5),
(22,'Sangat Buruk',1),
(22,'Buruk',2),
(22,'Cukup',3),
(22,'Baik',4),
(22,'Sangat Baik',5),
(23,'Sangat Buruk',1),
(23,'Buruk',2),
(23,'Cukup',3),
(23,'Baik',4),
(23,'Sangat Baik',5),
(24,'Sangat Tidak Nyaman',1),
(24,'Tidak Nyaman',2),
(24,'Cukup Nyaman',3),
(24,'Nyaman',4),
(24,'Sangat Nyaman',5),
(25,'Sangat Tidak Nyaman',1),
(25,'Tidak Nyaman',2),
(25,'Cukup Nyaman',3),
(25,'Nyaman',4),
(25,'Sangat Nyaman',5),
(26,'Sangat Tidak Nyaman',1),
(26,'Tidak Nyaman',2),
(26,'Cukup Nyaman',3),
(26,'Nyaman',4),
(26,'Sangat Nyaman',5),
(27,'Sangat Tidak Nyaman',1),
(27,'Tidak Nyaman',2),
(27,'Cukup Nyaman',3),
(27,'Nyaman',4),
(27,'Sangat Nyaman',5),
(28,'Sangat Tidak Nyaman',1),
(28,'Tidak Nyaman',2),
(28,'Cukup Nyaman',3),
(28,'Nyaman',4),
(28,'Sangat Nyaman',5),
(29,'Sangat Tidak Nyaman',1),
(29,'Tidak Nyaman',2),
(29,'Cukup Nyaman',3),
(29,'Nyaman',4),
(29,'Sangat Nyaman',5),
(30,'Sangat Ketat',1),
(30,'Ketat',2),
(30,'Cukup Fleksibel',3),
(30,'Fleksibel',4),
(30,'Sangat Fleksibel',5),
(31,'Tidak',1),
(31,'Ya, dengan aturan tertentu',2),
(31,'Ya, bebas',3),
(32,'Tidak',1),
(32,'Ya, dengan aturan tertentu',2),
(32,'Ya, bebas',3),
(33,'Ya',1),
(33,'Tidak',2),
(34,'Tidak',1),
(34,'Ya, dengan aturan tertentu',2),
(34,'Ya, bebas',3);

INSERT INTO fasilitas (nama) VALUES ('Kasur'),('Kipas angin'),('AC'),('Wi-Fi'),('Kamar mandi dalam'),('Dapur bersama'),('Parkir motor'),('Parkir mobil'),('Jemuran'),('Mesin cuci'),('Dispenser/air minum'),('Ruang tamu bersama');

INSERT INTO kost (id,nama,alamat,tipe_kost,harga_bulan,jarak_meter,deskripsi,status) VALUES
(1,'Kost Melati Putri','Jl. Banggeris Gg. 6, Gunung Kelua, Samarinda','Khusus Perempuan',950000,300,'Contoh data untuk uji coba. Ganti dengan data kost sebenarnya.','aktif'),
(2,'Kost Borneo Putra','Jl. Pramuka Gg. 3, Sempaja, Samarinda','Khusus Laki-Laki',700000,1500,'Contoh data untuk uji coba. Ganti dengan data kost sebenarnya.','aktif'),
(3,'Kost Mahakam Residence','Jl. Ir. H. Juanda, Air Hitam, Samarinda','Campuran',1750000,800,'Contoh data untuk uji coba. Ganti dengan data kost sebenarnya.','aktif'),
(4,'Kost Pakadang','Jl. Samratulangi Gg. H. Sani, Samarinda Seberang','Khusus Perempuan',850000,1600,'Contoh data untuk uji coba. Ganti dengan data kost sebenarnya.','aktif'),
(5,'Kost Hemat Mulawarman','Jl. Kemakmuran, Gunung Kelua, Samarinda','Khusus Laki-Laki',550000,450,'Contoh data untuk uji coba. Ganti dengan data kost sebenarnya.','aktif');

-- C01 (harga) & C02 (jarak) otomatis diambil dari tabel kost
INSERT INTO nilai_kost (kost_id,kriteria_id,nilai) VALUES
(1,3,2),(1,4,4),(1,5,4),(1,6,3),(1,7,4),(1,8,4),(1,9,4),(1,10,3),(1,11,4),(1,12,3),(1,13,4),(1,14,2),(1,15,3),(1,16,3),(1,17,1),(1,18,3),(1,19,4),(1,20,5),(1,21,4),(1,22,3),(1,23,4),(1,24,4),(1,25,4),(1,26,4),(1,27,4),(1,28,4),(1,29,4),(1,30,2),(1,31,2),(1,32,2),(1,33,1),(1,34,1),(2,3,1),(2,4,1),(2,5,3),(2,6,3),(2,7,3),(2,8,3),(2,9,3),(2,10,4),(2,11,3),(2,12,3),(2,13,3),(2,14,1),(2,15,2),(2,16,2),(2,17,1),(2,18,2),(2,19,3),(2,20,3),(2,21,3),(2,22,3),(2,23,3),(2,24,3),(2,25,3),(2,26,3),(2,27,3),(2,28,3),(2,29,3),(2,30,3),(2,31,3),(2,32,3),(2,33,2),(2,34,2),(3,3,2),(3,4,4),(3,5,4),(3,6,4),(3,7,4),(3,8,4),(3,9,4),(3,10,4),(3,11,4),(3,12,4),(3,13,4),(3,14,4),(3,15,4),(3,16,3),(3,17,3),(3,18,3),(3,19,5),(3,20,5),(3,21,5),(3,22,4),(3,23,5),(3,24,5),(3,25,4),(3,26,5),(3,27,5),(3,28,5),(3,29,5),(3,30,3),(3,31,2),(3,32,2),(3,33,2),(3,34,1),(4,3,1),(4,4,4),(4,5,4),(4,6,4),(4,7,4),(4,8,4),(4,9,4),(4,10,4),(4,11,4),(4,12,4),(4,13,4),(4,14,4),(4,15,4),(4,16,2),(4,17,1),(4,18,3),(4,19,5),(4,20,5),(4,21,5),(4,22,5),(4,23,5),(4,24,5),(4,25,4),(4,26,4),(4,27,4),(4,28,5),(4,29,5),(4,30,3),(4,31,2),(4,32,2),(4,33,2),(4,34,1),(5,3,1),(5,4,1),(5,5,3),(5,6,2),(5,7,2),(5,8,2),(5,9,3),(5,10,3),(5,11,2),(5,12,2),(5,13,2),(5,14,1),(5,15,1),(5,16,2),(5,17,1),(5,18,2),(5,19,3),(5,20,3),(5,21,2),(5,22,2),(5,23,3),(5,24,3),(5,25,2),(5,26,3),(5,27,3),(5,28,3),(5,29,3),(5,30,4),(5,31,3),(5,32,3),(5,33,2),(5,34,3);

INSERT INTO kost_fasilitas (kost_id,fasilitas_id) VALUES (1,2),(1,4),(1,5),(1,7),(1,9),(1,11),(2,2),(2,4),(2,7),(2,9),(3,3),(3,4),(3,5),(3,6),(3,7),(3,8),(3,9),(3,10),(3,11),(3,12),(4,2),(4,4),(4,7),(4,9),(5,2),(5,4),(5,7),(5,9);
