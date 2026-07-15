<?php
include 'config.php';

if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
  die('Koneksi database tidak tersedia. Periksa config.php Anda.');
}

if (method_exists($mysqli, 'set_charset')) {
  $mysqli->set_charset('utf8mb4');
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
    .navbar .dropdown-item { color:#fff !important; font-weight:bold; white-space: normal; line-height: 1.3; }
    .navbar .dropdown-item:hover { background-color:#004080; }

    .content {
      max-width: 1000px;
      margin: 40px auto;
      padding: 30px;
      background: #fff;
      border: 2px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; font-weight: bold; margin-bottom: 10px; }
    h6 { text-align: center; margin-bottom: 20px; }
    table { text-align: center; }

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
                            $nama_lay      = htmlspecialchars($row_layanan['nama_layanan'] ?? '', ENT_QUOTES, 'UTF-8'); 
                            $deskripsi_lay = htmlspecialchars($row_layanan['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8'); 

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
      <h2>Kelurahan</h2>
      <h6>Daftar Kelurahan di Kecamatan Sidamanik</h6>

      <table class="table table-bordered">
        <thead class="table-dark">
          <tr>
            <th style="width: 50px;">No</th>
            <th>Kelurahan</th>
            <th>Nama Lurah</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          $sql = "SELECT id_kelurahan, nm_kelurahan, nm_lurah FROM kelurahan ORDER BY id_kelurahan ASC";
          $result = $mysqli->query($sql);

          if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $nm_kelurahan = htmlspecialchars($row['nm_kelurahan'] ?? '', ENT_QUOTES, 'UTF-8');
              $nm_lurah     = htmlspecialchars($row['nm_lurah'] ?? '', ENT_QUOTES, 'UTF-8');

              echo "<tr>
                      <td>" . $no++ . "</td>
                      <td>{$nm_kelurahan}</td>
                      <td>{$nm_lurah}</td>
                    </tr>";
            }
            $result->free();
          } else {
            echo "<tr><td colspan='3'>Belum ada data kelurahan.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
</div> <footer class="footer-custom pt-4 pb-3">
  <div class="container text-center text-md-start">
    <div class="row">
      <div class="col-md-6 mb-3">
        <h5 class="fw-bold text-uppercase mb-3">Kantor Camat Sidamanik</h5>
        <p class="mb-1">📍 <strong>Alamat:</strong> <br> <?= htmlspecialchars($alamat, ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      
      <div class="col-md-6 mb-3 text-md-end">
        <h5 class="fw-bold text-uppercase mb-3">Jam Operasional</h5>
        <p class="mb-1">🕒 <?= htmlspecialchars($jam_op1, ENT_QUOTES, 'UTF-8') ?></p>
        <p class="mb-0">🕒 <?= htmlspecialchars($jam_op2, ENT_QUOTES, 'UTF-8') ?></p>
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
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
</script>
</body>
</html>