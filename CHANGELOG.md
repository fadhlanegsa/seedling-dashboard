# Changelog / Log Update Aplikasi

Semua update dan perbaikan pada aplikasi akan dicatat di sini.

## [2026-03-10] - Update Fitur Hapus Stok Bibit
### Fitur Baru (Pembaruan Terbaru)
- **Profil Operator Persemaian**: Operator Persemaian sekarang dapat mengedit profil mereka sendiri, termasuk nama, email, username, no telepon, dan mengganti password, melalui menu "Profil" di sidebar dasbor.
- **Filter Pencarian Persemaian**: Halaman "Cari Stok Bibit" publik (`/home/search`) kini dilengkapi dengan dropdown filter **Persemaian**, memungkinkan masyarakat mencari bibit secara lebih akurat pada tingkat Persemaian spesifik, tidak hanya per BPDAS tingkat provinsi.
- Tombol **Hapus** pada halaman Kelola Stok di dashboard **Operator Persemaian** (`views/operator/stock/index.php`).
- Fungsi backend `deleteStock` pada `OperatorController.php` untuk memvalidasi dan menghapus data stok bibit spesifik milik persemaian yang bersangkutan.

### Perbaikan Bug (Bugfixes)
- **Perbaikan Filter BPDAS**: Mengembalikan filter `BPDAS` pada halaman pencarian stok yang sebelumnya tertimpa oleh filter Persemaian, kini pengguna dapat memfilter berdasarkan BPDAS dan/atau Persemaian sekaligus.
- **Perbaikan Profil Operator**: Memperbaiki nama Provinsi yang sebelumnya kosong (tidak muncul) pada bagian Informasi Persemaian di menu Profil Operator.
- Memperbaiki error duplikasi data stok (Integrity constraint violation: 1062 Duplicate entry '...-...' for key 'unique_stock') ketika Operator Persemaian mencoba menambahkan bibit jenis yang sama dengan Persemaian lain dalam naungan satu BPDAS yang sama. Index constraint dasar di database `stock` telah disesuaikan agar dihitung per-persemaian (`nursery_id`), bukan per-BPDAS (`bpdas_id`).

### Info Tambahan
- Fitur hapus stok bibit pada dashboard **BPDAS** sudah tersedia dan dipastikan berjalan dengan baik (melalui `BPDASController.php` dan `views/bpdas/stock.php`).
- Update ini merespons masukan dari BPDAS & BPTH agar stok yang kosong/salah input (bernilai 0) dapat dihapus sehingga tidak membingungkan masyarakat saat melihat ketersediaan bibit.
