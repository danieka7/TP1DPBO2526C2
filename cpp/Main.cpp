#include <iostream>
#include <vector>
#include <string>
#include <algorithm>  // untuk std::transform (mengubah huruf jadi lowercase)
#include <limits>     // untuk std::numeric_limits (membersihkan buffer input)
#include "Film.cpp"
#include "operasiBatal.cpp"

using namespace std;

// Kata kunci yang dipakai user untuk membatalkan operasi yang sedang berjalan.
// (const dihapus -> sekarang variabel biasa, bukan konstanta)
string KATA_BATAL = "batal";

// Vector untuk menyimpan seluruh objek Film yang sudah ditambahkan.
// Menggunakan pointer (Film*) agar objek yang sama bisa diubah (update)
// dan dihapus (delete) tanpa perlu menyalin seluruh objek.
vector<Film*> daftarFilm;

void tampilkanMenu();
int bacaPilihanMenu();
void tambahFilm();
void tampilkanSemuaFilm();
void updateFilm();
void hapusFilm();
void cariFilm();

Film* cariFilmById(string id);
string bacaBaris(string label);
int bacaAngkaInt(string label, bool wajibPositif);
double bacaAngkaDouble(string label, bool bolehNegatif);
string toLowerStr(string s);

// ==================== TAMPILAN MENU ====================
void tampilkanMenu() {
    cout << "========================================\n";
    cout << "|| ----- BIOSKOP SINEFIL ABIEZ ------ ||\n";
    cout << "========================================\n";
    cout << "|| 1. Tambah Film                     ||\n";
    cout << "|| 2. Tampilkan Semua Film            ||\n";
    cout << "|| 3. Update Film                     ||\n";
    cout << "|| 4. Hapus Film                      ||\n";
    cout << "|| 5. Cari Film                       ||\n";
    cout << "|| 6. Keluar                          ||\n";
    cout << "========================================\n";
    cout << "(Ketik 'batal' pada input kapan saja untuk kembali ke menu ini)\n";
    cout << "Pilih menu: ";
}

/*
 * Membaca pilihan menu dari user.
 * Error handling: jika user mengetik huruf/karakter non-angka,
 * program tidak akan crash. cin akan masuk ke "fail state",
 * yang kita deteksi lalu kita bersihkan (clear + ignore), sama seperti
 * InputMismatchException yang ditangkap di versi Java.
 */
int bacaPilihanMenu() {
    int pilihan;
    if (!(cin >> pilihan)) {
        cin.clear(); // membersihkan status error pada cin
        // membuang sisa input yang salah dari buffer agar tidak infinite loop
        cin.ignore(numeric_limits<streamsize>::max(), '\n');
        cout << "Input harus berupa angka!\n";
        return -1;
    }
    cin.ignore(numeric_limits<streamsize>::max(), '\n'); // buang newline sisa
    return pilihan;
}

// ==================== FITUR 1: TAMBAH DATA ====================
void tambahFilm() {
    cout << "\n-- Tambah Film Baru --\n";

    // Error handling: ID tidak boleh kosong dan tidak boleh duplikat
    string id;
    while (true) {
        id = bacaBaris("ID Film (contoh: F003): ");

        if (id.empty()) {
            cout << "ID tidak boleh kosong!\n";
            continue;
        }
        if (cariFilmById(id) != nullptr) {
            cout << "ID sudah digunakan film lain! Gunakan ID lain.\n";
            continue;
        }
        break;
    }

    // Error handling: judul tidak boleh kosong
    string judul;
    do {
        judul = bacaBaris("Judul Film: ");
        if (judul.empty()) {
            cout << "Judul tidak boleh kosong!\n";
        }
    } while (judul.empty());

    // Error handling: genre tidak boleh kosong
    string genre;
    do {
        genre = bacaBaris("Genre: ");
        if (genre.empty()) {
            cout << "Genre tidak boleh kosong!\n";
        }
    } while (genre.empty());

    // Error handling: durasi harus angka dan lebih dari 0
    int durasi = bacaAngkaInt("Durasi (menit): ", true);

    // Error handling: sutradara tidak boleh kosong
    string sutradara;
    do {
        sutradara = bacaBaris("Sutradara: ");
        if (sutradara.empty()) {
            cout << "Nama sutradara tidak boleh kosong!\n";
        }
    } while (sutradara.empty());

    // Error handling: harga tiket harus angka dan tidak boleh negatif
    double harga = bacaAngkaDouble("Harga Tiket: ", false);

    // Film* filmBaru = new Film(id, judul, genre, durasi, sutradara, harga);
    // daftarFilm.push_back(filmBaru);
    cout << "Film berhasil ditambahkan!\n";
}

