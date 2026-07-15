<?php 
require_once 'config.php'; 

if (!function_exists('e')) {
    function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$jam_op1 = 'Senin–Kamis: 08.00–16.00 WIB';
$jam_op2 = 'Jumat: 08.00–17.00 WIB';
$alamat  = 'Jl. OP. Nai Horsik Damanik No. 08 Sarimatondang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>SILADA Sidamanik</title>
  <link rel="icon" type="image/png" href="gambar/logo_kecamatan.jpeg">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    body { background: #fff; margin:0; display: flex; flex-direction: column; min-height: 100vh; }
    .content-wrapper { flex: 1; }

    .navbar { background-color: #002B5B; }
    .navbar a, .navbar-brand { color: #fff !important; }
    .navbar .nav-link { color: #fff !important; font-weight: bold; }
    .navbar .dropdown-menu-dark { background-color: #002B5B; }
    .navbar .dropdown-item { 
      color: #fff !important; 
      font-weight: bold; 
      white-space: normal; 
      line-height: 1.3; 
    }
    .navbar .dropdown-item:hover { background-color: #004080; }

    .content {
      max-width: 900px;
      margin: 40px auto;
      padding: 40px;
      border: 2px solid #ccc;
      border-radius: 12px;
      background-color: #fff;
      background-image: url('gambar/logo_kecamatan.jpeg');
      background-repeat: no-repeat;
      background-position: center;
      background-size: 40%;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      position: relative;
      overflow: hidden;
    }

    .content::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(255,255,255,0.85);
      border-radius: 12px;
      z-index: 0;
    }

    .content h2, .content h4, .content p, .content ol {
      position: relative;
      z-index: 1;
    }

    .content h2 {
      text-align: center;
      font-weight: bold;
      margin-bottom: 25px;
    }

    .content h4 {
      font-weight: bold;
      margin-top: 20px;
    }

    .content p {
      text-align: justify;
      line-height: 1.7;
    }

    .content ol {
      text-align: left;
      padding-left: 20px;
    }

    .footer-custom { background-color: #002B5B; color: #ffffff; }
  </style>
</head>
<body>

<div class="content-wrapper">
    <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
          <img src="gambar/logo_kecamatan.jpeg" alt="Logo" width="50" height="50" class="me-2">
          <div>
            <small style="font-size:15px;">Kecamatan Sidamanik</small><br>
            <small style="font-size:16px;">Kabupaten Simalungun</small>
          </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>

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

            <li class="nav-item"><a class="nav-link" href="berita.php">Berita</a></li>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="content mb-5">
      <h2>Visi dan Misi Kecamatan Sidamanik</h2>

      <h4>Visi</h4>
      <p><b>“Rakyat harus sejahtera.”</b></p>

      <h4>Misi</h4>
      <ol>
        <li>Pemulihan ekonomi.</li>
        <li>Pemulihan kesehatan.</li>
        <li>Penerapan GCG (Good and Clean Government).</li>
        <li>Pengembangan pendidikan dan kebudayaan.</li>
        <li>Pengembangan pariwisata dan ekonomi kreatif.</li>
        <li>Peningkatan pertanian dan pengembangan sistem agribisnis.</li>
        <li>Peningkatan kualitas infrastruktur.</li>
        <li>Peningkatan kualitas generasi muda/milenial.</li>
        <li>Restrukturisasi anggaran (perbaikan postur APBD).</li>
        <li>Restrukturisasi organisasi dan reformasi birokrasi.</li>
      </ol>
    </div>
</div> <footer class="footer-custom pt-4 pb-3">
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
      © <?= date('Y') ?> <strong>SILADA Sidamanik</strong>. Semua Hak Cipta Dilindungi.
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