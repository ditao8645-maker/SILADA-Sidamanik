<?php
// validasi.php (Simpan di folder utama /sidamanik)
require_once __DIR__ . '/config.php';

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function tgl_indo($tanggal) {
    if (!$tanggal || $tanggal === '0000-00-00' || $tanggal === '0000-00-00 00:00:00') return '-';
    $time = strtotime($tanggal);
    if (!$time) return '-';
    $bulan = [ 1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember' ];
    return date('d', $time) . ' ' . $bulan[(int)date('m', $time)] . ' ' . date('Y', $time);
}

$id_pengajuan = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$valid = false;
$data = null;

if ($id_pengajuan > 0) {
    // Ambil data surat, pastikan statusnya Selesai (sudah divalidasi Camat)
    $stmt = $mysqli->prepare("
        SELECT p.nomor_surat, p.tanggal_validasi, l.nama_layanan, pg.nm_pengguna 
        FROM pengajuan p
        JOIN layanan l ON p.id_layanan = l.id
        LEFT JOIN pengguna pg ON p.id_akun = pg.id_akun
        WHERE p.id_pengajuan = ? AND p.status_pengajuan = 'Selesai'
        LIMIT 1
    ");
    $stmt->bind_param("i", $id_pengajuan);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $valid = true;
        $data = $result->fetch_assoc();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Dokumen - SILADA Sidamanik</title>
    <link rel="icon" type="image/png" href="gambar/logo_kecamatan.jpeg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Roboto, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .validasi-card { background: #fff; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); width: 100%; max-width: 480px; padding: 40px 30px; text-align: center; border-top: 6px solid #002B5B; }
        .logo-img { width: 80px; margin-bottom: 20px; }
        .icon-status { font-size: 70px; margin-bottom: 15px; }
        .text-success-custom { color: #10b981; }
        .text-danger-custom { color: #ef4444; }
        .table-info-surat { text-align: left; margin-top: 30px; font-size: 15px; width: 100%; }
        .table-info-surat th { color: #64748b; font-weight: 600; padding: 10px 0; border-bottom: 1px solid #f1f5f9; width: 40%; }
        .table-info-surat td { color: #0f172a; font-weight: 700; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .watermark { font-size: 12px; color: #94a3b8; margin-top: 30px; }
    </style>
</head>
<body>

    <div class="validasi-card">
        <img src="gambar/logo_kecamatan.jpeg" alt="Logo Simalungun" class="logo-img">
        <h5 class="fw-bold text-dark mb-1">Pemerintah Kabupaten Simalungun</h5>
        <p class="text-muted small mb-4">Kecamatan Sidamanik</p>

        <?php if ($valid): ?>
            <i class="fa-solid fa-circle-check icon-status text-success-custom"></i>
            <h4 class="fw-bold text-success-custom mb-2">Dokumen Valid</h4>
            <div class="badge bg-success bg-opacity-10 text-success-custom px-3 py-2 rounded-pill mb-2">
                <i class="fa-solid fa-shield-halved me-1"></i> Asli & Sah
            </div>
            <p class="text-muted small px-3">
                Dokumen ini dinyatakan <strong>ASLI</strong> dan ditandatangani secara elektronik oleh Camat Sidamanik.
            </p>

            <table class="table-info-surat">
                <tr>
                    <th>Nomor Surat</th>
                    <td><?= e($data['nomor_surat'] ?: 'Menunggu Nomor') ?></td>
                </tr>
                <tr>
                    <th>Jenis Layanan</th>
                    <td><?= e($data['nama_layanan']) ?></td>
                </tr>
                <tr>
                    <th>Tanggal Terbit</th>
                    <td><?= e(tgl_indo($data['tanggal_validasi'])) ?></td>
                </tr>
                <tr>
                    <th>Pemohon</th>
                    <td><?= e($data['nm_pengguna'] ?: 'Warga / Ahli Waris') ?></td>
                </tr>
            </table>

        <?php else: ?>
            <i class="fa-solid fa-circle-xmark icon-status text-danger-custom"></i>
            <h4 class="fw-bold text-danger-custom mb-2">Dokumen Tidak Valid</h4>
            <div class="badge bg-danger bg-opacity-10 text-danger-custom px-3 py-2 rounded-pill mb-2">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> Tidak Ditemukan
            </div>
            <p class="text-muted small px-3 mt-3">
                Maaf, data dokumen ini tidak ditemukan di dalam sistem SILADA Sidamanik atau surat belum selesai divalidasi. <strong>Harap waspada terhadap pemalsuan dokumen!</strong>
            </p>
        <?php endif; ?>

        <div class="watermark">
            <i class="fa-solid fa-lock me-1"></i> Sistem Informasi Layanan Administrasi (SILADA)
        </div>
    </div>

</body>
</html>