// ==================== FITUR 2: TAMPILKAN DATA ====================
void tampilkanSemuaFilm() {
    cout << "\n-- Daftar Semua Film --\n";

    // Error handling: cek apakah vector kosong sebelum looping
    if (daftarFilm.empty()) {
        cout << "Belum ada data film.\n";
        return;
    }

    for (Film* film : daftarFilm) {
        film->tampilkanInfo();
    }
}

// ==================== FITUR 3: UPDATE DATA ====================
void updateFilm() {
    cout << "\n-- Update Film --\n";

    if (daftarFilm.empty()) {
        cout << "Belum ada data film untuk diupdate.\n";
        return;
    }

    string id = bacaBaris("Masukkan ID film yang ingin diupdate: ");
    Film* film = cariFilmById(id);

    // Error handling: ID tidak ditemukan
    if (film == nullptr) {
        cout << "Film dengan ID '" << id << "' tidak ditemukan!\n";
        return;
    }

    cout << "Data film ditemukan, silakan masukkan data baru.\n";
    cout << "(Kosongkan/tekan Enter jika tidak ingin mengubah field tersebut)\n";

    string judul = bacaBaris("Judul baru [" + film->getJudul() + "]: ");
    if (!judul.empty()) {
        film->setJudul(judul);
    }

    string genre = bacaBaris("Genre baru [" + film->getGenre() + "]: ");
    if (!genre.empty()) {
        film->setGenre(genre);
    }

    string durasiStr = bacaBaris("Durasi baru (menit) [" + to_string(film->getDurasi()) + "]: ");
    if (!durasiStr.empty()) {
        try {
            size_t posisiAkhir;
            int durasiBaru = stoi(durasiStr, &posisiAkhir);
            // Memastikan seluruh string berupa angka (bukan cuma sebagian, mis: "12abc")
            if (posisiAkhir != durasiStr.size()) {
                throw invalid_argument("bukan angka murni");
            }
            if (durasiBaru <= 0) {
                // Error handling: nilai tidak valid, field lama dipertahankan
                cout << "Durasi harus lebih dari 0. Durasi tidak diubah.\n";
            } else {
                film->setDurasi(durasiBaru);
            }
        } catch (exception&) {
            // Error handling: input bukan angka (padanan NumberFormatException)
            cout << "Input durasi tidak valid (bukan angka). Durasi tidak diubah.\n";
        }
    }

    string sutradara = bacaBaris("Sutradara baru [" + film->getSutradara() + "]: ");
    if (!sutradara.empty()) {
        film->setSutradara(sutradara);
    }

    string hargaStr = bacaBaris("Harga tiket baru [" + to_string(film->getHargaTiket()) + "]: ");
    if (!hargaStr.empty()) {
        try {
            size_t posisiAkhir;
            double hargaBaru = stod(hargaStr, &posisiAkhir);
            if (posisiAkhir != hargaStr.size()) {
                throw invalid_argument("bukan angka murni");
            }
            if (hargaBaru < 0) {
                cout << "Harga tidak boleh negatif. Harga tidak diubah.\n";
            } else {
                film->setHargaTiket(hargaBaru);
            }
        } catch (exception&) {
            cout << "Input harga tidak valid (bukan angka). Harga tidak diubah.\n";
        }
    }

    cout << "Data film berhasil diupdate!\n";
}

