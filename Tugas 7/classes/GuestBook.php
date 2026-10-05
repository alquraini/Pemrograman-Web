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

    /**
     * Ambil semua pesan, terbaru di atas (SELECT prepared).
    */
    public function ambilSemua(int $limit = 50): array
    {
        $sql = "SELECT id, nama, email, pesan, tanggal_kirim
                FROM buku_tamu
                ORDER BY tanggal_kirim DESC, id DESC
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}