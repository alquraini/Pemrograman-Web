-- DDL Schema Database KRS Akademik ternormalisasi 3NF --

CREATE DATABASE akademik_db;
USE akademik_db;

-- 1. Tabel Dosen (3NF)
CREATE TABLE dosen (
    nidn VARCHAR(10) PRIMARY KEY,
    nama_dosen VARCHAR(100) NOT NULL
);

-- 2. Tabel Mahasiswa (3NF)
CREATE TABLE mahasiswa (
    nim VARCHAR(10) PRIMARY KEY,
    nama_mhs VARCHAR(100) NOT NULL,
    alamat_mhs TEXT
);

-- 3. Tabel Mata_Kuliah (3NF)
CREATE TABLE mata_kuliah (
    kode_mk VARCHAR(10) PRIMARY KEY,
    nama_mk VARCHAR(100) NOT NULL,
    sks INT NOT NULL,
    nidn VARCHAR(10),
    FOREIGN KEY (nidn) REFERENCES dosen(nidn) ON DELETE SET NULL
);

-- 4. Tabel KRS (3NF)
CREATE TABLE krs (
    nim VARCHAR(10),
    kode_mk VARCHAR(10),
    tanggal_ambil DATE NOT NULL,
    PRIMARY KEY (nim, kode_mk),
    FOREIGN KEY (nim) REFERENCES mahasiswa(nim) ON DELETE CASCADE,
    FOREIGN KEY (kode_mk) REFERENCES mata_kuliah(kode_mk) ON DELETE CASCADE
);