<?php
/*
 * Alur program (berbasis parameter GET/POST "action"):
 * - action=tambah   (POST) -> menambah film baru
 * - action=update   (POST) -> mengubah data film berdasarkan ID
 * - action=hapus    (GET)  -> menghapus film berdasarkan ID
 * - action=edit     (GET)  -> menampilkan form edit (prefill) untuk ID tertentu
 * - action=cari     (GET)  -> mencari/memfilter film berdasarkan kata kunci
 * - action=batal    (GET)  -> membatalkan mode edit, kembali ke form tambah biasa
 * - (tanpa action)         -> tampilan default: form tambah + tabel semua film
 */

// PENTING: class Film harus sudah didefinisikan SEBELUM session_start(),
// karena PHP butuh definisi class ini untuk bisa meng-unserialize kembali
// objek Film yang tersimpan di dalam session dari request sebelumnya.
require_once 'Film.php';

session_start();

// ==================== INISIALISASI DATA SESSION ====================
// Error handling: cek dulu apakah session sudah pernah diisi sebelumnya,
// agar tidak muncul error "undefined array key" saat pertama kali dibuka.
if (!isset($_SESSION['daftarFilm'])) {
    // Data awal supaya halaman tidak kosong saat pertama dibuka.
    $_SESSION['daftarFilm'] = [
        new Film("F001", "The Dark Knight", "Action", 152, "Christopher Nolan", 45000, ""),
        new Film("F002", "Interstellar", "Sci-Fi", 169, "Christopher Nolan", 50000, ""),
    ];
}

// Variabel untuk menampung pesan sukses/error yang akan ditampilkan ke user.
$pesan = "";
$pesanError = "";

// Variabel untuk menampung ID yang sedang diedit (jika mode edit aktif).
// Jika kosong, form akan berfungsi sebagai form "Tambah Film".
$idSedangDiedit = "";

// Folder tempat menyimpan file gambar yang diunggah user.
$folderUpload = __DIR__ . '/uploads/';
if (!is_dir($folderUpload)) {
    mkdir($folderUpload, 0777, true);
}

// Ekstensi file gambar yang diizinkan (error handling upload gambar).
$ekstensiDiizinkan = ['jpg', 'jpeg', 'png', 'gif'];

/*
 * Fungsi bantu: mencari objek Film di dalam $_SESSION['daftarFilm']
 * berdasarkan ID. Mengembalikan null jika tidak ditemukan.
 * Dipakai untuk fitur Update, Hapus, dan pengecekan ID duplikat.
 */
function cariFilmById(string $id): ?Film{
    foreach ($_SESSION['daftarFilm'] as $film) {
        if (strtolower($film->getId()) === strtolower($id)) {
            return $film;
        }
    }
    return null;
}

/*
 * Fungsi bantu: memproses upload file gambar.
 * Mengembalikan path lokal (string) jika berhasil, atau string kosong
 * jika user tidak mengunggah file apa pun.
 * Melempar Exception jika file diunggah tapi tidak valid (error handling).
 */
