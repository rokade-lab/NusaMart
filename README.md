# NusaMart — lab peserta

Simulasi toko daring dengan storefront, katalog, checkout, antrean pesanan, dan pelaporan. Pembayaran memakai data dummy.

Repositori ini adalah titik awal latihan secure coding. Fork repositori, jalankan secara lokal, temukan masalah, lalu ajukan perbaikan beserta pengujian. Aplikasi sengaja memuat kelemahan untuk latihan; gunakan data dummy dan jalankan hanya di komputer lokal. Port Docker dibatasi ke loopback.

## Menjalankan lokal

Prasyarat: Docker Engine atau Docker Desktop dengan Compose v2, dan Git. Python 3.10+ hanya diperlukan untuk latihan classifier. Build pertama membutuhkan internet untuk mengunduh image dan dependency.

```sh
git clone <URL-fork-Anda>
cd <folder-repositori>
docker compose up -d
```

Perintah pertama membangun image, membuat database, menjalankan migrasi, dan mengisi data dummy secara otomatis. Tunggu sampai semua service berstatus `healthy` (`docker compose ps`); proxy dapat membutuhkan sekitar dua menit.

Buka **http://127.0.0.1:19180**. Akun dummy tercantum di [docs/AKUN-LAB.md](docs/AKUN-LAB.md).

Port bawaan: proxy 19180, storefront 19181, katalog 19182, checkout 19183, admin proxy 19184. Jika port sudah digunakan, salin `.env.example` menjadi `.env`, ubah nilainya, lalu jalankan kembali `docker compose up -d`.

## Perintah harian

```sh
docker compose ps
docker compose logs --tail 100
docker compose logs -f storefront
docker compose down
docker compose up -d --build
```

`up -d --build` membangun ulang image agar perubahan source ikut dijalankan. `down` menghentikan container tanpa menghapus data.

Data database dan proxy disimpan di folder `data/` pada working directory ini. Untuk menghapus seluruh data latihan dan mengisi ulang data dummy:

```sh
docker compose down
rm -rf data
docker compose up -d
```

Jika `rm -rf data` ditolak karena izin file container, jalankan `docker run --rm -v "$PWD/data:/d" alpine sh -c 'rm -rf /d/*'` lalu hapus folder `data`.

Dockerfile tersedia di `docker/` dan runtime didefinisikan di `compose.yaml`. Secret dummy lokal berada di `docker/lab-secrets/`. Jangan commit `.env`, `data/`, cookie, token, atau hasil tangkapan proxy.

## Pengerjaan dan pengumpulan

1. Baca [panduan peserta](docs/PANDUAN-PESERTA.md) dan jalankan alur aplikasi normal.
2. Buat branch `fix/<topik>` pada fork Anda.
3. Catat masalah dan bukti awal dengan data dummy.
4. Perbaiki source atau konfigurasi, tambahkan pengujian yang membuktikan perbaikan, lalu uji ulang alur sah.
5. Buka pull request di fork Anda sesuai instruksi kelas; isi [template laporan](docs/LAPORAN.md).

Sertakan langkah reproduksi, dampak, alasan perubahan, cara menguji, dan risiko tersisa. Jangan sertakan data sensitif atau folder `data/`.
