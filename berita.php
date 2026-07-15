<?php
require_once __DIR__ . '/config.php';

if (!function_exists('e')) {
    function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$jam_op1 = 'Senin–Kamis: 08.00–16.00 WIB';
$jam_op2 = 'Jumat: 08.00–17.00 WIB';
$alamat  = 'Jl. OP. Nai Horsik Damanik No. 08 Sarimatondang';

$featured_news = null;
$res_featured = $mysqli->query("SELECT * FROM berita WHERE status='Publish' AND is_featured='Ya' ORDER BY tanggal DESC LIMIT 1");
if ($res_featured && $res_featured->num_rows > 0) {
    $featured_news = $res_featured->fetch_assoc();
}

$where_not = ($featured_news) ? " AND id_berita != " . $featured_news['id_berita'] : "";
$res_news = $mysqli->query("SELECT * FROM berita WHERE status='Publish' $where_not ORDER BY tanggal DESC");

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>SILADA Sidamanik</title>
  <link rel="icon" type="image/png" href="gambar/logo_kecamatan.jpeg">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body { background-color: #f5f7fb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; display: flex; flex-direction: column; min-height: 100vh; }
    .content-wrapper { flex: 1; }

    .navbar { background-color:#002B5B; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .navbar a, .navbar-brand { color:#fff !important; }
    .navbar .nav-link { color:#fff !important; font-weight:bold; display: flex; align-items: center; }
    .navbar .dropdown-menu-dark { background-color:#002B5B; border: none; }
    .navbar .dropdown-item { 
      color:#fff !important; 
      font-weight:bold; 
      white-space: normal; 
      line-height: 1.3; 
    }
    .navbar .dropdown-item:hover { background-color:#004080; }

    .page-title { font-weight: 800; color: #002B5B; position: relative; display: inline-block; padding-bottom: 10px; margin-bottom: 30px; }
    .page-title::after { content: ''; position: absolute; left: 0; bottom: 0; width: 50px; height: 4px; background-color: #f39c12; border-radius: 2px; }

    .featured-card { border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.08); background: #fff; transition: 0.3s; }
    .featured-card:hover { transform: translateY(-5px); }
    .featured-img-wrap { position: relative; height: 100%; min-height: 350px; background: #eee; }
    .featured-img-wrap img { width: 100%; height: 100%; object-fit: cover; position: absolute; }
    .featured-badge { position: absolute; top: 15px; left: 15px; background: #e74c3c; color: white; padding: 6px 15px; border-radius: 20px; font-weight: bold; z-index: 2; font-size: 13px; }
    
    .news-card { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: all 0.3s ease; background: #fff; height: 100%; display: flex; flex-direction: column; }
    .news-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,43,91,0.1); }
    .news-img { height: 200px; width: 100%; object-fit: cover; background: #eee; }
    .news-title { font-size: 1.1rem; font-weight: 700; color: #2c3e50; text-decoration: none; margin-bottom: 10px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .news-title:hover { color: #002B5B; }

    .featured-excerpt, .news-excerpt {
        color: #64748b;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-align: justify;
        line-height: 1.6;
    }
    .featured-excerpt { margin-bottom: 1.5rem; }
    .news-excerpt { font-size: 0.875rem; margin-bottom: 1rem; }

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

            <li class="nav-item"><a class="nav-link active" href="berita.php">Berita</a></li>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5 mb-5">
        <h2 class="page-title">Kabar Sidamanik</h2>

        <?php if ($featured_news): ?>
        <div class="card featured-card mb-5">
            <div class="row g-0">
                <div class="col-lg-7">
                    <div class="featured-img-wrap">
                        <span class="featured-badge"><i class="fa-solid fa-fire me-1"></i> Sorotan Utama</span>
                        <?php if(!empty($featured_news['gambar'])): ?>
                            <img src="uploads/berita/<?= e($featured_news['gambar']) ?>" alt="Sorotan">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/800x500?text=Berita+Sidamanik" alt="No Image">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-5 d-flex align-items-center">
                    <div class="p-4 p-md-5">
                        <div class="text-primary fw-bold small mb-2 text-uppercase"><?= e($featured_news['kategori']) ?></div>
                        <h3 class="fw-bold text-dark mb-3"><?= e($featured_news['judul']) ?></h3>
                        
                        <div class="featured-excerpt">
                            <?= strip_tags(html_entity_decode($featured_news['konten'])) ?>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <span class="text-muted small"><i class="fa-regular fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($featured_news['tanggal'])) ?></span>
                            <a href="baca_berita.php?id=<?= $featured_news['id_berita'] ?>" class="btn btn-primary rounded-pill fw-bold px-4">Baca Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <h4 class="fw-bold mb-4 text-dark"><i class="fa-regular fa-newspaper text-primary me-2"></i>Berita Terbaru</h4>
        <div class="row g-4">
            <?php if ($res_news && $res_news->num_rows > 0): ?>
                <?php while($news = $res_news->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="news-card">
                        <?php if(!empty($news['gambar'])): ?>
                            <img src="uploads/berita/<?= e($news['gambar']) ?>" class="news-img" alt="Thumbnail">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/600x400?text=Berita" class="news-img">
                        <?php endif; ?>
                        
                        <div class="news-body p-4 flex-grow-1 d-flex flex-column">
                            <div class="text-primary fw-bold small mb-2 text-uppercase"><?= e($news['kategori']) ?></div>
                            <a href="baca_berita.php?id=<?= $news['id_berita'] ?>" class="news-title"><?= e($news['judul']) ?></a>
                            
                            <div class="news-excerpt">
                                <?= strip_tags(html_entity_decode($news['konten'])) ?>
                            </div>
                            
                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fa-regular fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($news['tanggal'])) ?></span>
                                <a href="baca_berita.php?id=<?= $news['id_berita'] ?>" class="text-primary text-decoration-none fw-bold small">Selengkapnya &raquo;</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <?php if(!$featured_news): ?>
                    <div class="col-12 text-center py-5 bg-white rounded shadow-sm border">
                        <i class="fa-regular fa-folder-open fa-3x mb-3 text-muted"></i>
                        <p class="text-muted">Belum ada berita yang dipublikasikan saat ini.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
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
  // Aktifkan tooltip untuk deskripsi layanan
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
</script>
</body>
</html>