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
    body { background:#fff; margin:0; display: flex; flex-direction: column; min-height: 100vh; }
    .content-wrapper { flex: 1; }

    .navbar { background-color:#002B5B; }
    .navbar a, .navbar-brand { color:#fff !important; }
    .navbar .nav-link { color:#fff !important; font-weight:bold; }
    .navbar .dropdown-menu-dark { background-color:#002B5B; }
    .navbar .dropdown-item { 
      color:#fff !important; 
      font-weight:bold; 
      white-space: normal; 
      line-height:1.3; 
    }
    .navbar .dropdown-item:hover { background-color:#004080; }

    .profile-container { 
      max-width: 900px; 
      margin: 40px auto; 
      text-align: center; 
      padding: 0 15px;
    }
    .profile-container img { 
      max-width: 500px; 
      width: 100%; 
      height: auto; 
      margin: 20px 0; 
      border-radius: 8px; 
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .profile-container p { 
      text-align: justify; 
      line-height: 1.7; 
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

            <li class="nav-item">
              <a class="nav-link" href="index.php">🏠 Home</a>
            </li>

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

    <div class="profile-container">
      <h2 class="fw-bold">Profil Kantor Camat Sidamanik</h2>

      <img src="gambar/kantor.jpg" alt="Foto Kantor Camat">

      <p>
        Kantor Camat Sidamanik merupakan pusat pelayanan dan administrasi pemerintahan Kecamatan Sidamanik, Kabupaten Simalungun. 
        Berlokasi strategis di Jalan OP. Nai Horsik Damanik No. 08 Sarimatondang, Kecamatan Sidamanik, Kabupaten Simalungun, Sumatera Utara, instansi ini menjadi garda terdepan dalam melayani kebutuhan masyarakat. 
        Kami berkomitmen untuk menyediakan layanan publik yang profesional dan transparan, seperti pengurusan Surat Keterangan Domisili (Penduduk, Usaha, maupun Lembaga), 
        Surat Keterangan Tidak Mampu (SKTM), hingga administrasi ahli waris dan keterangan penghasilan.
      </p>
      <p>
        Pusat pemerintahan Kecamatan Sidamanik saat ini berkedudukan di Sarimatondang, yang secara historis memiliki latar belakang yang sangat kaya. 
        Nama Sarimatondang sendiri berasal dari bahasa Simalungun "Sarima Tondong" yang berarti mencari kerabat. 
        Wilayah yang subur ini dulunya merupakan bagian dari Partuanon Sidamanik di bawah Kerajaan Siantar. 
        Menariknya, pada masa penjajahan Belanda, kawasan ini juga sempat dikenal dengan istilah Landbow atau "Kaddang Lobbu" karena potensinya yang besar di sektor pertanian dan peternakan.
      </p>
      <p>
        Keunikan lain dari Sidamanik adalah harmonisasi budayanya yang sangat indah. Selain masyarakat Batak Simalungun dan Batak Toba sebagai perintis, 
        wilayah ini juga dihuni oleh berbagai etnis lain, terutama suku Jawa. Keberagaman ini berawal dari sejarah panjang pengelolaan perkebunan teh Sidamanik di masa lampau yang membawa semangat gotong royong hingga saat ini. 
        Keberagaman inilah yang menjadi identitas sekaligus kekuatan utama bagi kemajuan Kecamatan Sidamanik.
      </p>

      <h4 class="fw-bold mt-5">Lokasi Kantor Camat Sidamanik</h4>
      <div class="ratio ratio-16x9 mt-3 mb-5">
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.5828691316417!2d98.94825637404495!3d2.935272354432103!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303196d71e4775d5%3A0x4ed64e2dd0f8f4f9!2sKantor%20Camat%20Sidamanik!5e0!3m2!1sid!2sid!4v1711588000000!5m2!1sid!2sid" 
          style="border:0;" 
          allowfullscreen="" 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
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