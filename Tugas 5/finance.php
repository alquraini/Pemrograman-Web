<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/Transaction.php';

// Inisialisasi state sesi
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 0.0;
}
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

// Generate CSRF token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Validasi CSRF token
    $tokenFromForm = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $tokenFromForm)) {
        $errors[] = 'Token CSRF tidak valid. Silakan muat ulang halaman.';
    }

    // 2. Validasi jenis transaksi (whitelist, dicocokkan lewat match)
    $typeInput = $_POST['type'] ?? '';
    $type = match ($typeInput) {
        'deposit', 'withdrawal' => $typeInput,
        default => null,
    };
    if ($type === null) {
        $errors[] = 'Jenis transaksi tidak valid.';
    }

    // 3. Validasi jumlah: harus angka desimal positif
    $amountInput = $_POST['amount'] ?? '';
    if (!is_numeric($amountInput) || (float) $amountInput <= 0) {
        $errors[] = 'Jumlah transaksi harus berupa angka desimal positif.';
    } else {
        $amount = (float) $amountInput;
    }

        // 4. Jika lolos semua validasi, proses transaksi
    if (empty($errors)) {
        try {
            $id = bin2hex(random_bytes(8));
            $transaction = new Transaction($id, $type, $amount);

            $newBalance = $transaction->process((float) $_SESSION['balance']);

            $_SESSION['balance'] = $newBalance;
            $_SESSION['history'][] = $transaction->toArray();

            $success = 'Transaksi berhasil diproses.';

            // Regenerasi token CSRF setelah pemrosesan berhasil (mencegah replay)
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Mengambil data dari sesi untuk ditampilkan pada tampilan HTML
$csrfToken = $_SESSION['csrf_token'];
$balance = (float) $_SESSION['balance'];
$history = $_SESSION['history'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Sistem Manajemen Keuangan Sederhana</title>
<style>
    /* Styling tampilan antarmuka halaman web */
    body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 0 16px; }
    .balance { font-size: 1.4em; font-weight: bold; margin-bottom: 20px; }
    .error { color: #b00020; }
    .success { color: #1a7f37; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    form { margin-bottom: 16px; }
    label { display: block; margin-top: 8px; }
</style>
</head>
<body>

<h1>Sistem Manajemen Keuangan Sederhana</h1>

<!-- Menampilkan saldo terkini dengan format mata uang Rupiah -->
<div class="balance">
    Saldo saat ini: Rp <?= htmlspecialchars(number_format($balance, 2, ',', '.')) ?>
</div>

<?php foreach ($errors as $error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>

<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <label for="type">Jenis Transaksi</label>
    <select name="type" id="type" required>
        <option value="deposit">Deposit</option>
        <option value="withdrawal">Penarikan</option>
    </select>

    <label for="amount">Jumlah</label>
    <input type="number" step="0.01" min="0.01" name="amount" id="amount" required>

    <button type="submit" style="margin-top:12px;">Proses Transaksi</button>
</form>

<h2>Riwayat Transaksi</h2>
<?php if (empty($history)): ?>
    <p>Belum ada transaksi.</p>
<?php else: ?>
    <table>
        <thead>
            <tr><th>ID</th><th>Jenis</th><th>Jumlah</th></tr>
        </thead>
        <tbody>
        <?php foreach (array_reverse($history) as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['id']) ?></td>
                <td><?= htmlspecialchars($item['type']) ?></td>
                <td><?= htmlspecialchars(number_format((float) $item['amount'], 2, ',', '.')) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>