# Sistem Tracer Study & Rekomendasi Karir

Sistem informasi untuk pelacakan alumni dan rekomendasi karir berbasis web yang dikhususkan untuk Sekolah Menengah Kejuruan (SMK). Sistem ini menggunakan algoritma Simple Additive Weighting (SAW) untuk memberikan rekomendasi pekerjaan yang sesuai berdasarkan hasil kuesioner siswa.

## 📋 Fitur Utama

-   **Manajemen Alumni**: Pencatatan dan pelacakan data alumni
-   **Tracer Study**: Survei pelacakan status alumni setelah lulus
-   **Sistem Rekomendasi**: Algoritma SAW untuk rekomendasi pekerjaan
-   **Dashboard Multi-Role**: Dashboard untuk Superadmin, Operator, Guru, Kepala Sekolah, dan Siswa
-   **Visualisasi Data**: Chart dan grafik untuk analisis data tracer study
-   **Export/Import**: Fitur export data ke Excel/PDF dan import data siswa
-   **Blog & Artikel**: Sistem manajemen konten untuk informasi karir
-   **Kuesioner**: Sistem kuesioner adaptif untuk profiling siswa

## 🛠️ Teknologi yang Digunakan

-   **Backend**: Laravel 10.x
-   **Frontend**: Bootstrap 5, Chart.js
-   **Database**: MySQL
-   **Server**: PHP 8.1+
-   **Package Manager**: Composer, NPM
-   **PDF Generation**: DomPDF
-   **Excel Processing**: Maatwebsite Excel
-   **Real-time**: WebSocket (Ratchet)

## 📦 Persyaratan Sistem

### Persyaratan Minimum

-   **PHP**: >= 8.1
-   **Composer**: >= 2.0
-   **Node.js**: >= 16.x
-   **NPM**: >= 8.x
-   **MySQL**: >= 5.7 atau MariaDB >= 10.2
-   **Web Server**: Apache/Nginx
-   **Memory**: 512MB RAM
-   **Storage**: 1GB disk space

### Extension PHP yang Diperlukan

-   BCMath PHP Extension
-   Ctype PHP Extension
-   Fileinfo PHP Extension
-   JSON PHP Extension
-   Mbstring PHP Extension
-   OpenSSL PHP Extension
-   PDO PHP Extension
-   Tokenizer PHP Extension
-   XML PHP Extension
-   ZIP Extension

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/bowoxdaily/Sistemrekomendasi.git
cd Sistemrekomendasi
```

### 2. Install Dependencies PHP

```bash
composer install
```

### 3. Install Dependencies Node.js

```bash
npm install
```

### 4. Konfigurasi Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skripsi
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Buat Database

Buat database MySQL dengan nama yang sesuai (contoh: `skripsi`):

```sql
CREATE DATABASE skripsi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Migrasi Database

```bash
# Jalankan migrasi
php artisan migrate

# Jalankan seeder (opsional)
php artisan db:seed
```

### 8. Setup Storage

```bash
# Buat symbolic link untuk storage
php artisan storage:link

# Set permission untuk storage dan bootstrap/cache (Linux/Mac)
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 9. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 10. Jalankan Server

```bash
# Server development Laravel
php artisan serve

# Atau gunakan task yang tersedia
# Jalankan di VS Code: Run Task > Run Laravel Development Server
```

## 🔧 Konfigurasi

### Konfigurasi Aplikasi

Sesuaikan pengaturan di file `.env`:

```env
APP_NAME="Sistem Tracer Study"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skripsi
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration (untuk notifikasi)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Default User Accounts

Setelah menjalankan seeder, akun default yang tersedia:

| Role           | Email                  | Password |
| -------------- | ---------------------- | -------- |
| Superadmin     | superadmin@example.com | password |
| Operator       | operator@example.com   | password |
| Guru           | guru@example.com       | password |
| Kepala Sekolah | kepsek@example.com     | password |

## 📖 Cara Penggunaan

### 1. Login ke Sistem

Buka browser dan akses `http://localhost:8000`

