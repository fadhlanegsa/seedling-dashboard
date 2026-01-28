# Dashboard Stok Bibit Persemaian Indonesia

Sistem manajemen stok bibit untuk BPDAS (Balai Pengelolaan Daerah Aliran Sungai) di seluruh Indonesia.

## 🌳 Fitur Utama

### Halaman Publik (Tanpa Login)
- **Landing Page**: Pencarian BPDAS berdasarkan provinsi dan jenis bibit
- **Hasil Pencarian**: Daftar BPDAS dengan filter
- **Detail BPDAS**: Informasi lengkap dan stok bibit
- **Cara Mendapatkan Bibit**: Panduan lengkap

### Dashboard BPDAS
- Manajemen stok bibit (CRUD)
- Kelola permintaan bibit dari masyarakat
- Approve/Reject permintaan
- Profil BPDAS

### Dashboard Admin
- Analytics dashboard dengan grafik
- Manajemen BPDAS
- Manajemen jenis bibit (138 jenis)
- Manajemen stok nasional
- Manajemen permintaan
- Manajemen pengguna

### Fitur Tambahan
- Sistem permintaan bibit
- Notifikasi email
- Generate PDF surat persetujuan dengan QR code
- Import data dari CSV
- Responsive design

## 📋 Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache Web Server dengan mod_rewrite
- Extension PHP: PDO, PDO_MySQL, mbstring, openssl

## 🚀 Instalasi

### 1. Clone atau Download Project

```bash
# Clone repository
git clone [repository-url]

# Atau extract file ZIP ke direktori web server
# Contoh: C:/xampp/htdocs/seedling-dashboard
```

### 2. Konfigurasi Database

**a. Buat Database**

```sql
CREATE DATABASE seedling_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**b. Import Schema**

```bash
mysql -u root -p seedling_dashboard < database/schema.sql
```

**c. Import Sample Data**

```bash
mysql -u root -p seedling_dashboard < database/sample_data.sql
```

### 3. Konfigurasi Aplikasi

Edit file `config/config.php`:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'seedling_dashboard');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application URL
define('APP_URL', 'http://localhost/seedling-dashboard/public');
define('BASE_PATH', '/seedling-dashboard/public');

// Email Configuration (untuk notifikasi)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@seedling-dashboard.id');
```

### 4. Set Permissions

```bash
# Linux/Mac
chmod -R 755 seedling-dashboard/
chmod -R 777 seedling-dashboard/public/uploads/
chmod -R 777 seedling-dashboard/logs/

# Windows: Pastikan folder uploads dan logs dapat ditulis
```

### 5. Konfigurasi Virtual Host (Opsional)

**Apache Virtual Host:**

```apache
<VirtualHost *:80>
    ServerName seedling-dashboard.local
    DocumentRoot "C:/xampp/htdocs/seedling-dashboard/public"
    
    <Directory "C:/xampp/htdocs/seedling-dashboard/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Tambahkan ke `hosts` file:
```
127.0.0.1 seedling-dashboard.local
```

### 6. Akses Aplikasi

Buka browser dan akses:
```
http://localhost/seedling-dashboard/public
```

Atau jika menggunakan virtual host:
```
http://seedling-dashboard.local
```

## 👤 Akun Default

### Admin
- **Username**: admin
- **Password**: admin123
- **Email**: admin@seedling-dashboard.id

### BPDAS (10 akun tersedia)
- **Username**: bpdas_jabar
- **Password**: bpdas123
- **Email**: jabar@bpdas.id

### Public User (3 akun tersedia)
- **Username**: budi_santoso
- **Password**: user123
- **Email**: budi.santoso@email.com

**⚠️ PENTING: Ganti semua password default setelah instalasi!**

## 📁 Struktur Direktori

```
seedling-dashboard/
├── config/              # Konfigurasi aplikasi
│   ├── config.php
│   └── database.php
├── core/                # Core MVC classes
│   ├── Model.php
│   ├── View.php
│   └── Controller.php
├── models/              # Model classes
│   ├── User.php
│   ├── BPDAS.php
│   ├── Stock.php
│   ├── SeedlingType.php
│   ├── Request.php
│   └── Province.php
├── controllers/         # Controller classes
│   ├── HomeController.php
│   ├── AuthController.php
│   ├── BPDASController.php
│   ├── AdminController.php
│   └── ErrorController.php
├── views/               # View templates
│   ├── public/          # Public pages
│   ├── bpdas/           # BPDAS dashboard
│   ├── admin/           # Admin dashboard
│   ├── auth/            # Authentication pages
│   └── layouts/         # Layout templates
├── public/              # Public accessible files
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript files
│   ├── images/          # Images
│   ├── uploads/         # Uploaded files
│   └── index.php        # Entry point
├── utils/               # Utility classes
│   ├── PDFGenerator.php
│   ├── EmailSender.php
│   └── CSVImporter.php
├── database/            # Database files
│   ├── schema.sql
│   └── sample_data.sql
└── logs/                # Application logs
```

## 🔧 Import Data dari CSV

Gunakan script CSV importer untuk import data dari Google Sheets:

```bash
php utils/CSVImporter.php import path/to/your/file.csv
```

Format CSV yang didukung:
- BPDAS data
- Stock data
- Seedling types

## 📊 Teknologi yang Digunakan

- **Backend**: PHP 7.4+ (MVC Pattern)
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Libraries**:
  - Chart.js (untuk grafik)
  - DataTables.js (untuk tabel interaktif)
  - FPDF/TCPDF (untuk generate PDF)
  - PHPMailer (untuk email)

## 🔒 Keamanan

- Password hashing dengan `password_hash()`
- Prepared statements untuk mencegah SQL injection
- CSRF token untuk semua form
- XSS protection dengan `htmlspecialchars()`
- Input validation dan sanitization
- Session security

## 📧 Konfigurasi Email

Untuk mengaktifkan notifikasi email:

1. Gunakan Gmail dengan App Password:
   - Buka Google Account Settings
   - Security → 2-Step Verification
   - App passwords → Generate new password
   - Copy password ke `config/config.php`

2. Atau gunakan SMTP server lain sesuai kebutuhan

## 🐛 Troubleshooting

### Error: Database connection failed
- Pastikan MySQL service berjalan
- Cek kredensial database di `config/config.php`
- Pastikan database sudah dibuat

### Error: 404 Not Found
- Pastikan mod_rewrite Apache aktif
- Cek file `.htaccess` ada di root dan public folder
- Periksa BASE_PATH di `config/config.php`

### Error: Permission denied
- Set permission yang benar untuk folder uploads dan logs
- Linux/Mac: `chmod -R 777 public/uploads logs`

### Email tidak terkirim
- Cek konfigurasi SMTP di `config/config.php`
- Pastikan firewall tidak memblokir port SMTP
- Cek log error di folder `logs/`

## 📝 Lisensi

Project ini dibuat untuk keperluan manajemen stok bibit BPDAS di Indonesia.

## 👥 Kontributor

- Developer: [Your Name]
- Organization: Kementerian Lingkungan Hidup dan Kehutanan

## 📞 Kontak

Untuk pertanyaan atau dukungan:
- Email: support@seedling-dashboard.id
- Website: https://seedling-dashboard.id

---

**Hijau Indonesia Dimulai dari Sini! 🌳**
