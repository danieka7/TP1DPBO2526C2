import java.util.ArrayList;
import java.util.InputMismatchException;
import java.util.List;
import java.util.Scanner;

/**
 * Class BioskopApp
 * -----------------
 * Class ini adalah program utama (entry point) yang mengelola sekumpulan
 * objek Film menggunakan ArrayList (implementasi dari List of Object).
 *
 * Fitur yang disediakan:
 * 1. Tambah Data
 * 2. Tampilkan Data
 * 3. Update Data (berdasarkan ID)
 * 4. Hapus Data (berdasarkan ID)
 * 5. Cari Data (berdasarkan ID)
 *
 * Semua input dari user divalidasi agar program tidak crash ketika
 * menerima input yang tidak sesuai (error handling).
 */
public class Main {

    // List untuk menyimpan seluruh objek Film yang sudah ditambahkan.
    private static List<Film> daftarFilm = new ArrayList<>();

    // Scanner dipakai bersama di seluruh method agar tidak terjadi konflik input.
    private static Scanner scanner = new Scanner(System.in);

    public static void main(String[] args) {
        boolean berjalan = true;

        // Data awal (opsional) supaya program tidak kosong saat pertama dijalankan.
        daftarFilm.add(new Film("F001", "The Dark Knight", "Action", 152, "Christopher Nolan", 45000));
        daftarFilm.add(new Film("F002", "Interstellar", "Sci-Fi", 169, "Christopher Nolan", 50000));

        // ==================== LOOP MENU UTAMA ====================
        while (berjalan) {
            tampilkanMenu();
            int pilihan = bacaPilihanMenu(); // sudah termasuk error handling input non-angka

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
                    System.out.println("Program selesai. Terima kasih!");
                    break;
                default:
                    // Error handling: pilihan menu di luar rentang 1-6
                    System.out.println("Pilihan tidak valid! Silakan pilih menu 1-6.");
            }
        }

