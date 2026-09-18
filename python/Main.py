from typing import Optional

from Film import Film
from OperasiBatal import OperasiDibatalkanException

# Kata kunci untuk membatalkan operasi yang sedang berjalan.
KATA_BATAL = "batal"

# List untuk menyimpan seluruh objek Film yang sudah ditambahkan.
daftar_film: list[Film] = []


def main() -> None:
    """Entry point program: mengisi data awal lalu menjalankan loop menu."""
    # Data awal (opsional) supaya program tidak kosong saat pertama dijalankan.
    daftar_film.append(Film("F001", "The Dark Knight", "Action", 152, "Christopher Nolan", 45000))
    daftar_film.append(Film("F002", "Interstellar", "Sci-Fi", 169, "Christopher Nolan", 50000))

    berjalan = True

    # ==================== LOOP MENU UTAMA ====================
    while berjalan:
        tampilkan_menu()
        pilihan = baca_pilihan_menu()  # sudah termasuk error handling input non-angka

        # try-except di sini menangkap OperasiDibatalkanException dari fitur
        # manapun, sehingga jika user mengetik "batal" di tengah proses input,
        # program otomatis kembali ke menu utama tanpa menyelesaikan operasi.
        try:
            if pilihan == 1:
                tambah_film()
            elif pilihan == 2:
                tampilkan_semua_film()
            elif pilihan == 3:
                update_film()
            elif pilihan == 4:
                hapus_film()
            elif pilihan == 5:
                cari_film()
            elif pilihan == 6:
                berjalan = False
                print("Program selesai. Terima kasih!")
            else:
                # Error handling: pilihan menu di luar rentang 1-6
                print("Pilihan tidak valid! Silakan pilih menu 1-6.")
        except OperasiDibatalkanException as e:
            print(f"\n{e} Kembali ke menu utama.")


# ==================== TAMPILAN MENU ====================
def tampilkan_menu() -> None:
    print()
    print("=" * 40)
    print("|| ----- BIOSKOP SINEFIL ABIEZ ------ ||")
    print("=" * 40)
    print("||  1. Tambah Film                    ||")
    print("||  2. Tampilkan Semua Film           ||")
    print("||  3. Update Film                    ||")
    print("||  4. Hapus Film                     ||")
    print("||  5. Cari Film                      ||")
    print("||  6. Keluar                         ||")
    print("=" * 40)
    print("Ketik 'batal' kapan saja untuk kembali ke menu ini")


def baca_pilihan_menu() -> int:
    """
    Membaca pilihan menu dari user.
    Error handling: jika user mengetik huruf/karakter non-angka,
    program tidak akan crash (ValueError ditangkap), melainkan
    mengembalikan nilai -1 (dianggap pilihan tidak valid).
    """
    input_user = input("Pilih menu: ").strip()
    try:
        return int(input_user)
    except ValueError:
        print("Input harus berupa angka!")
        return -1


# ==================== FITUR 1: TAMBAH DATA ====================
def tambah_film() -> None:
    print("\n-- Tambah Film Baru --")

    # Error handling: ID tidak boleh kosong dan tidak boleh duplikat
    while True:
        id_film = baca_baris("ID Film (contoh: F003): ")
        if not id_film:
            print("ID tidak boleh kosong!")
            continue
        if cari_film_by_id(id_film) is not None:
            print("ID sudah digunakan film lain! Gunakan ID lain.")
            continue
        break

    # Error handling: judul tidak boleh kosong
    judul = baca_baris("Judul Film: ")
    while not judul:
        print("Judul tidak boleh kosong!")
        judul = baca_baris("Judul Film: ")

    # Error handling: genre tidak boleh kosong
    genre = baca_baris("Genre: ")
    while not genre:
        print("Genre tidak boleh kosong!")
        genre = baca_baris("Genre: ")

    # Error handling: durasi harus angka dan lebih dari 0
    durasi = baca_angka_int("Durasi (menit): ", wajib_positif=True)

    # Error handling: sutradara tidak boleh kosong
    sutradara = baca_baris("Sutradara: ")
    while not sutradara:
        print("Nama sutradara tidak boleh kosong!")
        sutradara = baca_baris("Sutradara: ")

    # Error handling: harga tiket harus angka dan tidak boleh negatif
    harga = baca_angka_float("Harga Tiket: ", boleh_negatif=False)

    daftar_film.append(Film(id_film, judul, genre, durasi, sutradara, harga))
    print("Film berhasil ditambahkan!")


# ==================== FITUR 2: TAMPILKAN DATA ====================
def tampilkan_semua_film() -> None:
    print("\n-- Daftar Semua Film --")

    # Error handling: cek apakah list kosong sebelum looping
    if not daftar_film:
        print("Belum ada data film.")
        return

    for film in daftar_film:
        film.tampilkan_info()


