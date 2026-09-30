<?php
declare(strict_types=1);
require_once './Student.php';

session_start();

// 1. Generate CSRF Token jika belum ada di session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$successMessage = '';

// 2. Pemrosesan HTTP POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verifikasi CSRF Token
    $postToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $postToken)) {
        die('Kesalahan Keamanan: Token CSRF tidak cocok.');
    }

    // Mengambil dan mensanitasi input teks dasar
    $nim = trim($_POST['nim'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validasi NIM (Wajib angka dan panjang tepat 10 karakter)
    if (!preg_match('/^[0-9]{10}$/', $nim)) {
        $errors[] = 'NIM harus berupa angka sepanjang tepat 10 digit.';
    }

    // Validasi Nama (Tidak boleh kosong)
    if (empty($name)) {
        $errors[] = 'Nama lengkap mahasiswa tidak boleh kosong.';
    }

    // Validasi Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format alamat email tidak valid.';
    }

    // Jika tidak ada error, buat instansiasi objek dan simpan
    if (empty($errors)) {
        $student = new Student($nim, $name, $email);
        if ($student->saveToSession()) {
            $successMessage = 'Pendaftaran mahasiswa ' . htmlspecialchars($student->getName()) . ' berhasil disimpan!';
            // Regenerasi CSRF token setelah sukses untuk keamanan tambahan
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Mahasiswa Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body class="bg-light p-5">

    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="h4 mb-0">Formulir Pendaftaran Mahasiswa Baru</h1>
            </div>
            <div class="card-body">
                
                <!-- Tampilan Pesan Error jika Validasi Gagal -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Tampilan Pesan Sukses -->
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($successMessage) ?>
                    </div>
                <?php endif; ?>

                <form action="./register.php" method="POST">
                    <!-- CSRF Token Hidden Input -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                    <div class="mb-3">
                        <label for="nim" class="form-label">Nomor Induk Mahasiswa (NIM)</label>
                        <input type="text" class="form-control" id="nim" name="nim" required placeholder="Contoh: D121241001">
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Masukkan nama lengkap Anda">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="nama@mahasiswa.ac.id">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Daftar Sekarang</button>
                </form>

            </div>
        </div>

        <!-- Tampilan Hasil yang Terdaftar di Session -->
        <?php if (!empty($_SESSION['registered_students'])): ?>
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h2 class="h5 mb-0">Daftar Mahasiswa Terdaftar (Session)</h2>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($_SESSION['registered_students'] as $s): ?>
                            <li class="list-group-item">
                                <!-- Mencegah XSS saat merender data masukan -->
                                <strong><?= htmlspecialchars($s['nim']) ?></strong> - 
                                <?= htmlspecialchars($s['name']) ?> 
                                (<em><?= htmlspecialchars($s['email']) ?></em>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>