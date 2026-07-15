<?php
require_once 'config.php';

if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    die('Koneksi database ($mysqli) tidak tersedia. Periksa config.php Anda.');
}

if (method_exists($mysqli, 'set_charset')) {
    $mysqli->set_charset('utf8mb4');
}

if (!function_exists('e')) {
    function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$jam_op1 = 'Senin–Kamis: 08.00–16.00 WIB';
$jam_op2 = 'Jumat: 08.00–17.00 WIB';
$alamat  = 'Jl. OP. Nai Horsik Damanik No. 08 Sarimatondang';

function resolve_web_photo(string $raw): string {
    $raw = trim($raw);
    if ($raw === '') return 'assets/uploads/no-photo.png';

    $val = str_replace('\\', '/', $raw);
    if (preg_match('~^https?://~i', $val)) return $val;
    if (str_starts_with($val, '/')) return $val;

    $projectRoot = rtrim(str_replace('\\','/', realpath(__DIR__)), '/');

    if (preg_match('~^[a-zA-Z]:/|^/~', $val)) {
        $fsNorm   = strtolower($val);
        $rootNorm = strtolower($projectRoot);
        if (str_starts_with($fsNorm, $rootNorm)) {
            $val = ltrim(substr($val, strlen($projectRoot)), '/');
        } else {
            $val = '';
        }
    }

    $val = preg_replace('~^(\./|\../)+~', '', $val);
    $candidates = [];
    if ($val !== '') $candidates[] = $val;

    if ($val !== '' && !str_contains($val, '/')) {
        $candidates[] = 'uploads/perangkat/' . $val;
        $candidates[] = 'assets/uploads/' . $val;
        $candidates[] = 'Admin/uploads/perangkat/' . $val;
    } else {
        if (!preg_match('~^(uploads/|assets/|Admin/uploads/)~', $val)) {
            $candidates[] = 'uploads/perangkat/' . ltrim($val, '/');
        }
        $candidates[] = 'assets/uploads/' . basename($val);
        if (!str_starts_with($val, 'Admin/')) {
            $candidates[] = 'Admin/uploads/perangkat/' . basename($val);
        }
    }

    foreach ($candidates as $cand) {
        $fs = $projectRoot . '/' . ltrim($cand, '/');
        if (is_file($fs)) return $cand;
    }

    if ($val !== '') return $val;
    return 'assets/uploads/no-photo.png';
}
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

    .content {
      max-width: 1000px;
      margin: 40px auto;
      padding: 30px;
      background: #fff;
      border: 2px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .content h2 { text-align:center; margin-bottom:25px; font-weight:bold; }
    .content h4 { margin-top:30px; font-weight:bold; }

    .struktur-img {
      display:block;
      margin:0 auto 20px;
      max-width:950px;
      width:100%;
      height:auto;
    }

    .foto-perangkat img {
      width:80px; height:100px; object-fit:cover;
      border:1px solid #ccc; border-radius:5px;
    }

    /* === Style khusus Footer === */
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
      <h2>Struktur Organisasi</h2>
      <img src="gambar/struktur.png" alt="Struktur Organisasi" class="struktur-img">

      <h4>Daftar Nama Perangkat Kecamatan Sidamanik</h4>

      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0 text-center">
          <thead class="table-dark">
            <tr>
              <th style="width:60px;">No</th>
              <th>Foto</th>
              <th>Nama</th>
              <th>Jabatan</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no   = 1;
            $sql  = "SELECT id_perangkat, photo, nama, jabatan FROM perangkat_camat ORDER BY id_perangkat ASC";
            $res  = $mysqli->query($sql);

            if ($res && $res->num_rows > 0):
              while ($row = $res->fetch_assoc()):
                $nama = e($row['nama']);
                $jab  = e($row['jabatan']);
                $fotoWeb = resolve_web_photo((string)($row['photo'] ?? ''));
            ?>
              <tr>
                <td><?php echo $no++; ?></td>
                <td class="foto-perangkat">
                  <img src="<?php echo e($fotoWeb); ?>?v=<?php echo time(); ?>"
                       alt="Foto <?php echo $nama; ?>"
                       onerror="this.onerror=null;this.src='assets/uploads/no-photo.png';">
                </td>
                <td><?php echo $nama; ?></td>
                <td><?php echo $jab; ?></td>
              </tr>
            <?php
              endwhile;
              $res->free();
            else:
              echo "<tr><td colspan='4'><b>" . (!$res ? "Terjadi kesalahan database." : "Belum ada data perangkat.") . "</b></td></tr>";
            endif;
            ?>
          </tbody>
        </table>
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