# ==================== FITUR 3: UPDATE DATA ====================
def update_film() -> None:
    print("\n-- Update Film --")

    if not daftar_film:
        print("Belum ada data film untuk diupdate.")
        return

    id_film = baca_baris("Masukkan ID film yang ingin diupdate: ")
    film = cari_film_by_id(id_film)

    # Error handling: ID tidak ditemukan
    if film is None:
        print(f"Film dengan ID '{id_film}' tidak ditemukan!")
        return

    print("Data film ditemukan, silakan masukkan data baru.")
    print("(Kosongkan/tekan Enter jika tidak ingin mengubah field tersebut)")

    judul = baca_baris(f"Judul baru [{film.get_judul()}]: ")
    if judul:
        film.set_judul(judul)

    genre = baca_baris(f"Genre baru [{film.get_genre()}]: ")
    if genre:
        film.set_genre(genre)

    durasi_str = baca_baris(f"Durasi baru (menit) [{film.get_durasi()}]: ")
    if durasi_str:
        try:
            durasi_baru = int(durasi_str)
            if durasi_baru <= 0:
                # Error handling: nilai tidak valid, field lama dipertahankan
                print("Durasi harus lebih dari 0. Durasi tidak diubah.")
            else:
                film.set_durasi(durasi_baru)
        except ValueError:
            # Error handling: input bukan angka
            print("Input durasi tidak valid (bukan angka). Durasi tidak diubah.")

    sutradara = baca_baris(f"Sutradara baru [{film.get_sutradara()}]: ")
    if sutradara:
        film.set_sutradara(sutradara)

    harga_str = baca_baris(f"Harga tiket baru [{film.get_harga_tiket()}]: ")
    if harga_str:
        try:
            harga_baru = float(harga_str)
            if harga_baru < 0:
                print("Harga tidak boleh negatif. Harga tidak diubah.")
            else:
                film.set_harga_tiket(harga_baru)
        except ValueError:
            print("Input harga tidak valid (bukan angka). Harga tidak diubah.")

    print("Data film berhasil diupdate!")


# ==================== FITUR 4: HAPUS DATA ====================
def hapus_film() -> None:
    print("\n-- Hapus Film --")

    if not daftar_film:
        print("Belum ada data film untuk dihapus.")
        return

    id_film = baca_baris("Masukkan ID film yang ingin dihapus: ")
    film = cari_film_by_id(id_film)

    # Error handling: ID tidak ditemukan
    if film is None:
        print(f"Film dengan ID '{id_film}' tidak ditemukan!")
        return

    # Konfirmasi sebelum menghapus (mencegah penghapusan tidak sengaja)
    konfirmasi = baca_baris(f"Yakin ingin menghapus '{film.get_judul()}'? (y/n): ").lower()

    if konfirmasi == "y":
        daftar_film.remove(film)
        print("Film berhasil dihapus!")
    else:
        print("Penghapusan dibatalkan.")


# ==================== FITUR 5: CARI DATA ====================
def cari_film() -> None:
    print("\n-- Cari Film --")

    if not daftar_film:
        print("Belum ada data film.")
        return

    id_film = baca_baris("Masukkan ID film yang dicari: ")
    film = cari_film_by_id(id_film)

    # Error handling: ID tidak ditemukan
    if film is None:
        print(f"Film dengan ID '{id_film}' tidak ditemukan!")
    else:
        print("Film ditemukan:")
        film.tampilkan_info()


# ==================== FUNGSI BANTUAN (HELPER) ====================

def cari_film_by_id(id_film: str) -> Optional[Film]:
    """
    Mencari objek Film di dalam list berdasarkan ID.
    Mengembalikan None jika tidak ditemukan (dipakai untuk error handling
    di semua fitur yang butuh ID: update, hapus, cari, dan cek duplikasi
    saat tambah).
    """
    for film in daftar_film:
        if film.get_id().lower() == id_film.lower():
            return film
    return None


def baca_baris(label: str) -> str:
    """
    Membaca satu baris input teks dari user, sekaligus menjadi "gerbang"
    pengecekan fitur batal: jika user mengetik "batal" (tanpa memandang
    huruf besar/kecil), fungsi ini akan melempar OperasiDibatalkanException
    yang otomatis menghentikan fitur yang sedang berjalan dan mengembalikan
    user ke menu utama.
    """
    input_user = input(label).strip()
    if input_user.lower() == KATA_BATAL:
        raise OperasiDibatalkanException()
    return input_user


def baca_angka_int(label: str, wajib_positif: bool) -> int:
    """
    Membaca input angka bulat (int) dari user dengan validasi:
    - Bisa dibatalkan dengan mengetik "batal" (lihat baca_baris)
    - Harus berupa angka (menangkap ValueError)
    - Jika wajib_positif True, nilai harus lebih besar dari 0
    Fungsi ini akan terus meminta input sampai user memasukkan nilai yang valid.
    """
    while True:
        input_user = baca_baris(label)  # sudah termasuk pengecekan kata "batal"
        try:
            nilai = int(input_user)
            if wajib_positif and nilai <= 0:
                print("Nilai harus lebih besar dari 0!")
                continue
            return nilai
        except ValueError:
            print("Input tidak valid! Harap masukkan angka bulat.")


def baca_angka_float(label: str, boleh_negatif: bool) -> float:
    """
    Membaca input angka desimal (float) dari user dengan validasi:
    - Bisa dibatalkan dengan mengetik "batal" (lihat baca_baris)
    - Harus berupa angka (menangkap ValueError)
    - Jika boleh_negatif False, nilai negatif ditolak
    """
    while True:
        input_user = baca_baris(label)  # sudah termasuk pengecekan kata "batal"
        try:
            nilai = float(input_user)
            if not boleh_negatif and nilai < 0:
                print("Nilai tidak boleh negatif!")
                continue
            return nilai
        except ValueError:
            print("Input tidak valid! Harap masukkan angka.")


# Baris ini memastikan main() hanya dijalankan saat file ini dieksekusi
# langsung (python main.py), bukan saat di-import sebagai module oleh file lain.
if __name__ == "__main__":
    main()