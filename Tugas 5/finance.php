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
}