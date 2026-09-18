"""
Module operasi_dibatalkan_exception.py
----------------------------------------
Berisi class OperasiDibatalkanException, exception khusus yang dilempar
ketika user mengetik kata kunci "batal" saat diminta memasukkan data
di prompt manapun.

Dengan exception ini, proses input yang sedang berjalan (walaupun sudah
beberapa field terisi) bisa langsung dihentikan dan kembali ke tampilan
menu utama, cukup ditangkap satu kali saja di loop menu utama (main.py),
tanpa perlu pengecekan berulang di tiap fitur (Tambah, Update, Hapus, Cari).
"""


class OperasiDibatalkanException(Exception):
    """Exception yang menandakan user membatalkan operasi yang sedang berjalan."""

    def __init__(self, pesan: str = "Operasi dibatalkan oleh pengguna."):
        super().__init__(pesan)