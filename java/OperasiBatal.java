/**
 * Class OperasiDibatalkanException
 * ----------------------------------
 * Exception khusus (custom exception) yang dilempar ketika user mengetik
 * "batal" di salah satu prompt input, saat sedang berada di dalam fitur
 * Tambah/Update/Hapus/Cari.
 *
 * Dengan exception ini, kita bisa langsung menghentikan proses input yang
 * sedang berjalan (walaupun sudah beberapa field terisi) dan kembali ke
 * tampilan menu utama, tanpa perlu banyak percabangan if-else manual.
 *
 * Extends RuntimeException agar tidak perlu dideklarasikan dengan "throws"
 * di setiap method (unchecked exception), karena sifatnya seperti kontrol
 * alur program, bukan error sistem yang sesungguhnya.
 */

public class OperasiBatal extends RuntimeException {

    public OperasiBatal() {
        super("Operasi dibatalkan oleh pengguna.");
    }
}