<?php
declare(strict_types=1);

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