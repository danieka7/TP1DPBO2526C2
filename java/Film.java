/**
 * Class Film
 * -----------
 * Class ini merepresentasikan data sebuah film yang tayang di bioskop.
 * Setiap objek Film menyimpan informasi berupa id, judul, genre, durasi,
 * sutradara, dan harga tiket.
 */
public class Film {

    // ==================== ATRIBUT ====================
    private String id;          // Identifier unik, contoh: "F001"
    private String judul;       // Judul film
    private String genre;       // Genre film, contoh: "Action", "Horror"
    private int durasi;         // Durasi film dalam satuan menit
    private String sutradara;   // Nama sutradara film
    private double hargaTiket;  // Harga tiket film dalam Rupiah

    // ==================== CONSTRUCTOR ====================
    //  Constructor untuk membuat objek Film baru dengan seluruh atributnya.
    public Film(String id, String judul, String genre, int durasi, String sutradara, double hargaTiket) {
        this.id = id;
        this.judul = judul;
        this.genre = genre;
        this.durasi = durasi;
        this.sutradara = sutradara;
        this.hargaTiket = hargaTiket;
    }

    // ==================== GETTER ====================
    // Getter digunakan untuk mengambil nilai tiap atribut dari luar class.
    public String getId() {
        return id;
    }

    public String getJudul() {
        return judul;
    }

    public String getGenre() {
        return genre;
    }

    public int getDurasi() {
        return durasi;
    }

    public String getSutradara() {
        return sutradara;
    }

    public double getHargaTiket() {
        return hargaTiket;
    }

    // ==================== SETTER ====================
    // Setter digunakan agar fitur "Update Data" bisa mengubah nilai atribut
    // objek Film yang sudah ada tanpa perlu membuat objek baru.
    public void setJudul(String judul) {
        this.judul = judul;
    }

    public void setGenre(String genre) {
        this.genre = genre;
    }

    public void setDurasi(int durasi) {
        this.durasi = durasi;
    }

    public void setSutradara(String sutradara) {
        this.sutradara = sutradara;
    }

    public void setHargaTiket(double hargaTiket) {
        this.hargaTiket = hargaTiket;
    }

    // ==================== METHOD TAMBAHAN ====================
    /**
     * Menampilkan seluruh informasi film dalam format yang rapi.
     * Dipanggil saat menu "Tampilkan Data" atau "Cari Data".
     */
    public void tampilkanInfo() {
        System.out.println("----------------------------------------");
        System.out.println("ID           : " + id);
        System.out.println("Judul        : " + judul);
        System.out.println("Genre        : " + genre);
        System.out.println("Durasi       : " + durasi + " menit");
        System.out.println("Sutradara    : " + sutradara);
        System.out.println("Harga Tiket  : Rp" + String.format("%,.0f", hargaTiket));
        System.out.println("----------------------------------------");
    }
}