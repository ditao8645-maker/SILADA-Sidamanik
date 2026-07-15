<?php
include 'config.php';

$err = '';
$alert = ["type" => "", "msg" => ""];

$jam_op1 = 'Senin–Kamis: 08.00–16.00 WIB';
$jam_op2 = 'Jumat: 08.00–17.00 WIB';
$alamat  = 'Jl. OP. Nai Horsik Damanik No. 08 Sarimatondang';

function redirect_by_role($role) {
    switch ($role) {
        case 'user':   header('Location: Pengguna/dashboard_penduduk.php'); exit;
        case 'sekcam': header('Location: Admin/dashboard_sekcam.php'); exit;
        case 'camat':  header('Location: Camat/dashboard_camat.php'); exit;
        case 'desa':   header('Location: Desa/dashboard_desa.php'); exit;
        default:       header('Location: index.php'); exit;
    }
}

$list_nagori = [];
$q_nagori = $mysqli->query("SELECT id_nagori, nm_nagori FROM nagori ORDER BY nm_nagori ASC");
if ($q_nagori) {
    while ($n = $q_nagori->fetch_assoc()) {
        $list_nagori[] = $n;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') !== 'daftar') {
    $username = trim($_POST['username'] ?? '');
    $password_input = $_POST['password'] ?? ''; 

    if ($username === '' || $password_input === '') {
        $err = 'Username dan password wajib diisi.';
    } else {
        $stmt = $mysqli->prepare("SELECT id_akun, username, password, role, verifikasi, status FROM akun WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $acc = $res->fetch_assoc();
            $password_db = $acc['password'];
            $auth_ok = false;

            if (password_verify($password_input, $password_db) || $password_input === $password_db) {
                $auth_ok = true; 
            }

            if ($auth_ok) {
                if ($acc['status'] === 'nonaktif') {
                    $err = 'Akun Anda telah dinonaktifkan.';
                } elseif ($acc['verifikasi'] === 'pending') {
                    $err = 'Akun Anda belum divalidasi oleh admin.';
                } elseif ($acc['verifikasi'] === 'ditolak') {
                    $err = 'Mohon maaf, registrasi akun Anda ditolak.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['id_akun'] = $acc['id_akun'];
                    $_SESSION['role']    = $acc['role'];
                    
                    if ($acc['role'] === 'user') {
                        $p = $mysqli->prepare("SELECT nm_pengguna, nik FROM pengguna WHERE id_akun = ?");
                        $p->bind_param("i", $acc['id_akun']);
                        $p->execute();
                        $uData = $p->get_result()->fetch_assoc();
                        $_SESSION['nama'] = $uData['nm_pengguna'] ?? 'User';
                        $_SESSION['nik']  = $uData['nik'] ?? '';
                    } else {
                        $p = $mysqli->prepare("SELECT nama FROM petugas WHERE id_akun = ?");
                        $p->bind_param("i", $acc['id_akun']);
                        $p->execute();
                        $uData = $p->get_result()->fetch_assoc();
                        $_SESSION['nama'] = $uData['nama'] ?? 'Petugas';
                    }

                    redirect_by_role($acc['role']);
                }
            } else {
                $err = 'Password salah.';
            }
        } else {
            $err = 'Username tidak ditemukan.';
        }

        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'daftar') {
    $nik          = preg_replace('/\D/', '', $_POST["nik"] ?? "");
    $nama         = trim($_POST["nm_pengguna"] ?? "");
    $tgl_lahir    = trim($_POST["tgl_lahir"] ?? "");
    $no_hp        = trim($_POST["no_hp"] ?? "");
    $alamat_reg   = trim($_POST["alamat"] ?? "");
    $id_nagori    = trim($_POST["id_nagori"] ?? ""); 
    $username_reg = trim($_POST["username_reg"] ?? "");
    $password_reg = $_POST["password_reg"] ?? "";
    
    $errReg = [];

    if (strlen($nik) !== 16) {
        $errReg[] = "NIK harus 16 digit.";
    }
    
    if ($nama === "" || $tgl_lahir === "" || $no_hp === "" || $alamat_reg === "" || $username_reg === "" || $password_reg === "" || $id_nagori === "") {
        $errReg[] = "Semua field bertanda * wajib diisi, termasuk pilihan Nagori/Desa.";
    }
    
    $foto_ktp_path = null;

    if (isset($_FILES["foto_ktp"]) && $_FILES["foto_ktp"]["error"] === UPLOAD_ERR_OK) {
        $file_size = $_FILES["foto_ktp"]["size"];
        $max_size  = 2 * 1024 * 1024;
        $allowed   = [
            "image/jpeg" => "jpg",
            "image/png"  => "png",
            "image/jpg"  => "jpg"
        ];

        $mime = mime_content_type($_FILES["foto_ktp"]["tmp_name"]);

        if (!isset($allowed[$mime])) {
            $errReg[] = "Format foto KTP harus JPG atau PNG.";
        } elseif ($file_size > $max_size) {
            $errReg[] = "Ukuran foto KTP maksimal adalah 2MB.";
        } else {
            $dir = "uploads/foto_ktp";

            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $fname = "KTP_" . $nik . "_" . time() . "." . $allowed[$mime];

            if (move_uploaded_file($_FILES["foto_ktp"]["tmp_name"], $dir . "/" . $fname)) {
                $foto_ktp_path = $dir . "/" . $fname;
            } else { 
                $errReg[] = "Gagal mengunggah foto KTP."; 
            }
        }
    } else { 
        $errReg[] = "Foto KTP wajib diunggah."; 
    }

    if (!$errReg) {
        $cek = $mysqli->prepare("SELECT id_akun FROM akun WHERE username = ?");
        $cek->bind_param("s", $username_reg);
        $cek->execute();

        if ($cek->get_result()->num_rows > 0) {
            $errReg[] = "Username sudah digunakan.";
        }

        $cek->close();
    }

    if (!$errReg) {
        $cek_nik = $mysqli->prepare("SELECT nik FROM pengguna WHERE nik = ?");
        $cek_nik->bind_param("s", $nik);
        $cek_nik->execute();

        if ($cek_nik->get_result()->num_rows > 0) {
            $errReg[] = "NIK ini sudah terdaftar. Silakan login menggunakan akun yang ada.";
        }

        $cek_nik->close();
    }

    if (!$errReg) {
        $mysqli->begin_transaction();

        try {
            $pass_hash = password_hash($password_reg, PASSWORD_DEFAULT);

            $stmt1 = $mysqli->prepare("INSERT INTO akun (username, password, role, verifikasi, status) VALUES (?, ?, 'user', 'pending', 'aktif')");
            $stmt1->bind_param("ss", $username_reg, $pass_hash);
            $stmt1->execute();

            $id_akun_baru = $mysqli->insert_id;
            
            $default_photo = "uploads/photo/default.png";

            $stmt2 = $mysqli->prepare("INSERT INTO pengguna (nik, id_akun, nm_pengguna, tgl_lahir, alamat, no_hp, photo, foto_ktp, id_nagori) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("sissssssi", $nik, $id_akun_baru, $nama, $tgl_lahir, $alamat_reg, $no_hp, $default_photo, $foto_ktp_path, $id_nagori);
            $stmt2->execute();

            $mysqli->commit();

            echo "<script>
                    alert('Registrasi berhasil! Silakan tunggu validasi dari Admin Kecamatan.');
                    window.location='login.php';
                  </script>";
            exit;
        } catch (Exception $e) { 
            $mysqli->rollback(); 
            $alert = ["type" => "danger", "msg" => "Gagal menyimpan data registrasi."]; 
        }
    } else { 
        $alert = ["type" => "danger", "msg" => implode('<br>', $errReg)]; 
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>SILADA Sidamanik</title>
  <link rel="icon" type="image/png" href="gambar/logo_kecamatan.jpeg">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    body {
      background: #fff;
      margin: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .content-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .navbar {
      background-color: #002B5B;
    }

    .navbar a,
    .navbar-brand {
      color: #fff !important;
    }

    .navbar .nav-link {
      color: #fff !important;
      font-weight: bold;
    }

    .navbar .dropdown-menu-dark {
      background-color: #002B5B;
    }

    .navbar .dropdown-item {
      color: #fff !important;
      font-weight: bold;
      white-space: normal;
      line-height: 1.3;
    }

    .navbar .dropdown-item:hover {
      background-color: #004080;
    }

    .auth-wrap {
      position: relative;
      flex: 1;
      padding-top: 50px;
      padding-bottom: 50px;
    }

    .auth-wrap::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(rgba(255,255,255,.75), rgba(255,255,255,.75)), url('gambar/kantor.jpg') center/cover no-repeat;
      z-index: -1;
    }

    .login-card {
      max-width: 420px;
      width: 100%;
      border-radius: 12px;
      background: #fff;
      margin: 0 auto;
    }

    .office-logo img {
      width: 110px;
      height: 110px;
      display: block;
      margin: 0 auto 15px;
      object-fit: contain;
    }

    .required-label::after {
      content: " *";
      color: red;
    }

    .password-wrapper {
      position: relative;
    }

    .password-wrapper i {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
      z-index: 10;
    }

    .footer-custom {
      background-color: #002B5B;
      color: #ffffff;
      margin-top: auto;
    }

    .btn-register-link {
      border: none;
      background: transparent;
      color: #0d6efd;
      text-decoration: underline;
      cursor: pointer;
      font-weight: bold;
      padding: 0;
    }

    .btn-register-link:hover {
      color: #0a58ca;
    }
  </style>
</head>
<body>

<div class="content-wrapper">
  <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
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
              if (isset($mysqli)) {
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

          <li class="nav-item">
            <a class="nav-link" href="berita.php">Berita</a>
          </li>

          <li class="nav-item">
            <a class="nav-link active" href="login.php">Login</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="auth-wrap d-flex justify-content-center align-items-center">
    <div class="card shadow p-4 login-card">
      <div class="office-logo">
        <img src="gambar/logo_kecamatan.jpeg" alt="Logo">
      </div>

      <?php if ($err): ?>
        <div class="alert alert-danger text-center py-2" style="font-size:14px;">
          <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>
      
      <form method="post">
        <div class="mb-3">
          <label class="form-label fw-bold">Username / NIK</label>
          <input type="text" class="form-control" name="username" placeholder="Masukkan Username / NIK" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Password</label>
          <div class="password-wrapper">
            <input type="password" class="form-control" name="password" id="passLogin" placeholder="••••••" required>
            <i class="fa-solid fa-eye" onclick="togglePass('passLogin', this)"></i>
          </div>
        </div>

        <button type="submit" class="btn btn-dark w-100 fw-bold py-2">Login</button>
        
        <div class="d-flex justify-content-center align-items-center mt-3" style="font-size: 14px;">
          <span class="text-dark">Jika belum punya akun, silahkan&nbsp;</span>

          <button type="button" id="btnOpenRegister" class="btn-register-link">
            Registrasi
          </button>
        </div>
      </form>
    </div>
  </div>
</div> 

<footer class="footer-custom pt-4 pb-3">
  <div class="container text-center text-md-start">
    <div class="row">
      <div class="col-md-6 mb-3">
        <h5 class="fw-bold text-uppercase mb-3">Kantor Camat Sidamanik</h5>
        <p class="mb-1">
          📍 <strong>Alamat:</strong> <br>
          <?= htmlspecialchars($alamat, ENT_QUOTES, 'UTF-8') ?>
        </p>
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

<div class="modal fade" id="modalRegister" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="aksi" value="daftar">

        <div class="modal-header bg-light border-0">
          <h5 class="modal-title fw-bold text-primary">
            <i class="fa-solid fa-address-card me-2"></i>Registrasi Akun Warga
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">
          <?php if ($alert["msg"]): ?>
            <div class="alert alert-<?= htmlspecialchars($alert["type"], ENT_QUOTES, 'UTF-8') ?> mb-3">
              <?= $alert["msg"] ?>
            </div>
          <?php endif; ?>
          
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required-label fw-bold">NIK (16 Digit)</label>
              <input type="text" name="nik" maxlength="16" class="form-control" placeholder="Contoh: 120801..." required>
            </div>

            <div class="col-md-6">
              <label class="form-label required-label fw-bold">Nama Lengkap</label>
              <input type="text" name="nm_pengguna" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label required-label fw-bold">Tanggal Lahir</label>
              <input type="date" name="tgl_lahir" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label required-label fw-bold">No. HP</label>
              <input type="text" name="no_hp" class="form-control" required>
            </div>
            
            <div class="col-12">
              <label class="form-label required-label fw-bold">Asal Nagori / Desa</label>
              <select name="id_nagori" class="form-select border-primary shadow-sm" required>
                <option value="">-- Pilih Nagori Tempat Tinggal --</option>

                <?php foreach ($list_nagori as $n): ?>
                  <option value="<?= htmlspecialchars($n['id_nagori'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($n['nm_nagori'], ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label required-label fw-bold">Alamat Lengkap Sesuai KTP</label>
              <textarea name="alamat" class="form-control" rows="2" required></textarea>
            </div>
          </div>
          
          <div class="bg-light p-3 rounded mt-4 border">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label required-label fw-bold">Username</label>
                <input type="text" name="username_reg" class="form-control" required>
                <small class="text-primary fw-bold">* Gunakan NIK sebagai username.</small>
              </div>

              <div class="col-md-6">
                <label class="form-label required-label fw-bold">Password</label>
                <div class="password-wrapper">
                  <input type="password" name="password_reg" id="passReg" class="form-control" required>
                  <i class="fa-solid fa-eye" onclick="togglePass('passReg', this)"></i>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-4">
            <label class="form-label required-label fw-bold">Foto KTP Fisik</label>
            <input type="file" name="foto_ktp" class="form-control" accept=".jpg,.jpeg,.png" required>
            <small class="text-muted">* Format: JPG, JPEG, PNG. Maksimal ukuran: 2MB.</small>
          </div>
        </div>

        <div class="modal-footer bg-light border-0">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background:#002B5B; border:none;">
            Daftar Sekarang
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const btnRegister = document.getElementById('btnOpenRegister');
    const modalElement = document.getElementById('modalRegister');

    if (typeof bootstrap !== 'undefined') {
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
      });
    }

    if (btnRegister && modalElement) {
      btnRegister.addEventListener('click', function () {
        if (typeof bootstrap !== 'undefined') {
          const modalRegister = new bootstrap.Modal(modalElement);
          modalRegister.show();
        } else {
          alert('Bootstrap belum terbaca. Pastikan koneksi internet aktif atau gunakan file Bootstrap lokal.');
        }
      });
    }

    <?php if ($alert["msg"]): ?>
    if (modalElement && typeof bootstrap !== 'undefined') {
      const modalReg = new bootstrap.Modal(modalElement);
      modalReg.show();
    }
    <?php endif; ?>

    const inputFotoKtp = document.querySelector('input[name="foto_ktp"]');

    if (inputFotoKtp) {
      inputFotoKtp.addEventListener('change', function () {
        if (this.files[0] && this.files[0].size > 2 * 1024 * 1024) {
          alert("Maaf, ukuran file terlalu besar! Maksimal adalah 2MB.");
          this.value = "";
        }
      });
    }
  });

  function togglePass(inputId, iconEl) {
    const input = document.getElementById(inputId);

    if (!input) return;

    if (input.type === "password") {
      input.type = "text";
      iconEl.classList.remove('fa-eye');
      iconEl.classList.add('fa-eye-slash');
    } else {
      input.type = "password";
      iconEl.classList.remove('fa-eye-slash');
      iconEl.classList.add('fa-eye');
    }
  }
</script>

</body>
</html>