function prosesUploadGambar(?array $fileGambar, string $folderUpload, array $ekstensiDiizinkan): string{
    // Jika user tidak memilih file sama sekali (field "gambar" bahkan tidak
    // terkirim di $_FILES, sehingga $fileGambar bernilai null), atau field
    // terkirim namun kosong, tidak dianggap error.
    if ($fileGambar === null || !isset($fileGambar['error']) || $fileGambar['error'] === UPLOAD_ERR_NO_FILE) {
        return "";
    }

    // Error handling: ada error lain saat upload (misal ukuran file kelewat besar)
    if ($fileGambar['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Gagal mengunggah gambar (kode error: {$fileGambar['error']}).");
    }

    // Error handling: validasi ekstensi file gambar
    $ekstensi = strtolower(pathinfo($fileGambar['name'], PATHINFO_EXTENSION));
    if (!in_array($ekstensi, $ekstensiDiizinkan, true)) {
        throw new Exception("Format gambar tidak didukung! Gunakan: " . implode(", ", $ekstensiDiizinkan));
    }

    // Error handling: validasi ukuran file maksimal 2MB
    $maksimalUkuran = 2 * 1024 * 1024; // 2MB
    if ($fileGambar['size'] > $maksimalUkuran) {
        throw new Exception("Ukuran gambar terlalu besar! Maksimal 2MB.");
    }

    // Membuat nama file unik agar tidak bentrok antar-upload.
    $namaFileBaru = uniqid('poster_', true) . '.' . $ekstensi;
    $tujuan = $folderUpload . $namaFileBaru;

    if (!move_uploaded_file($fileGambar['tmp_name'], $tujuan)) {
        throw new Exception("Gagal menyimpan file gambar ke server.");
    }

    // Path lokal yang disimpan ke atribut Film (relatif terhadap main.php).
    return 'uploads/' . $namaFileBaru;
}

// ==================== MENANGANI AKSI (ACTION) ====================
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
    if ($action === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // ---------- FITUR: TAMBAH DATA ----------
        $id = trim($_POST['id'] ?? '');
        $judul = trim($_POST['judul'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $durasiInput = trim($_POST['durasi'] ?? '');
        $sutradara = trim($_POST['sutradara'] ?? '');
        $hargaInput = trim($_POST['hargaTiket'] ?? '');

        // Error handling: validasi semua field wajib tidak boleh kosong
        if ($id === '' || $judul === '' || $genre === '' || $durasiInput === '' ||
            $sutradara === '' || $hargaInput === '') {
            throw new Exception("Semua field wajib diisi (kecuali gambar)!");
        }

        // Error handling: ID tidak boleh duplikat
        if (cariFilmById($id) !== null) {
            throw new Exception("ID '$id' sudah digunakan oleh film lain!");
        }

        // Error handling: durasi harus angka bulat positif
        if (!ctype_digit($durasiInput) || (int)$durasiInput <= 0) {
            throw new Exception("Durasi harus berupa angka bulat lebih dari 0!");
        }

        // Error handling: harga harus angka dan tidak boleh negatif
        if (!is_numeric($hargaInput) || (float)$hargaInput < 0) {
            throw new Exception("Harga tiket harus berupa angka dan tidak boleh negatif!");
        }

        // Proses upload gambar (opsional, boleh kosong)
        $pathGambar = prosesUploadGambar($_FILES['gambar'] ?? null, $folderUpload, $ekstensiDiizinkan);

        $filmBaru = new Film($id, $judul, $genre, (int)$durasiInput, $sutradara, (float)$hargaInput, $pathGambar);
        $_SESSION['daftarFilm'][] = $filmBaru;
        $pesan = "Film '$judul' berhasil ditambahkan!";

    } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // ---------- FITUR: UPDATE DATA ----------
        $id = trim($_POST['id'] ?? '');
        $film = cariFilmById($id);

        // Error handling: ID tidak ditemukan (misal data sudah terhapus di tab lain)
        if ($film === null) {
            throw new Exception("Film dengan ID '$id' tidak ditemukan!");
        }

        $judul = trim($_POST['judul'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $durasiInput = trim($_POST['durasi'] ?? '');
        $sutradara = trim($_POST['sutradara'] ?? '');
        $hargaInput = trim($_POST['hargaTiket'] ?? '');

        // Error handling: validasi field wajib tidak boleh kosong
        if ($judul === '' || $genre === '' || $durasiInput === '' ||
            $sutradara === '' || $hargaInput === '') {
            throw new Exception("Semua field wajib diisi (kecuali gambar)!");
        }

        // Error handling: durasi harus angka bulat positif
        if (!ctype_digit($durasiInput) || (int)$durasiInput <= 0) {
            throw new Exception("Durasi harus berupa angka bulat lebih dari 0!");
        }

        // Error handling: harga harus angka dan tidak boleh negatif
        if (!is_numeric($hargaInput) || (float)$hargaInput < 0) {
            throw new Exception("Harga tiket harus berupa angka dan tidak boleh negatif!");
        }

        // Jika user mengunggah gambar baru, ganti path gambar.
        // Jika tidak, path gambar lama tetap dipertahankan.
        $pathGambarBaru = prosesUploadGambar($_FILES['gambar'] ?? null, $folderUpload, $ekstensiDiizinkan);
        if ($pathGambarBaru !== "") {
            $film->setGambar($pathGambarBaru);
        }

        $film->setJudul($judul);
        $film->setGenre($genre);
        $film->setDurasi((int)$durasiInput);
        $film->setSutradara($sutradara);
        $film->setHargaTiket((float)$hargaInput);

        $pesan = "Film '$judul' berhasil diupdate!";

    } elseif ($action === 'hapus' && isset($_GET['id'])) {
        // ---------- FITUR: HAPUS DATA ----------
        $id = $_GET['id'];
        $film = cariFilmById($id);

        // Error handling: ID tidak ditemukan
        if ($film === null) {
            throw new Exception("Film dengan ID '$id' tidak ditemukan (mungkin sudah terhapus)!");
        }

        // Menghapus objek dari array berdasarkan ID, lalu re-main array
        // dengan array_values() agar tidak ada celah main setelah unset().
        $_SESSION['daftarFilm'] = array_values(array_filter(
            $_SESSION['daftarFilm'],
            fn(Film $f) => strtolower($f->getId()) !== strtolower($id)
        ));

        $pesan = "Film dengan ID '$id' berhasil dihapus!";

    } elseif ($action === 'edit' && isset($_GET['id'])) {
        // ---------- MODE: TAMPILKAN FORM EDIT (prefill) ----------
        $id = $_GET['id'];
        $film = cariFilmById($id);

        // Error handling: ID tidak ditemukan
        if ($film === null) {
            throw new Exception("Film dengan ID '$id' tidak ditemukan!");
        }
        $idSedangDiedit = $id;
    }
    // action=batal ditangani otomatis: karena $idSedangDiedit tetap "",
    // halaman akan kembali menampilkan form Tambah Film seperti biasa.

} catch (Exception $e) {
    // Menangkap seluruh error dari blok di atas (validasi, upload gambar, dll)
    // lalu ditampilkan sebagai pesan error ke user tanpa menghentikan program.
    $pesanError = $e->getMessage();
}

// Kata kunci pencarian (fitur Cari Data). Menggunakan GET agar bisa
// langsung dibagikan lewat URL. htmlspecialchars mencegah XSS.
$kataKunciCari = trim($_GET['keyword'] ?? '');

// Menyiapkan data film yang tersimpan di objek $filmUntukEdit (jika mode edit aktif),
// untuk mengisi (prefill) form secara otomatis.
$filmUntukEdit = $idSedangDiedit !== "" ? cariFilmById($idSedangDiedit) : null;

/*
 * Fungsi bantu untuk menampilkan data dengan aman ke HTML (mencegah XSS).
 * Semua data dari user WAJIB melalui fungsi ini sebelum dicetak ke halaman.
 */
function aman(string $teks): string
{
    return htmlspecialchars($teks, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bioskop Sinefil ABiez</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 30px auto;
            background: #f4f4f4;
            color: #222;
        }

        h1 {
            text-align: center;
            color: #1a1a2e;
        }

        .kotak {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type=text],
        input[type=number],
        input[type=file] {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button,
        .tombol {
            margin-top: 15px;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-simpan {
            background: #2e7d32;
            color: #fff;
        }

        .btn-batal {
            background: #757575;
            color: #fff;
        }

        .btn-edit {
            background: #1565c0;
            color: #fff;
            padding: 5px 10px;
            font-size: 13px;
        }

        .btn-hapus {
            background: #c62828;
            color: #fff;
            padding: 5px 10px;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 14px;
            vertical-align: middle;
        }

        th {
            background: #1a1a2e;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #fafafa;
        }

        img.poster {
            width: 60px;
            height: 85px;
            object-fit: cover;
            border-radius: 4px;
        }

        .pesan-sukses {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .pesan-error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .form-cari {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }

        .form-cari input {
            flex: 1;
        }

        .kosong {
            text-align: center;
            color: #888;
            padding: 15px;
        }
    </style>
</head>

<body>

<h1>🎬 BIOSKOP SINEFIL ABIEZ 🎬</h1>

<?php if ($pesan !== ""): ?>
    <div class="pesan-sukses"><?= aman($pesan) ?></div>
<?php endif; ?>

<?php if ($pesanError !== ""): ?>
    <div class="pesan-error"><?= aman($pesanError) ?></div>
<?php endif; ?>

<!-- ==================== FORM TAMBAH / EDIT FILM ==================== -->
<div class="kotak">
    <h2><?= $filmUntukEdit ? "Edit Film: " . aman($filmUntukEdit->getJudul()) : "Tambah Film Baru" ?></h2>

    <!-- enctype multipart/form-data WAJIB ada agar upload file (gambar) berfungsi -->
    <form method="POST"
          action="main.php?action=<?= $filmUntukEdit ? 'update' : 'tambah' ?>"
          enctype="multipart/form-data">

        <label for="id">ID Film</label>
        <input type="text" id="id" name="id"
               value="<?= $filmUntukEdit ? aman($filmUntukEdit->getId()) : '' ?>"
               <?= $filmUntukEdit ? 'readonly' : 'required' ?>>
        <!-- ID dikunci (readonly) saat mode edit karena ID adalah identifier unik -->

        <label for="judul">Judul Film</label>
        <input type="text" id="judul" name="judul" required
               value="<?= $filmUntukEdit ? aman($filmUntukEdit->getJudul()) : '' ?>">

        <label for="genre">Genre</label>
        <input type="text" id="genre" name="genre" required
               value="<?= $filmUntukEdit ? aman($filmUntukEdit->getGenre()) : '' ?>">

        <label for="durasi">Durasi (menit)</label>
        <input type="number" id="durasi" name="durasi" min="1" required
               value="<?= $filmUntukEdit ? aman((string)$filmUntukEdit->getDurasi()) : '' ?>">

        <label for="sutradara">Sutradara</label>
        <input type="text" id="sutradara" name="sutradara" required
               value="<?= $filmUntukEdit ? aman($filmUntukEdit->getSutradara()) : '' ?>">

        <label for="hargaTiket">Harga Tiket (Rp)</label>
        <input type="number" id="hargaTiket" name="hargaTiket" min="0" step="0.01" required
               value="<?= $filmUntukEdit ? aman((string)$filmUntukEdit->getHargaTiket()) : '' ?>">

        <label for="gambar">
            Gambar Poster (jpg/jpeg/png/gif, maks 2MB)
            <?= $filmUntukEdit ? '— kosongkan jika tidak ingin mengganti gambar' : '(opsional)' ?>
        </label>
        <input type="file" id="gambar" name="gambar" accept=".jpg,.jpeg,.png,.gif">

        <?php if ($filmUntukEdit): ?>
            <input type="hidden" name="id" value="<?= aman($filmUntukEdit->getId()) ?>">
            <button type="submit" class="tombol btn-simpan">Simpan Perubahan</button>
            <!-- Fitur batal: kembali ke main.php tanpa action, sehingga mode edit hilang -->
            <a href="main.php" class="tombol btn-batal">Batal</a>
        <?php else: ?>
            <button type="submit" class="tombol btn-simpan">Tambah Film</button>
        <?php endif; ?>
    </form>
</div>

<!-- ==================== FORM CARI FILM ==================== -->
<div class="kotak">
    <h2>Cari Film</h2>
    <form method="GET" action="main.php" class="form-cari">
        <input type="hidden" name="action" value="cari">
        <input type="text" name="keyword" placeholder="Cari berdasarkan ID atau Judul..."
               value="<?= aman($kataKunciCari) ?>">
        <button type="submit" class="tombol btn-simpan">Cari</button>
        <?php if ($kataKunciCari !== ""): ?>
            <a href="main.php" class="tombol btn-batal">Reset</a>
        <?php endif; ?>
    </form>
</div>

<!-- ==================== TABEL DAFTAR FILM ==================== -->
<div class="kotak">
    <h2>Daftar Film</h2>

    <?php
    // Menyaring daftar film berdasarkan kata kunci pencarian (jika ada).
    // Pencarian dilakukan pada ID dan Judul, tidak case-sensitive.
    $daftarUntukDitampilkan = $_SESSION['daftarFilm'];
    if ($kataKunciCari !== "") {
        $daftarUntukDitampilkan = array_filter($daftarUntukDitampilkan, function (Film $f) use ($kataKunciCari) {
            $kunci = strtolower($kataKunciCari);
            return str_contains(strtolower($f->getId()), $kunci) ||
                   str_contains(strtolower($f->getJudul()), $kunci);
        });
    }
    ?>

    <?php if (empty($daftarUntukDitampilkan)): ?>
        <!-- Error handling: tampilkan pesan yang sesuai jika data kosong -->
        <p class="kosong">
            <?= $kataKunciCari !== ""
                ? "Tidak ada film yang cocok dengan kata kunci '" . aman($kataKunciCari) . "'."
                : "Belum ada data film." ?>
        </p>
    <?php else: ?>
        <table>
            <tr>
                <th>Gambar</th>
                <th>ID</th>
                <th>Judul</th>
                <th>Genre</th>
                <th>Durasi</th>
                <th>Sutradara</th>
                <th>Harga Tiket</th>
                <th>Aksi</th>
            </tr>
            <?php foreach ($daftarUntukDitampilkan as $film): ?>
                <tr>
                    <td>
                        <?php if ($film->getGambar() !== "" && file_exists($film->getGambar())): ?>
                            <img class="poster" src="<?= aman($film->getGambar()) ?>" alt="Poster <?= aman($film->getJudul()) ?>">
                        <?php else: ?>
                            <em>Tidak ada gambar</em>
                        <?php endif; ?>
                    </td>
                    <td><?= aman($film->getId()) ?></td>
                    <td><?= aman($film->getJudul()) ?></td>
                    <td><?= aman($film->getGenre()) ?></td>
                    <td><?= $film->getDurasi() ?> menit</td>
                    <td><?= aman($film->getSutradara()) ?></td>
                    <td>Rp<?= number_format($film->getHargaTiket(), 0, ',', '.') ?></td>
                    <td>
                        <a href="main.php?action=edit&id=<?= urlencode($film->getId()) ?>" class="tombol btn-edit">Edit</a>
                        <a href="main.php?action=hapus&id=<?= urlencode($film->getId()) ?>"
                           class="tombol btn-hapus"
                           onclick="return confirm('Yakin ingin menghapus film \'<?= aman($film->getJudul()) ?>\'?');">
                           Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>