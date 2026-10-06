<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/classes/GuestBook.php';

/* Koneksi PDO */
$dsn  = 'mysql:host=localhost;dbname=perpustakaan;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Koneksi database gagal.');
}

$guestbook = new GuestBook($pdo);

/* Helper sanitasi keluaran */
function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* Token CSRF */

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$old = ['nama' => '', 'email' => '', 'pesan' => ''];

/* Proses form */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Token CSRF tidak valid.');
    }

    $old['nama']  = trim((string)($_POST['nama']  ?? ''));
    $old['email'] = trim((string)($_POST['email'] ?? ''));
    $old['pesan'] = trim((string)($_POST['pesan'] ?? ''));

    $errors = $guestbook->validasi($old['nama'], $old['email'], $old['pesan']);

    if (empty($errors)) {
        try {
            $guestbook->simpan($old['nama'], $old['email'], $old['pesan']);
            $old = ['nama' => '', 'email' => '', 'pesan' => ''];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errors['umum'] = 'Terjadi kesalahan saat menyimpan pesan.';
        }
    }
}

$daftarPesan = $guestbook->ambilSemua();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buku Tamu Perpustakaan</title>
    <style>
        :root {
            --biru: #05599d;
            --garis: #8a8a8a;
            --input: #e4e2e2;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            color: #000;
            background: #fff;
        }

        /* Header */
        .header {
            background: var(--biru);
            color: #fff;
            text-align: center;
            padding: 28px 16px;
        }
        .header h1 { margin: 0 0 6px; font-size: 1.35rem; }
        .header p  { margin: 0; font-size: 0.9rem; }

        .container { max-width: 700px; margin: 30px auto; padding: 0 16px; }

        /* Kartu form */
        .card {
            border: 1px solid var(--garis);
            border-radius: 14px;
            padding: 16px 16px 18px;
        }
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 36px;
            margin-bottom: 30px;
        }
        .card label {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0 0 10px 2px;
        }
        .card input,
        .card textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--garis);
            border-radius: 5px;
            background: var(--input);
            font: inherit;
            font-size: 0.9rem;
        }
        .card textarea { height: 100px; resize: vertical; }
        .card input::placeholder,
        .card textarea::placeholder { color: #8c8c8c; }
        .card input:focus,
        .card textarea:focus { outline: 2px solid var(--biru); background: #fff; }

        .btn-wrap { text-align: center; margin-top: 14px; }
        .btn {
            background: var(--biru);
            color: #fff;
            border: 0;
            border-radius: 999px;
            padding: 10px 52px;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }
        .btn:hover { filter: brightness(1.12); }

        .error { display: block; color: #b00020; font-size: 0.82rem; margin-top: 6px; }
        .alert { background: #fdecea; color: #b00020; padding: 10px 14px; border-radius: 8px; margin-bottom: 20px; }

        /* Tabel */
        h2.judul { font-size: 0.95rem; margin: 36px 0 12px 2px; }
        .tabel-wrap {
            border: 1px solid var(--garis);
            border-radius: 14px;
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
        th, td { padding: 10px 12px; vertical-align: top; text-align: left; }
        th { text-align: center; font-weight: 500; border-bottom: 1px solid var(--garis); }
        th + th, td + td { border-left: 1px solid var(--garis); }
        tbody tr + tr td { border-top: 1px solid #ddd; }
        td.no, td.tgl { text-align: center; white-space: nowrap; }
        td.kosong { text-align: center; color: #666; padding: 28px 12px; }

        @media (max-width: 640px) {
            .row { grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;}
            .tabel-wrap { overflow-x: auto; }
            table { min-width: 560px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Buku Tamu Perpustakaan</h1>
        <p>Tinggalkan jejak kunjungan Anda dengan mengisi form di bawah ini.</p>
    </div>

    <div class="container">
        <?php if (isset($errors['umum'])): ?>
            <div class="alert"><?= e($errors['umum']) ?></div>
        <?php endif; ?>

        <form method="post" action="guestbook.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

            <div class="row">
                <div class="card">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" placeholder="Masukkan nama"
                           value="<?= e($old['nama']) ?>">
                    <?php if (isset($errors['nama'])): ?>
                        <span class="error"><?= e($errors['nama']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan alamat email"
                           value="<?= e($old['email']) ?>">
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?= e($errors['email']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <label for="pesan">Pesan</label>
                <textarea id="pesan" name="pesan"
                          placeholder="Masukkan kesan dan pesan"><?= e($old['pesan']) ?></textarea>
                <?php if (isset($errors['pesan'])): ?>
                    <span class="error"><?= e($errors['pesan']) ?></span>
                <?php endif; ?>

                <div class="btn-wrap">
                    <button type="submit" class="btn">Kirim</button>
                </div>
            </div>
        </form>

        <h2 class="judul">Daftar Pesan</h2>
        <div class="tabel-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Pesan</th>
                        <th>Tanggal Kirim</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($daftarPesan)): ?>
                    <tr><td colspan="5" class="kosong">Belum ada pesan.</td></tr>
                <?php else: ?>
                    <?php foreach ($daftarPesan as $i => $row): ?>
                        <tr>
                            <td class="no"><?= $i + 1 ?></td>
                            <td><?= e($row['nama']) ?></td>
                            <td><?= e($row['email']) ?></td>
                            <td><?= nl2br(e($row['pesan'])) ?></td>
                            <td class="tgl"><?= e($row['tanggal_kirim']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>