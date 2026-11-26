# Panduan peserta NusaMart

Inventarisasikan layanan yang memiliki port maupun worker internal. Cocokkan versi runtime, image, konfigurasi, dan dependensinya. Baca advisory simulasi di folder ini sebagai aturan skenario latihan, bukan klaim CVE aktual. Usulkan perubahan dan rollback; buktikan login → katalog → keranjang → checkout → pesanan selesai → laporan tetap bekerja.

Mulai dengan memetakan fitur, identitas/peran, objek data, dan hasil yang diharapkan. Cari masalah melalui pengamatan runtime dan pembacaan source, lalu pisahkan bukti yang telah teruji dari dugaan.

Setiap perbaikan harus memiliki reproduksi sebelum perubahan, alasan teknis, dan verifikasi sesudah perubahan. Jangan hanya menyembunyikan tombol atau menonaktifkan seluruh fitur. Pertahankan fungsi bisnis dan uji penolakan tanpa merusak data.

## Menjalankan lab

Dari root repositori jalankan `docker compose up -d`, tunggu semua service `healthy` (`docker compose ps`), lalu buka http://127.0.0.1:19180. Akun dummy ada di [AKUN-LAB.md](AKUN-LAB.md). Gunakan `docker compose logs <service>` untuk melihat log, dan `docker compose up -d --build` setelah mengubah source. Reset data: `docker compose down`, hapus folder `data/`, lalu `docker compose up -d`.

Gunakan instance lokal milik sendiri. Simpan bukti yang sudah diredaksi dan laporkan melalui pull request sesuai arahan kelas.

## Latihan classifier

Lengkapi `nusamart_classifier/rules.py` dengan aturan advisory simulasi. CLI dan validasi input sudah tersedia. Uji struktur input/output publik:

```sh
python3 -m unittest discover -s tests -p 'classifier_public.py'
python3 -m nusamart_classifier --inventory docs/public-fixtures/inventory.csv --sbom docs/public-fixtures/sbom.json --advisory docs/advisory.simulation.json --as-of 2026-11-07T11:00:00Z
```

Timestamp di atas hanya untuk fixture contoh, bukan observasi runtime saat ini. Tambahkan pengujian keputusan Anda sendiri. Exit 0 berarti pemrosesan berhasil, bukan semua komponen aman. Gunakan template inventory, triage, change plan, dan test report di `templates/`.