### 2. Dashboard Roles

-   **Superadmin**: Manajemen penuh sistem, user management, settings
-   **Operator**: Input data siswa, manajemen tracer study, laporan
-   **Guru**: Lihat data siswa, monitoring tracer study
-   **Kepala Sekolah**: Dashboard eksekutif, laporan analitik
-   **Siswa**: Isi kuesioner, lihat rekomendasi karir

### 3. Input Data Alumni

1. Login sebagai Operator
2. Menu "Data Siswa" → "Tambah Siswa"
3. Isi data lengkap siswa
4. Import data massal melalui Excel (template tersedia)

### 4. Sistem Rekomendasi

1. Siswa login dan mengisi kuesioner
2. Sistem menganalisis jawaban dengan algoritma SAW
3. Rekomendasi pekerjaan ditampilkan berdasarkan kecocokan

## 📊 Struktur Database

### Tabel Utama

-   `users` - Data pengguna sistem
-   `students` - Data siswa/alumni
-   `jurusans` - Data jurusan
-   `data_kerjas` - Data pekerjaan alumni
-   `data_kuliahs` - Data pendidikan lanjut alumni
-   `questionnaires` - Master kuesioner
-   `questionnaire_questions` - Soal kuesioner
-   `questionnaire_responses` - Respon kuesioner
-   `job_recommendations` - Data pekerjaan untuk rekomendasi

## 🔍 Troubleshooting

### Error Umum dan Solusi

**1. Class not found error**

```bash
composer dump-autoload
```

**2. Permission denied pada storage**

```bash
# Linux/Mac
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Windows
# Pastikan folder memiliki write permission
```

**3. Key not set error**

```bash
php artisan key:generate
```

**4. Database connection error**

-   Pastikan MySQL service berjalan
-   Cek konfigurasi database di `.env`
-   Pastikan database sudah dibuat

**5. NPM installation error**

```bash
# Clear cache
npm cache clean --force
rm -rf node_modules
npm install
```

## 🧪 Testing

```bash
# Jalankan test
php artisan test

# Test dengan coverage
php artisan test --coverage
```

## 📁 Struktur Folder

```
app/
├── Console/           # Artisan commands
├── Exports/          # Excel export classes
├── Http/
│   ├── Controllers/  # Controller files
│   └── Middleware/   # Custom middleware
├── Imports/          # Excel import classes
├── Models/           # Eloquent models
├── Notifications/    # Email notifications
├── Providers/        # Service providers
└── Services/         # Business logic services

resources/
├── views/            # Blade templates
│   ├── dashboard/    # Dashboard views per role
│   ├── layout/       # Layout templates
│   └── auth/         # Authentication views
├── css/              # Stylesheets
└── js/               # JavaScript files

database/
├── migrations/       # Database migrations
├── seeders/          # Database seeders
└── factories/        # Model factories

public/
├── admin/            # Admin template assets
├── uploads/          # File uploads
└── storage/          # Symlink to storage
```

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/amazing-feature`)
3. Commit perubahan (`git commit -m 'Add amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buat Pull Request

## 📝 Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

## 👥 Tim Pengembang

-   **Developer**: [bowoxdaily](https://github.com/bowoxdaily)
-   **Project**: Sistem Rekomendasi Karir untuk SMK

## 📞 Support

Jika mengalami masalah atau membutuhkan bantuan:

1. Cek [Issues](https://github.com/bowoxdaily/Sistemrekomendasi/issues) yang sudah ada
2. Buat issue baru dengan detail masalah
3. Sertakan log error dan langkah yang sudah dicoba

## 🔄 Changelog

### v1.0.0 (Current)

-   ✅ Sistem tracer study lengkap
-   ✅ Algoritma rekomendasi SAW
-   ✅ Dashboard multi-role
-   ✅ Export/Import Excel
-   ✅ Visualisasi data dengan Chart.js
-   ✅ Sistem blog dan artikel
-   ✅ Responsive design

---

**Made with ❤️ for Indonesian Vocational Education**
