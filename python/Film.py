class Film:
    """Class Film — merepresentasikan satu data film di bioskop."""

    def __init__(self, id_film = "", judul = "", genre = "",
                 durasi = 0, sutradara = "", harga_tiket = 0):
        self._id = id_film
        self._judul = judul
        self._genre = genre
        self._durasi = durasi
        self._sutradara = sutradara
        self._harga_tiket = harga_tiket

    # ==================== GETTER ====================
    # Getter digunakan untuk mengambil nilai tiap atribut dari luar class.
    def get_id(self) -> str:
        return self._id

    def get_judul(self) -> str:
        return self._judul

    def get_genre(self) -> str:
        return self._genre

    def get_durasi(self) -> int:
        return self._durasi

    def get_sutradara(self) -> str:
        return self._sutradara

    def get_harga_tiket(self) -> float:
        return self._harga_tiket

    # ==================== SETTER ====================
    # Setter digunakan agar fitur "Update Data" bisa mengubah nilai atribut
    # objek Film yang sudah ada tanpa perlu membuat objek baru.
    def set_judul(self, judul: str) -> None:
        self._judul = judul

    def set_genre(self, genre: str) -> None:
        self._genre = genre

    def set_durasi(self, durasi: int) -> None:
        self._durasi = durasi

    def set_sutradara(self, sutradara: str) -> None:
        self._sutradara = sutradara

    def set_harga_tiket(self, harga_tiket: float) -> None:
        self._harga_tiket = harga_tiket

    # ==================== METHOD TAMBAHAN ====================
    def tampilkan_info(self) -> None:
        """Menampilkan seluruh informasi film dalam format yang rapi."""
        # Format harga dengan pemisah ribuan gaya Indonesia, misal 45000 -> "45.000"
        harga_str = f"{self._harga_tiket:,.0f}".replace(",", ".")

        print("-" * 40)
        print(f"ID           : {self._id}")
        print(f"Judul        : {self._judul}")
        print(f"Genre        : {self._genre}")
        print(f"Durasi       : {self._durasi} menit")
        print(f"Sutradara    : {self._sutradara}")
        print(f"Harga Tiket  : Rp{harga_str}")
        print("-" * 40)