        scanner.close();
    }

    // ==================== TAMPILAN MENU ====================
    private static void tampilkanMenu() {
        System.out.println("\n==== MENU BIOSKOP ====");
        System.out.println("1. Tambah Film");
        System.out.println("2. Tampilkan Semua Film");
        System.out.println("3. Update Film");
        System.out.println("4. Hapus Film");
        System.out.println("5. Cari Film");
        System.out.println("6. Keluar");
        System.out.print("Pilih menu: ");
    }

    /**
     * Membaca pilihan menu dari user.
     * Error handling: jika user mengetik huruf/karakter non-angka,
     * program tidak akan crash (InputMismatchException ditangkap),
     * melainkan mengembalikan nilai -1 (dianggap pilihan tidak valid).
     */
    private static int bacaPilihanMenu() {
        try {
            int pilihan = scanner.nextInt();
            scanner.nextLine(); // membersihkan newline setelah nextInt()
            return pilihan;
        } catch (InputMismatchException e) {
            scanner.nextLine(); // buang input yang salah agar tidak infinite loop
            System.out.println("Input harus berupa angka!");
            return -1;
        }
    }

    // ==================== FITUR 1: TAMBAH DATA ====================
    private static void tambahFilm() {
        System.out.println("\n-- Tambah Film Baru --");

        // Error handling: ID tidak boleh kosong dan tidak boleh duplikat
        String id;
        while (true) {
            System.out.print("ID Film (contoh: F003): ");
            id = scanner.nextLine().trim();

            if (id.isEmpty()) {
                System.out.println("ID tidak boleh kosong!");
                continue;
            }
            if (cariFilmById(id) != null) {
                System.out.println("ID sudah digunakan film lain! Gunakan ID lain.");
                continue;
            }
            break;
        }

        // Error handling: judul tidak boleh kosong
        String judul;
        do {
            System.out.print("Judul Film: ");
            judul = scanner.nextLine().trim();
            if (judul.isEmpty()) {
                System.out.println("Judul tidak boleh kosong!");
            }
        } while (judul.isEmpty());

        // Error handling: genre tidak boleh kosong
        String genre;
        do {
            System.out.print("Genre: ");
            genre = scanner.nextLine().trim();
            if (genre.isEmpty()) {
                System.out.println("Genre tidak boleh kosong!");
            }
        } while (genre.isEmpty());

        // Error handling: durasi harus angka dan lebih dari 0
        int durasi = bacaAngkaInt("Durasi (menit): ", true);

        // Error handling: sutradara tidak boleh kosong
        String sutradara;
        do {
            System.out.print("Sutradara: ");
            sutradara = scanner.nextLine().trim();
            if (sutradara.isEmpty()) {
                System.out.println("Nama sutradara tidak boleh kosong!");
            }
        } while (sutradara.isEmpty());

        // Error handling: harga tiket harus angka dan tidak boleh negatif
        double harga = bacaAngkaDouble("Harga Tiket: ", false);

        Film filmBaru = new Film(id, judul, genre, durasi, sutradara, harga);
        daftarFilm.add(filmBaru);
        System.out.println("Film berhasil ditambahkan!");
    }

    // ==================== FITUR 2: TAMPILKAN DATA ====================
    private static void tampilkanSemuaFilm() {
        System.out.println("\n-- Daftar Semua Film --");

        // Error handling: cek apakah list kosong sebelum looping
        if (daftarFilm.isEmpty()) {
            System.out.println("Belum ada data film.");
            return;
        }

        for (Film film : daftarFilm) {
            film.tampilkanInfo();
        }
    }

    // ==================== FITUR 3: UPDATE DATA ====================
    private static void updateFilm() {
        System.out.println("\n-- Update Film --");

        if (daftarFilm.isEmpty()) {
            System.out.println("Belum ada data film untuk diupdate.");
            return;
        }

        System.out.print("Masukkan ID film yang ingin diupdate: ");
        String id = scanner.nextLine().trim();

        Film film = cariFilmById(id);

        // Error handling: ID tidak ditemukan
        if (film == null) {
            System.out.println("Film dengan ID '" + id + "' tidak ditemukan!");
            return;
        }

        System.out.println("Data film ditemukan, silakan masukkan data baru.");
        System.out.println("(Kosongkan/tekan Enter jika tidak ingin mengubah field tersebut)");

        System.out.print("Judul baru [" + film.getJudul() + "]: ");
        String judul = scanner.nextLine().trim();
        if (!judul.isEmpty()) {
            film.setJudul(judul);
        }

        System.out.print("Genre baru [" + film.getGenre() + "]: ");
        String genre = scanner.nextLine().trim();
        if (!genre.isEmpty()) {
            film.setGenre(genre);
        }

        System.out.print("Durasi baru (menit) [" + film.getDurasi() + "]: ");
        String durasiStr = scanner.nextLine().trim();
        if (!durasiStr.isEmpty()) {
            try {
                int durasiBaru = Integer.parseInt(durasiStr);
                if (durasiBaru <= 0) {
                    // Error handling: nilai tidak valid, field lama dipertahankan
                    System.out.println("Durasi harus lebih dari 0. Durasi tidak diubah.");
                } else {
                    film.setDurasi(durasiBaru);
                }
            } catch (NumberFormatException e) {
                // Error handling: input bukan angka
                System.out.println("Input durasi tidak valid (bukan angka). Durasi tidak diubah.");
            }
        }

        System.out.print("Sutradara baru [" + film.getSutradara() + "]: ");
        String sutradara = scanner.nextLine().trim();
        if (!sutradara.isEmpty()) {
            film.setSutradara(sutradara);
        }

        System.out.print("Harga tiket baru [" + film.getHargaTiket() + "]: ");
        String hargaStr = scanner.nextLine().trim();
        if (!hargaStr.isEmpty()) {
            try {
                double hargaBaru = Double.parseDouble(hargaStr);
                if (hargaBaru < 0) {
                    System.out.println("Harga tidak boleh negatif. Harga tidak diubah.");
                } else {
                    film.setHargaTiket(hargaBaru);
                }
            } catch (NumberFormatException e) {
                System.out.println("Input harga tidak valid (bukan angka). Harga tidak diubah.");
            }
        }

        System.out.println("Data film berhasil diupdate!");
    }

    // ==================== FITUR 4: HAPUS DATA ====================
    private static void hapusFilm() {
        System.out.println("\n-- Hapus Film --");

        if (daftarFilm.isEmpty()) {
            System.out.println("Belum ada data film untuk dihapus.");
            return;
        }

        System.out.print("Masukkan ID film yang ingin dihapus: ");
        String id = scanner.nextLine().trim();

        Film film = cariFilmById(id);

        // Error handling: ID tidak ditemukan
        if (film == null) {
            System.out.println("Film dengan ID '" + id + "' tidak ditemukan!");
            return;
        }

        // Konfirmasi sebelum menghapus (mencegah penghapusan tidak sengaja)
        System.out.print("Yakin ingin menghapus '" + film.getJudul() + "'? (y/n): ");
        String konfirmasi = scanner.nextLine().trim().toLowerCase();

        if (konfirmasi.equals("y")) {
            daftarFilm.remove(film);
            System.out.println("Film berhasil dihapus!");
        } else {
            System.out.println("Penghapusan dibatalkan.");
        }
    }

    // ==================== FITUR 5: CARI DATA ====================
    private static void cariFilm() {
        System.out.println("\n-- Cari Film --");

        if (daftarFilm.isEmpty()) {
            System.out.println("Belum ada data film.");
            return;
        }

        System.out.print("Masukkan ID film yang dicari: ");
        String id = scanner.nextLine().trim();

        Film film = cariFilmById(id);

        // Error handling: ID tidak ditemukan
        if (film == null) {
            System.out.println("Film dengan ID '" + id + "' tidak ditemukan!");
        } else {
            System.out.println("Film ditemukan:");
            film.tampilkanInfo();
        }
    }

    // ==================== METHOD BANTUAN (HELPER) ====================

    /**
     * Mencari objek Film di dalam list berdasarkan ID.
     * Mengembalikan null jika tidak ditemukan (dipakai untuk error handling
     * di semua fitur yang butuh ID: update, hapus, cari, dan cek duplikasi saat tambah).
     */
    private static Film cariFilmById(String id) {
        for (Film film : daftarFilm) {
            if (film.getId().equalsIgnoreCase(id)) {
                return film;
            }
        }
        return null;
    }

    /**
     * Membaca input angka bulat (int) dari user dengan validasi:
     * - Harus berupa angka (menangkap NumberFormatException)
     * - Jika parameter wajibPositif = true, nilai harus lebih besar dari 0
     * Method ini akan terus meminta input sampai user memasukkan nilai yang valid.
     */
    private static int bacaAngkaInt(String label, boolean wajibPositif) {
        int nilai;
        while (true) {
            System.out.print(label);
            String input = scanner.nextLine().trim();
            try {
                nilai = Integer.parseInt(input);
                if (wajibPositif && nilai <= 0) {
                    System.out.println("Nilai harus lebih besar dari 0!");
                    continue;
                }
                break;
            } catch (NumberFormatException e) {
                System.out.println("Input tidak valid! Harap masukkan angka bulat.");
            }
        }
        return nilai;
    }

    /**
     * Membaca input angka desimal (double) dari user dengan validasi:
     * - Harus berupa angka (menangkap NumberFormatException)
     * - Jika parameter tidakBolehNegatif = false, nilai negatif ditolak
     */
    private static double bacaAngkaDouble(String label, boolean bolehNegatif) {
        double nilai;
        while (true) {
            System.out.print(label);
            String input = scanner.nextLine().trim();
            try {
                nilai = Double.parseDouble(input);
                if (!bolehNegatif && nilai < 0) {
                    System.out.println("Nilai tidak boleh negatif!");
                    continue;
                }
                break;
            } catch (NumberFormatException e) {
                System.out.println("Input tidak valid! Harap masukkan angka.");
            }
        }
        return nilai;
    }
}