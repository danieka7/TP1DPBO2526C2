#include <iostream>
#include <string>
using namespace std;

/*
 * Film.cpp
 * -----------
 * Class ini merepresentasikan data sebuah film yang tayang di bioskop.
 * Setiap objek Film menyimpan informasi berupa id, judul, genre, durasi,
 * sutradara, dan harga tiket.
 *
 * Class ini murni sebagai "data holder" (menyimpan data + getter/setter),
 * sedangkan logika tambah/hapus/update/cari ditangani oleh Main.cpp
 * yang mengelola kumpulan objek Film dalam sebuah std::vector.
 *
 */

class Film {

private:
    // ==================== ATRIBUT ====================
    string id;          // Identifier unik, contoh: "F001"
    string judul;       // Judul film
    string genre;       // Genre film, contoh: "Action", "Horror"
    int durasi;         // Durasi film dalam satuan menit
    string sutradara;   // Nama sutradara film
    double hargaTiket;  // Harga tiket film dalam Rupiah

public:
    // ==================== CONSTRUCTOR ====================
    // Constructor untuk membuat objek Film baru dengan seluruh atributnya.
    // Memakai initializer list (lebih efisien dibanding assignment di dalam body).
    
    // konstrukor kosnong
    Film (){
        this->id = "";
        this->judul = "";
        this->genre = "";
        this->durasi = 0;
        this->sutradara = "";
        this->hargaTiket = 0;
    }

    // konstruktor diisi atribut
    Film (string id, string judul, string genre, int durasi, string sutradara, double hargaTiket){
        this->id = id;
        this->judul = judul;
        this->genre = genre;
        this->durasi = durasi;
        this->sutradara = sutradara;
        this->hargaTiket = hargaTiket;
    }

    // ==================== GETTER ====================
    // Getter digunakan untuk mengambil nilai tiap atribut dari luar class.
    string getId() { 
        return id; 
    }
    string getJudul() { 
        return judul; 
    }
    string getGenre() { 
        return genre; 
    }
    int getDurasi() { 
        return durasi; 
    }
    string getSutradara() { 
        return sutradara; 
    }
    double getHargaTiket() { 
        return hargaTiket; 
    }

    // ==================== SETTER ====================
    // Setter digunakan agar fitur "Update Data" bisa mengubah nilai atribut
    // objek Film yang sudah ada tanpa perlu membuat objek baru.
    void setJudul(string judul) { 
        this->judul = judul; 
    }
    void setGenre(string genre) { 
        this->genre = genre; 
    }
    void setDurasi(int durasi) { 
        this->durasi = durasi; 
    }
    void setSutradara(string sutradara) { 
        this->sutradara = sutradara; 
    }
    void setHargaTiket(double hargaTiket) { 
        this->hargaTiket = hargaTiket; 
    }

    // ==================== METHOD TAMBAHAN ====================
    // Menampilkan seluruh informasi film dalam format yang rapi,
    // termasuk memformat harga tiket dengan pemisah ribuan (mis: Rp45.000).
    // Catatan: kata kunci "const" pada method ini sudah dihapus sesuai
    // permintaan, sehingga method ini sekarang boleh mengubah atribut
    // objek (walau pada praktiknya method ini tidak mengubah apa pun).
    void tampilkanInfo() {
        string hargaStr = to_string(static_cast<long long>(hargaTiket));
        string hargaFormatted;
        int hitung = 0;
        for (int i = static_cast<int>(hargaStr.size()) - 1; i >= 0; i--) {
            hargaFormatted = hargaStr[i] + hargaFormatted;
            hitung++;
            if (hitung % 3 == 0 && i != 0) {
                hargaFormatted = "." + hargaFormatted;
            }
        }

        cout << "----------------------------------------\n";
        cout << "ID           : " << id << "\n";
        cout << "Judul        : " << judul << "\n";
        cout << "Genre        : " << genre << "\n";
        cout << "Durasi       : " << durasi << " menit\n";
        cout << "Sutradara    : " << sutradara << "\n";
        cout << "Harga Tiket  : Rp" << hargaFormatted << "\n";
        cout << "----------------------------------------\n";
    }
};