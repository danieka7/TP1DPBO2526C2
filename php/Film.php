<?php
/**
 * Class Film
 * -----------
 * Merepresentasikan data sebuah film yang tayang di bioskop, khusus untuk
 * versi web (PHP). Class ini berfungsi sebagai "data holder": menyimpan
 * atribut film beserta getter/setter-nya.
 *
 * Berbeda dari versi CLI (Java/C++/Python), versi web ini menambahkan
 * atribut $gambar yang menyimpan PATH FILE LOKAL (bukan URL), karena
 * sesuai ketentuan tugas dilarang menggunakan database — gambar disimpan
 * sebagai file fisik di folder "uploads/" pada server, dan yang disimpan
 * di objek Film hanyalah lokasi path-nya saja.
 */
class Film {
    // ==================== ATRIBUT ====================
    private string $id;          // Identifier unik, contoh: "F001"
    private string $judul;       // Judul film
    private string $genre;       // Genre film, contoh: "Action", "Horror"
    private int $durasi;         // Durasi film dalam satuan menit
    private string $sutradara;   // Nama sutradara film
    private float $hargaTiket;   // Harga tiket film dalam Rupiah
    private string $gambar;      // Path file lokal poster, contoh: "uploads/abc123_poster.jpg"

    /**
     * Constructor untuk membuat objek Film baru dengan seluruh atributnya.
     * Parameter $gambar boleh dikosongkan (default string kosong) apabila
     * user tidak mengunggah gambar saat menambah film.
     */
    public function __construct(
        string $id,
        string $judul,
        string $genre,
        int $durasi,
        string $sutradara,
        float $hargaTiket,
        string $gambar = ""
    ) {
        $this->id = $id;
        $this->judul = $judul;
        $this->genre = $genre;
        $this->durasi = $durasi;
        $this->sutradara = $sutradara;
        $this->hargaTiket = $hargaTiket;
        $this->gambar = $gambar;
    }

    // ==================== GETTER ====================
    // Getter digunakan untuk mengambil nilai tiap atribut dari luar class.
    public function getId(): string
    {
        return $this->id;
    }

    public function getJudul(): string
    {
        return $this->judul;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function getDurasi(): int
    {
        return $this->durasi;
    }

    public function getSutradara(): string
    {
        return $this->sutradara;
    }

    public function getHargaTiket(): float
    {
        return $this->hargaTiket;
    }

    public function getGambar(): string
    {
        return $this->gambar;
    }

    // ==================== SETTER ====================
    // Setter digunakan agar fitur "Update Data" bisa mengubah nilai atribut
    // objek Film yang sudah ada tanpa perlu membuat objek baru.
    public function setJudul(string $judul): void
    {
        $this->judul = $judul;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    public function setDurasi(int $durasi): void
    {
        $this->durasi = $durasi;
    }

    public function setSutradara(string $sutradara): void
    {
        $this->sutradara = $sutradara;
    }

    public function setHargaTiket(float $hargaTiket): void
    {
        $this->hargaTiket = $hargaTiket;
    }

    public function setGambar(string $gambar): void
    {
        $this->gambar = $gambar;
    }
}