// ==================== FITUR 4: HAPUS DATA ====================
void hapusFilm() {
    cout << "\n-- Hapus Film --\n";

    if (daftarFilm.empty()) {
        cout << "Belum ada data film untuk dihapus.\n";
        return;
    }

    string id = bacaBaris("Masukkan ID film yang ingin dihapus: ");
    Film* film = cariFilmById(id);

    // Error handling: ID tidak ditemukan
    if (film == nullptr) {
        cout << "Film dengan ID '" << id << "' tidak ditemukan!\n";
        return;
    }

    // Konfirmasi sebelum menghapus (mencegah penghapusan tidak sengaja)
    string konfirmasi = toLowerStr(bacaBaris("Yakin ingin menghapus '" + film->getJudul() + "'? (y/n): "));

    if (konfirmasi == "y") {
        // Mencari posisi (iterator) film di dalam vector agar bisa dihapus
        auto it = find(daftarFilm.begin(), daftarFilm.end(), film);
        if (it != daftarFilm.end()) {
            delete *it;              // membebaskan memori objek Film
            daftarFilm.erase(it);    // menghapus pointer dari vector
        }
        cout << "Film berhasil dihapus!\n";
    } else {
        cout << "Penghapusan dibatalkan.\n";
    }
}

// ==================== FITUR 5: CARI DATA ====================
void cariFilm() {
    cout << "\n-- Cari Film --\n";

    if (daftarFilm.empty()) {
        cout << "Belum ada data film.\n";
        return;
    }

    string id = bacaBaris("Masukkan ID film yang dicari: ");
    Film* film = cariFilmById(id);

    // Error handling: ID tidak ditemukan
    if (film == nullptr) {
        cout << "Film dengan ID '" << id << "' tidak ditemukan!\n";
    } else {
        cout << "Film ditemukan:\n";
        film->tampilkanInfo();
    }
}

// ==================== FUNGSI BANTUAN (HELPER) ====================

// Mengubah seluruh huruf pada string menjadi huruf kecil.
// Dipakai untuk perbandingan tanpa memandang besar/kecil huruf
// (padanan .equalsIgnoreCase() / .toLowerCase() di Java).
// Catatan: parameter "const string&" diubah menjadi "string" (by value)
// karena "const" dihapus. Tanpa "const", parameter bertipe reference
// (string&) tidak bisa menerima nilai sementara seperti hasil
// film->getId(), sehingga parameter harus disalin (by value).
string toLowerStr(string s) {
    string hasil = s;
    transform(hasil.begin(), hasil.end(), hasil.begin(), ::tolower);
    return hasil;
}

/*
 * Mencari objek Film di dalam vector berdasarkan ID.
 * Mengembalikan nullptr jika tidak ditemukan (dipakai untuk error handling
 * di semua fitur yang butuh ID: update, hapus, cari, dan cek duplikasi saat tambah).
 */
Film* cariFilmById(string id) {
    for (Film* film : daftarFilm) {
        if (toLowerStr(film->getId()) == toLowerStr(id)) {
            return film;
        }
    }
    return nullptr;
}

/*
 * Membaca satu baris input teks dari user, sekaligus menjadi "gerbang"
 * pengecekan fitur batal: jika user mengetik "batal" (tanpa memandang
 * huruf besar/kecil), fungsi ini akan melempar OperasiBatal yang otomatis
 * menghentikan fitur yang sedang berjalan dan mengembalikan user ke menu utama.
 *
 * Semua pembacaan input teks pada file ini (ID, judul, genre, sutradara,
 * konfirmasi hapus, dsb) wajib melalui fungsi ini agar fitur batal
 * konsisten berlaku di seluruh prompt.
 */
string bacaBaris(string label) {
    cout << label;
    string input;
    getline(cin, input);

    // Trim spasi di awal & akhir string (padanan .trim() di Java)
    size_t awal = input.find_first_not_of(" \t\r\n");
    size_t akhir = input.find_last_not_of(" \t\r\n");
    input = (awal == string::npos) ? "" : input.substr(awal, akhir - awal + 1);

    if (toLowerStr(input) == KATA_BATAL) {
        throw OperasiBatal();
    }
    return input;
}

