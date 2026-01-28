<?php
/**
 * How To Get Seedlings Page
 * Step-by-step guide for getting free seedlings
 */
?>

<div class="container" style="padding: 3rem 0;">
    <h1>Cara Mendapatkan Bibit Gratis</h1>
    <p class="text-light">Panduan lengkap untuk mendapatkan bibit gratis dari BPDAS</p>

    <!-- Steps -->
    <div class="row mt-4">
        <div class="col-12">
            <!-- Step 1 -->
            <div class="card mb-3">
                <div class="card-header" style="background: var(--primary-color); color: white;">
                    <h3><i class="fas fa-user-plus"></i> Langkah 1: Registrasi/Login</h3>
                </div>
                <div class="card-body">
                    <p>Untuk mengajukan permintaan bibit, Anda harus memiliki akun terlebih dahulu.</p>
                    <ul>
                        <li>Klik tombol "Daftar" di halaman utama</li>
                        <li>Isi formulir registrasi dengan data lengkap (Nama, Email, NIK, No. Telepon)</li>
                        <li>Setelah berhasil mendaftar, login menggunakan username dan password Anda</li>
                    </ul>
                    <?php if (!isLoggedIn()): ?>
                        <a href="<?= url('auth/register') ?>" class="btn btn-primary">Daftar Sekarang</a>
                        <a href="<?= url('auth/login') ?>" class="btn btn-outline">Sudah Punya Akun? Login</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="card mb-3">
                <div class="card-header" style="background: var(--primary-color); color: white;">
                    <h3><i class="fas fa-search"></i> Langkah 2: Cari BPDAS Terdekat</h3>
                </div>
                <div class="card-body">
                    <p>Temukan BPDAS di provinsi Anda yang memiliki stok bibit yang dibutuhkan.</p>
                    <ul>
                        <li>Gunakan fitur pencarian di halaman utama</li>
                        <li>Pilih provinsi dan jenis bibit yang Anda inginkan</li>
                        <li>Lihat detail BPDAS untuk melihat stok yang tersedia</li>
                        <li>Catat informasi kontak BPDAS untuk keperluan pengambilan bibit</li>
                    </ul>
                    <a href="<?= url('home/search') ?>" class="btn btn-primary">Cari BPDAS</a>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="card mb-3">
                <div class="card-header" style="background: var(--primary-color); color: white;">
                    <h3><i class="fas fa-file-alt"></i> Langkah 3: Ajukan Permintaan</h3>
                </div>
                <div class="card-body">
                    <p>Isi formulir permintaan bibit dengan lengkap dan benar.</p>
                    <ul>
                        <li>Login ke dashboard Anda</li>
                        <li>Klik menu "Ajukan Permintaan"</li>
                        <li>Pilih provinsi dan BPDAS tujuan</li>
                        <li>Pilih jenis bibit dan jumlah yang dibutuhkan</li>
                        <li>Jelaskan tujuan penggunaan bibit (penghijauan, reboisasi, dll)</li>
                        <li>Isi luas lahan yang akan ditanami (jika ada)</li>
                        <li>Submit permintaan</li>
                    </ul>
                    <?php if (isLoggedIn() && hasRole('public')): ?>
                        <a href="<?= url('public/request-form') ?>" class="btn btn-primary">Ajukan Permintaan</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="card mb-3">
                <div class="card-header" style="background: var(--primary-color); color: white;">
                    <h3><i class="fas fa-clock"></i> Langkah 4: Tunggu Persetujuan</h3>
                </div>
                <div class="card-body">
                    <p>BPDAS akan meninjau permintaan Anda dan memberikan keputusan.</p>
                    <ul>
                        <li>Proses review biasanya memakan waktu 1-3 hari kerja</li>
                        <li>Anda akan menerima notifikasi email tentang status permintaan</li>
                        <li>Cek dashboard Anda secara berkala untuk melihat status terkini</li>
                        <li>Jika disetujui, Anda akan mendapatkan surat persetujuan</li>
                        <li>Jika ditolak, Anda dapat mengajukan permintaan baru dengan penyesuaian</li>
                    </ul>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="card mb-3">
                <div class="card-header" style="background: var(--success-color); color: white;">
                    <h3><i class="fas fa-download"></i> Langkah 5: Download Surat Persetujuan</h3>
                </div>
                <div class="card-body">
                    <p>Setelah permintaan disetujui, download surat persetujuan dalam format PDF.</p>
                    <ul>
                        <li>Login ke dashboard Anda</li>
                        <li>Buka menu "Permintaan Saya"</li>
                        <li>Klik permintaan yang sudah disetujui</li>
                        <li>Download surat persetujuan (PDF dengan QR Code)</li>
                        <li>Cetak surat persetujuan tersebut</li>
                    </ul>
                </div>
            </div>

            <!-- Step 6 -->
            <div class="card mb-3">
                <div class="card-header" style="background: var(--success-color); color: white;">
                    <h3><i class="fas fa-seedling"></i> Langkah 6: Ambil Bibit di BPDAS</h3>
                </div>
                <div class="card-body">
                    <p>Kunjungi BPDAS untuk mengambil bibit sesuai surat persetujuan.</p>
                    <ul>
                        <li>Bawa surat persetujuan yang sudah dicetak</li>
                        <li>Bawa KTP asli untuk verifikasi identitas</li>
                        <li>Datang pada jam kerja BPDAS (Senin-Jumat, 08:00-16:00)</li>
                        <li>Tunjukkan surat persetujuan dan KTP kepada petugas</li>
                        <li>Petugas akan memverifikasi dengan QR Code pada surat</li>
                        <li>Terima bibit sesuai jumlah yang disetujui</li>
                        <li>Tanda tangani berita acara serah terima bibit</li>
                    </ul>
                    <div class="alert alert-warning">
                        <strong>Penting!</strong> Surat persetujuan berlaku 30 hari sejak tanggal persetujuan. Pastikan Anda mengambil bibit sebelum masa berlaku habis.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Requirements -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Persyaratan Umum</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <h4>Dokumen yang Diperlukan:</h4>
                    <ul>
                        <li>KTP asli dan fotokopi</li>
                        <li>Surat persetujuan dari sistem (sudah didownload dan dicetak)</li>
                        <li>Surat pengantar dari RT/RW (untuk jumlah besar)</li>
                        <li>Proposal kegiatan (untuk organisasi/lembaga)</li>
                    </ul>
                </div>
                <div class="col-6">
                    <h4>Ketentuan:</h4>
                    <ul>
                        <li>Bibit gratis hanya untuk tujuan penghijauan/reboisasi</li>
                        <li>Tidak diperbolehkan untuk diperjualbelikan</li>
                        <li>Wajib melaporkan perkembangan penanaman (opsional)</li>
                        <li>Maksimal permintaan per orang: 1000 batang/tahun</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Pertanyaan yang Sering Diajukan (FAQ)</h3>
        </div>
        <div class="card-body">
            <h5>Q: Apakah bibit benar-benar gratis?</h5>
            <p>A: Ya, bibit dari BPDAS diberikan secara gratis untuk tujuan penghijauan dan reboisasi.</p>

            <h5>Q: Berapa lama proses persetujuan?</h5>
            <p>A: Biasanya 1-3 hari kerja, tergantung ketersediaan stok dan kelengkapan data.</p>

            <h5>Q: Apa yang harus dilakukan jika permintaan ditolak?</h5>
            <p>A: Anda dapat mengajukan permintaan baru dengan menyesuaikan jumlah atau jenis bibit sesuai ketersediaan stok.</p>

            <h5>Q: Apakah bisa mengambil bibit untuk orang lain?</h5>
            <p>A: Tidak, pengambilan bibit harus dilakukan oleh pemohon sendiri dengan membawa KTP asli.</p>

            <h5>Q: Bagaimana jika surat persetujuan hilang?</h5>
            <p>A: Anda dapat mendownload ulang surat persetujuan dari dashboard Anda.</p>
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center mt-4">
        <?php if (!isLoggedIn()): ?>
            <a href="<?= url('auth/register') ?>" class="btn btn-primary btn-lg">Mulai Sekarang - Daftar Gratis</a>
        <?php else: ?>
            <a href="<?= url('public/request-form') ?>" class="btn btn-primary btn-lg">Ajukan Permintaan Bibit</a>
        <?php endif; ?>
    </div>
</div>
