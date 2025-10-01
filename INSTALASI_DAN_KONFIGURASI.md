# 🚀 PANDUAN INSTALASI DAN KONFIGURASI SIMPEDAS

## 📋 SYARAT SISTEM

### Server Requirements:

-   **PHP**: 8.2 atau lebih tinggi
-   **Composer**: 2.0 atau lebih tinggi
-   **Database**: MySQL 8.0 atau PostgreSQL 13
-   **Web Server**: Apache 2.4 atau Nginx 1.18
-   **Memory**: Minimal 512MB RAM
-   **Storage**: Minimal 1GB ruang kosong

### Browser Requirements:

-   **Chrome**: Versi 90 atau lebih tinggi
-   **Firefox**: Versi 88 atau lebih tinggi
-   **Safari**: Versi 14 atau lebih tinggi
-   **Edge**: Versi 90 atau lebih tinggi

---

## 🔧 INSTALASI LARAVEL SIMPEDAS

### 1. Persiapan Environment

```bash
# Clone repository
git clone [repository-url] simpedas
cd simpedas

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 2. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simpedas
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Migrasi Database

```bash
# Run migrations
php artisan migrate

# Seed initial data
php artisan db:seed
```

### 4. Konfigurasi Storage

```bash
# Create storage link
php artisan storage:link

# Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 5. Konfigurasi Mail (Opsional)

Untuk fitur email, edit `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="Simpedas System"
```

---

## 👥 SETUP USER AWAL

### 1. Membuat Admin Dinas

```bash
php artisan tinker
```

```php
use App\Models\User;
use Spatie\Permission\Models\Role;

// Create admin dinas user
$adminDinas = User::create([
    'name' => 'Admin Dinas',
    'email' => 'admin@dinas.com',
    'password' => bcrypt('password123'),
    'role' => 'admin_dinas'
]);

// Assign role
$adminDinas->assignRole('admin_dinas');
```

### 2. Membuat Admin Sekolah

```php
use App\Models\School;

// Create school first
$school = School::create([
    'name' => 'SD Negeri 1',
    'npsn' => '12345678',
    'address' => 'Jl. Pendidikan No. 1',
    'education_level' => 'SD',
    'status' => 'Negeri',
    'region' => 'Jakarta Pusat',
    'headmaster' => 'Dr. Kepala Sekolah'
]);

// Create admin sekolah user
$adminSekolah = User::create([
    'name' => 'Admin Sekolah',
    'email' => 'admin@sekolah.com',
    'password' => bcrypt('password123'),
    'role' => 'admin_sekolah',
    'school_id' => $school->id
]);

$adminSekolah->assignRole('admin_sekolah');
```

### 3. Membuat User Guru

```php
use App\Models\Teacher;

// Create teacher record
$teacher = Teacher::create([
    'full_name' => 'Guru Contoh',
    'nuptk' => '1234567890123456',
    'school_id' => $school->id,
    'subjects' => 'Matematika',
    'employment_status' => 'PNS'
]);

// Create guru user
$guru = User::create([
    'name' => 'Guru Contoh',
    'email' => 'guru@sekolah.com',
    'password' => bcrypt('password123'),
    'role' => 'guru'
]);

$guru->assignRole('guru');
```

---

## ⚙️ KONFIGURASI LANJUTAN

### 1. Konfigurasi File Upload

Pastikan direktori `storage/app/public` dapat diakses:

```bash
# Set permissions
chown -R www-data:www-data storage
chmod -R 775 storage
```

### 2. Konfigurasi Excel Import/Export

Pastikan extension PHP yang diperlukan terinstall:

```bash
# Install PHP extensions
sudo apt-get install php-mbstring php-xml php-zip
```

### 3. Konfigurasi Queue (Opsional)

Untuk processing import yang besar, aktifkan queue:

```bash
# Install Redis (recommended)
sudo apt-get install redis-server

# Or use database queue
php artisan queue:table
php artisan migrate

# Start queue worker
php artisan queue:work
```

### 4. Konfigurasi Cache

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔐 KEAMANAN SISTEM

### 1. Konfigurasi HTTPS

Edit `.env` untuk production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Force HTTPS
FORCE_HTTPS=true
```

### 2. Konfigurasi Session Security

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### 3. Backup Database

Buat script backup otomatis:

```bash
#!/bin/bash
# backup.sh
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p password simpedas > backup_$DATE.sql
```

---

## 🧪 TESTING SISTEM

### 1. Test Import Excel

1. Download template dari sistem
2. Isi beberapa data contoh
3. Upload kembali untuk test import
4. Verifikasi data masuk dengan benar

### 2. Test User Roles

1. Login dengan admin dinas
2. Test akses semua menu
3. Login dengan admin sekolah
4. Verifikasi akses terbatas
5. Login dengan guru
6. Test akses menu guru

### 3. Test Upload File

1. Test upload foto profil
2. Test upload dokumen
3. Test upload sertifikat
4. Verifikasi file tersimpan dengan benar

---

## 📊 MONITORING DAN MAINTENANCE

### 1. Log Monitoring

```bash
# Monitor Laravel logs
tail -f storage/logs/laravel.log

# Monitor web server logs
tail -f /var/log/apache2/error.log
```

### 2. Database Maintenance

```sql
-- Optimize database
OPTIMIZE TABLE schools, teachers, students, non_teaching_staff;

-- Check database size
SELECT
    table_name AS "Table",
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "Size (MB)"
FROM information_schema.TABLES
WHERE table_schema = "simpedas";
```

### 3. Performance Monitoring

```bash
# Check PHP memory usage
php -i | grep memory_limit

# Monitor server resources
htop
```

---

## 🚨 TROUBLESHOOTING INSTALASI

### Problem: Composer install gagal

```bash
# Update Composer
composer self-update

# Clear cache
composer clear-cache

# Install dengan verbose
composer install -vvv
```

### Problem: Migration gagal

```bash
# Reset database
php artisan migrate:reset
php artisan migrate:fresh --seed
```

### Problem: Storage link gagal

```bash
# Remove existing link
rm public/storage

# Create new link
php artisan storage:link
```

### Problem: Permission denied

```bash
# Set correct ownership
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 📞 DUKUNGAN TEKNIS

Untuk bantuan instalasi dan konfigurasi:

-   **Email**: support@simpedas.com
-   **Telepon**: +62-xxx-xxx-xxxx
-   **Dokumentasi**: [Link dokumentasi]
-   **GitHub Issues**: [Link repository]

---

_Panduan Instalasi Simpedas v1.0 - Terakhir diupdate: [Tanggal]_