/*
 * Membaca input angka bulat (int) dari user dengan validasi:
 * - Bisa dibatalkan dengan mengetik "batal" (lihat bacaBaris)
 * - Harus berupa angka (menangkap exception dari stoi)
 * - Jika parameter wajibPositif = true, nilai harus lebih besar dari 0
 * Fungsi ini akan terus meminta input sampai user memasukkan nilai yang valid.
 */
int bacaAngkaInt(string label, bool wajibPositif) {
    int nilai;
    while (true) {
        string input = bacaBaris(label); // sudah termasuk pengecekan kata "batal"
        try {
            size_t posisiAkhir;
            nilai = stoi(input, &posisiAkhir);
            if (posisiAkhir != input.size()) {
                throw invalid_argument("bukan angka murni");
            }
            if (wajibPositif && nilai <= 0) {
                cout << "Nilai harus lebih besar dari 0!\n";
                continue;
            }
            break;
        } catch (exception&) {
            cout << "Input tidak valid! Harap masukkan angka bulat.\n";
        }
    }
    return nilai;
}

/*
 * Membaca input angka desimal (double) dari user dengan validasi:
 * - Bisa dibatalkan dengan mengetik "batal" (lihat bacaBaris)
 * - Harus berupa angka (menangkap exception dari stod)
 * - Jika parameter bolehNegatif = false, nilai negatif ditolak
 */
double bacaAngkaDouble(string label, bool bolehNegatif) {
    double nilai;
    while (true) {
        string input = bacaBaris(label); // sudah termasuk pengecekan kata "batal"
        try {
            size_t posisiAkhir;
            nilai = stod(input, &posisiAkhir);
            if (posisiAkhir != input.size()) {
                throw invalid_argument("bukan angka murni");
            }
            if (!bolehNegatif && nilai < 0) {
                cout << "Nilai tidak boleh negatif!\n";
                continue;
            }
            break;
        } catch (exception&) {
            cout << "Input tidak valid! Harap masukkan angka.\n";
        }
    }
    return nilai;
}

// ==================== FUNGSI MAIN (ENTRY POINT) ====================
int main() {
    bool berjalan = true;

    // Data awal (opsional) supaya program tidak kosong saat pertama dijalankan.
    daftarFilm.push_back(new Film("F001", "The Dark Knight", "Action", 152, "Christopher Nolan", 45000));
    daftarFilm.push_back(new Film("F002", "Interstellar", "Sci-Fi", 169, "Christopher Nolan", 50000));

    // ==================== LOOP MENU UTAMA ====================
    while (berjalan) {
        tampilkanMenu();
        int pilihan = bacaPilihanMenu(); // sudah termasuk error handling input non-angka

        // try-catch di sini menangkap exception OperasiBatal dari fitur
        // manapun (tambah/update/hapus/cari), sehingga jika user mengetik
        // "batal" di tengah proses input, program otomatis kembali ke
        // menu utama tanpa menyelesaikan operasi tersebut.
        try {
            switch (pilihan) {
                case 1:
                    tambahFilm();
                    break;
                case 2:
                    tampilkanSemuaFilm();
                    break;
                case 3:
                    updateFilm();
                    break;
                case 4:
                    hapusFilm();
                    break;
                case 5:
                    cariFilm();
                    break;
                case 6:
                    berjalan = false;
                    cout << "Program selesai. Terima kasih!\n";
                    break;
                default:
                    // Error handling: pilihan menu di luar rentang 1-6
                    cout << "Pilihan tidak valid! Silakan pilih menu 1-6.\n";
            }
        } catch (OperasiBatal& e) {
            cout << "\n" << e.what() << " Kembali ke menu utama.\n";
        }
    }

    // Membersihkan memori: menghapus seluruh objek Film yang dibuat dengan "new"
    // agar tidak terjadi memory leak (C++ tidak punya garbage collector seperti Java).
    for (Film* f : daftarFilm) {
        delete f;
    }
    daftarFilm.clear();

    return 0;
}