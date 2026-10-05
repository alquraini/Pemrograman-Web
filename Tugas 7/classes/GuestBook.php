<?php
declare(strict_types=1);

class GuestBook
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Simpan pesan memakai prepared statement (INSERT).
     */
    public function simpan(string $nama, string $email, string $pesan): bool
    {
        $sql = "INSERT INTO buku_tamu (nama, email, pesan)
                VALUES (:nama, :email, :pesan)";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':nama'  => $nama,
            ':email' => $email,
            ':pesan' => $pesan,
        ]);
    }
}