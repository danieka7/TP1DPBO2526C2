#include <exception>
#include <string>
using namespace std;

/*
 * OperasiBatal.cpp
 * --------------------
 * Class exception (turunan dari exception) yang dilempar ketika
 * user mengetik kata kunci "batal" pada salah satu prompt input.
 *
 * Exception ini ditangkap satu kali saja di fungsi main(), sehingga
 * fitur apapun yang sedang berjalan (tambah/update/hapus/cari) langsung
 * dihentikan dan program kembali ke tampilan menu utama.
 *
 * CATATAN: karena tidak memakai file header (.h), seluruh isi class
 * ditulis langsung di sini, lalu file ini di-#include langsung oleh
 * Main.cpp.
 */
class OperasiBatal : public exception {
private:
    string pesan; // Pesan yang ditampilkan saat exception ditangkap

public:
    // Constructor: pesan default "Operasi dibatalkan."
    OperasiBatal() : pesan("Operasi dibatalkan.") {}

    // override what() dari exception agar bisa mengembalikan pesan kustom.
    // "noexcept" menandakan method ini dijamin tidak melempar exception lain.
    const char* what() const noexcept override {
        return pesan.c_str();
    }
};