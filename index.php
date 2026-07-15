<?php
require_once 'config.php'; 

if (!function_exists('e')) {
    function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$slides = [
  'Gambar/slide1.jpg',
  'Gambar/slide2.jpg',
];

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
    body { background:#fff; margin:0; }
    
    .navbar { background-color:#002B5B; }
    .navbar a, .navbar-brand { color:#fff !important; }
    .navbar .nav-link { color:#fff !important; font-weight:bold; }
    .navbar .dropdown-menu-dark { background-color:#002B5B; }
    .navbar .dropdown-item { color:#fff !important; font-weight:bold; white-space: normal; line-height: 1.3; }
    .navbar .dropdown-item:hover { background-color:#004080; }

    .carousel, .carousel-inner, .carousel-item { width:100%; }
    .carousel-item img { display:block; width:100%; height:auto; }

    .carousel-caption h3, .carousel-caption p {
      background: rgba(255, 255, 255, 0.4);
      color: #002B5B;
      font-weight: bold;
      display: inline-block;
      padding: 6px 12px;
      border-radius: 10px;
    }
    .carousel-caption p { border-radius: 8px; }
    

    .footer-custom { background-color: #002B5B; color: #ffffff; }
  </style>
</head>
<body>

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
            // Logika Dropdown Dinamis
            if(isset($mysqli)){
                $query_layanan = "SELECT id, nama_layanan, deskripsi FROM layanan WHERE status = 'Aktif' ORDER BY id ASC";
                $result_layanan = $mysqli->query($query_layanan);

                if ($result_layanan && $result_layanan->num_rows > 0) {
                    while ($row_layanan = $result_layanan->fetch_assoc()) {
                        $id_lay        = e($row_layanan['id']);
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

<div id="carouselExample" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
  <div class="carousel-inner">
    <?php foreach ($slides as $i => $slide): ?>
      <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
        <img src="<?= e($slide) ?>" class="d-block w-100" alt="Slide <?= $i+1 ?>" loading="lazy">
        <div class="carousel-caption d-none d-md-block">
          <h3>Selamat Datang Di Kecamatan Sidamanik</h3><br>
          <p>Kabupaten Simalungun</p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
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
  const el = document.querySelector('#carouselExample');
  if (el) {
    new bootstrap.Carousel(el, { interval: 3000, ride: 'carousel', pause: false, wrap: true });
  }
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
</script>
</body>
</html>