# Tugas Praktikum 1 - Desain Pemrograman Berbasis Objek

## Janji
Saya **Dani Eka Saputra** dengan NIM **2501158** mengerjakan Tugas Praktikum 1 pada Mata Kuliah Desain dan Pemrograman Berorientasi Objek untuk keberkahan-Nya maka saya tidak melakukan kecurangan seperti yang telah dispesifikasikan. Aamiin

## Fitur Utama
### C++ / Java / Python (CLI / Terminal)
- Tambah Data: Menambah objek baru.
- Tampilkan Data: Menampilkan semua objek yang tersimpan.
- Update Data: Mengubah data objek berdasarkan id.
- Hapus Data: Menghapus objek berdasarkan id .
- Cari Data: Mencari film berdasarkan id

### PHP (Web)
- Tambah Data: Menambah objek baru melalui form HTML.
- Tampilkan Data: Menampilkan semua data film yang tersimpan di bagian bawah.
- Update Data: Mengubah data objek berdasarkan id.
- Hapus Data: Menghapus objek berdasarkan id.
- Cari Data: Mencari satu objek spesifik berdasarkan id atau judul

##  Fitur Tambahan
- **Fitur Batal** (CLI) — ketik `batal` di manapun untuk kembali ke menu utama tanpa menyelesaikan operasi
- **Error Handling** — validasi input kosong, ID duplikat/tidak ditemukan, tipe data salah, nilai negatif, dan list/data kosong
- **Penyimpanan tanpa Database** (PHP) — data disimpan sementara menggunakan `$_SESSION`

## 🗂️ Struktur Projek
```
.
├── cpp
│   ├── Film.cpp
│   ├── main.cpp
│   └── operasiBatal.cpp
├── java
│   ├── Film.java
│   ├── Main.java
│   └── OperasiBatal.java
├── php
│   ├── Film.php
│   ├── main.php
│   └── uploads
│       ├── poster_6aad010571c630.46461702.jpg
│       └── poster_6aad1afc536599.58279517.jpg
├── python
│   ├── Film.py
│   ├── main.py
│   └── OperasiBatal.py
└── README.md 
```

## Desain dan Alur Kerja

### Struktur Data Kelas Film
Untuk desain, disini digunakan satu kelas dengan nama **Film** dengan atribut sebagai berikut:
| Atribut                                 | Keterangan             |
| --------------------------------------- | ---------------------- |
| **ID** *(string)*                         | Identifier unik film   |
| **Judul** *(string)*                      | Judul film             |
| **Genre** *(string)*                      | Genre film             |
| **Durasi** *(int)*                        | Durasi dalam menit     |
| **Sutradara** *(string)*                  | Nama sutradara         |
| **Harga Tiket** *(double)*                | Harga tiket            |
| **Gambar *(hanya versi Web)*** *(string)* | Path lokal poster film |

### Alur Kerja
- #### Versi CLI (Java, C++, Python) 
  berjalan dengan alur menu loop yang identik di ketiganya. Saat program dijalankan, layar menu akan terus ditampilkan berulang-ulang selama user belum memilih "Keluar". Setiap kali user memilih sebuah menu, program membaca pilihan tersebut, memvalidasinya, lalu menjalankan fitur yang sesuai (Tambah, Tampilkan, Update, Hapus, atau Cari). Di dalam fitur tersebut, user diminta mengisi data satu per satu melalui input, dan setiap input divalidasi sebelum diproses lebih lanjut. Jika di tengah proses input user mengetik kata kunci "batal", program akan langsung menghentikan fitur yang sedang berjalan dan kembali ke tampilan menu tanpa menyimpan perubahan apa pun. Setelah sebuah fitur selesai dijalankan (baik berhasil maupun dibatalkan), program kembali menampilkan menu utama, dan siklus ini berulang terus dalam satu kali eksekusi program yang sama. Karena data hanya disimpan di memori (List/Vector), begitu program ditutup maka seluruh data akan hilang.

- #### Versi Web (PHP) 
  mengikuti alur request-response khas aplikasi web, yang berbeda dari alur loop di CLI. Setiap kali halaman dibuka, browser mengirim request ke index.php, lalu program memulai session dan memuat data film yang sebelumnya sudah tersimpan di $_SESSION. Program kemudian memeriksa parameter aksi yang dikirim (misalnya tambah, update, hapus, edit, atau cari) untuk menentukan proses apa yang harus dijalankan. Jika ada data yang dikirim lewat form, data tersebut divalidasi terlebih dahulu; jika tidak valid, pesan error disiapkan untuk ditampilkan, dan jika valid, data akan diproses ke dalam array film di session (ditambah, diubah, atau dihapus). Setelah seluruh proses selesai, halaman HTML dirender ulang dari awal — lengkap dengan form, tabel data terbaru, dan pesan sukses/error — lalu dikirim kembali ke browser. Berbeda dengan CLI yang punya proses input bertahap dalam satu sesi program yang terus hidup, di versi web setiap aksi (klik tombol atau kirim form) memicu request baru yang independen, sehingga fitur "batal" pun diwujudkan secara berbeda: bukan sebagai exception di tengah proses input, melainkan sebagai tautan yang mengarahkan user kembali ke halaman utama.

## Dokumentasi

- ### Dokumentasi Program C++
    - #### Tambah Data Baru
        ![alt](<dokumentasi/cpp/cpp_add.png>)
    - #### Tampilkan Semua Data
        ![alt](<dokumentasi/cpp/cpp_view.png>)
    - #### Update Data
        ![alt](<dokumentasi/cpp/cpp_update.png>)
    - #### Hapus Data
        ![alt](<dokumentasi/cpp/cpp_delete.png>)
    - #### Mencari Data dengan ID
        ![alt](<dokumentasi/cpp/cpp_search.png>)

- ### Dokumentasi Program Java
    - #### Tambah Data Baru
        ![alt](<dokumentasi/java/java_add.png>)
    - #### Tampilkan Semua Data
        ![alt](<dokumentasi/java/java_view.png>)
    - #### Update Data
        ![alt](<dokumentasi/java/java_update.png>)
    - #### Hapus Data
        ![alt](<dokumentasi/java/java_delete.png>)
    - #### Mencari Data dengan ID
        ![alt](<dokumentasi/java/java_search.png>)

- ### Dokumentasi Program Python
    - #### Tambah Data Baru
        ![alt](<dokumentasi/python/python_add.png>)
    - #### Tampilkan Semua Data
        ![alt](<dokumentasi/python/python_view.png>)
    - #### Update Data
        ![alt](<dokumentasi/python/python_update.png>)
    - #### Hapus Data
        ![alt](<dokumentasi/python/python_delete.png>)
    - #### Mencari Data dengan ID
        ![alt](<dokumentasi/python/python_search.png>)

- ### Dokumentasi Program PHP
    - #### Tambah Data Baru
        ![alt](<dokumentasi/php/php_add.png>)
    - #### Tampilkan Semua Data
        ![alt](<dokumentasi/php/php_view.png>)
    - #### Update Data
        ![alt](<dokumentasi/php/php_update1.png>)
        ![alt](<dokumentasi/php/php_update2.png>)
    - #### Hapus Data
        ![alt](<dokumentasi/php/php_delete1.png>)
        ![alt](<dokumentasi/php/php_delete2.png>)
    - #### Mencari Data dengan ID
        ![alt](<dokumentasi/php/php_search.png>)