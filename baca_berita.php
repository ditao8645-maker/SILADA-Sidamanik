<?php
require_once __DIR__ . '/config.php';

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$jam_op1 = 'Senin–Kamis: 08.00–16.00 WIB';
$jam_op2 = 'Jumat: 08.00–17.00 WIB';
$alamat  = 'Jl. OP. Nai Horsik Damanik No. 08 Sarimatondang';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    die("<h2 style='text-align:center; margin-top:50px; font-family:sans-serif;'>Berita tidak ditemukan.</h2>");
}

$sql = "SELECT b.*, pt.nama AS nama_penulis, pt.jabatan 
        FROM berita b 
        LEFT JOIN petugas pt ON b.id_akun = pt.id_akun 
        WHERE b.id_berita = ? AND b.status = 'Publish' LIMIT 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$berita = $result->fetch_assoc();
$stmt->close();

if (!$berita) {
    die("<h2 style='text-align:center; margin-top:50px; font-family:sans-serif;'>Berita tidak tersedia atau telah dihapus.</h2>");
}

$nama_penulis_tampil = 'Sistem/Admin';
if (!empty($berita['nama_penulis'])) {
    $jabatan = strtolower($berita['jabatan'] ?? '');
    $label_jabatan = ($jabatan == 'desa') ? 'Pemerintah Desa' : 'Kecamatan Sidamanik';
    $nama_penulis_tampil = e($berita['nama_penulis']) . ' (' . $label_jabatan . ')';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>SILADA Sidamanik - <?= e($berita['judul']) ?></title>
  <link rel="icon" type="image/png" href="gambar/logo_kecamatan.jpeg">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body { background:#f5f7fb; margin:0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; flex-direction: column; min-height: 100vh; }
    .content-wrapper { flex: 1; }

    .navbar { background-color:#002B5B; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .navbar a, .navbar-brand { color:#fff !important; }
    .navbar .nav-link { color:#fff !important; font-weight:bold; display: flex; align-items: center; height: 48px; }
    .navbar .dropdown-menu-dark { background-color:#002B5B; border: none; }
    .navbar .dropdown-item { color:#fff !important; font-weight:bold; white-space: normal; line-height: 1.3; }
    .navbar .dropdown-item:hover { background-color:#004080; }

    .read-container { max-width: 850px; margin: 40px auto 60px auto; padding: 40px; background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    .btn-back { display: inline-block; margin-bottom: 25px; color: #64748b; font-weight: 600; text-decoration: none; transition: 0.3s; }
    .btn-back:hover { color: #002B5B; transform: translateX(-5px); }
    
    .read-kategori { background: #e74c3c; color: #fff; font-weight: bold; text-transform: uppercase; font-size: 13px; padding: 5px 15px; border-radius: 20px; display: inline-block; margin-bottom: 15px; }
    .read-judul { font-size: 32px; font-weight: 900; color: #1e293b; margin-bottom: 15px; line-height: 1.4; }
    .read-meta { color: #64748b; font-size: 14px; margin-bottom: 30px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap;}
    
    .read-foto { width: 100%; border-radius: 12px; margin-bottom: 35px; object-fit: cover; max-height: 450px; background: #f0f0f0; border: 1px solid #e2e8f0; }
    
    .read-konten { 
        font-size: 17px; 
        color: #334155; 
        line-height: 1.8; 
        text-align: justify;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word; 
    }
    .read-konten p { margin-bottom: 1.5rem; }
    .read-konten img, .read-konten iframe, .read-konten table { max-width: 100%; height: auto; }

    .footer-custom { background-color: #002B5B; color: #ffffff; }
  </style>
</head>
<body>

<div class="content-wrapper">
    <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
          <img src="gambar/logo_kecamatan.jpeg" width="50" height="50" class="me-2">
          <div>
            <small style="font-size:15px;">Kecamatan Sidamanik</small><br>
            <small style="font-size:16px;">Kabupaten Simalungun</small>
          </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto fw-bold">
            <li class="nav-item"><a class="nav-link" href="index.php">🏠 Home</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">About</a>
              <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item" href="profil_kantor_camat.php">Profil Kantor Camat</a></li>
                <li><a class="dropdown-item" href="visi_misi.php">Visi & Misi</a></li>
                <li><a class="dropdown-item" href="struktur_organisasi.php">Struktur Organisasi</a></li>
                <li><a class="dropdown-item" href="informasi_kecamatan.php">Informasi Kecamatan</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Layanan Administrasi</a>
              <ul class="dropdown-menu dropdown-menu-dark">
                <?php
                if(isset($mysqli)){
                    $query_layanan = "SELECT id, nama_layanan, deskripsi FROM layanan WHERE status = 'Aktif' ORDER BY id ASC";
                    $result_layanan = $mysqli->query($query_layanan);

                    if ($result_layanan && $result_layanan->num_rows > 0) {
                        while ($row_layanan = $result_layanan->fetch_assoc()) {
                            $nama_lay      = e($row_layanan['nama_layanan']); 
                            $deskripsi_lay = e($row_layanan['deskripsi']); 

                            echo '<li><a class="dropdown-item" href="login.php" data-bs-toggle="tooltip" data-bs-placement="left" title="' . $deskripsi_lay . '">' . $nama_lay . '</a></li>';
                        }
                    } else {
                        echo '<li><a class="dropdown-item text-muted" href="#">Tidak ada layanan aktif</a></li>';
                    }
                }
                ?>
              </ul>
            </li>
            <li class="nav-item"><a class="nav-link active" href="berita.php">Berita</a></li>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container">
        <div class="read-container">
            <a href="berita.php" class="btn-back"><i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Daftar Berita</a>
            
            <br>
            <span class="read-kategori"><?= e($berita['kategori']) ?></span>
            <h1 class="read-judul"><?= e($berita['judul']) ?></h1>
            
            <div class="read-meta">
                <span><i class="fa-regular fa-calendar-alt me-1"></i> <?= date('d F Y', strtotime($berita['tanggal'])) ?></span>
                <span><i class="fa-regular fa-clock me-1"></i> <?= date('H:i', strtotime($berita['tanggal'])) ?> WIB</span>
                <span><i class="fa-solid fa-user-pen me-1"></i> <?= $nama_penulis_tampil ?></span>
            </div>

            <?php if (!empty($berita['gambar'])): ?>
                <img src="uploads/berita/<?= e($berita['gambar']) ?>" class="read-foto" alt="Foto Berita">
            <?php endif; ?>

            <div class="read-konten">
                <?= $berita['konten'] ?>
            </div>
        </div>
    </div>
</div>

<footer class="footer-custom pt-4 pb-3">
  <div class="container text-center text-md-start">
    <div class="row">
      <div class="col-md-6 mb-3">
        <h5 class="fw-bold text-uppercase mb-3">Kantor Camat Sidamanik</h5>
        <p class="mb-1">📍 <strong>Alamat:</strong> <br> <?= e($alamat) ?></p>
      </div>
      
      <div class="col-md-6 mb-3 text-md-end">
        <h5 class="fw-bold text-uppercase mb-3">Jam Operasional</h5>
        <p class="mb-1">🕒 <?= e($jam_op1) ?></p>
        <p class="mb-0">🕒 <?= e($jam_op2) ?></p>
      </div>
    </div>
    
    <hr class="border-light mt-3 mb-3">
    
    <div class="text-center small">
      &copy; <?= date('Y') ?> <strong>SILADA Sidamanik</strong>. Semua Hak Cipta Dilindungi.
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
    new bootstrap.Tooltip(el);
  });
</script>
</body>
</html>