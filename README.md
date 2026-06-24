# RPN Supervisor - Sistem Manajemen Laporan Inspeksi

Aplikasi web untuk manajemen laporan inspeksi supervisor dengan validasi GPS, selfie check-in, dan dashboard admin.

## Tech Stack

- **Laravel 12** (kompatibel dengan pola Laravel 11)
- **PHP 8.2+**
- **MySQL 8**
- **Bootstrap 5** via AdminLTE 3
- **DataTables**, **SweetAlert2**, **Chart.js**, **Leaflet.js**
- **DomPDF** (export PDF)
- **Maatwebsite Excel** (export Excel)

## Fitur Utama

### Authentication
- Login / Logout
- Middleware role (Admin & Supervisor)
- Dashboard terpisah per role

### GPS & Selfie Check-In
- Validasi lokasi dengan rumus Haversine
- Selfie via MediaDevices API (webcam browser)
- Check-in aktif memungkinkan banyak laporan tanpa selfie ulang
- Check-out wajib sebelum pindah lokasi

### Admin
- Dashboard statistik + grafik
- CRUD Lokasi inspeksi
- Kelola check-in, laporan, temuan
- Approval laporan (setujui/tolak)
- Proses & hasil temuan
- Monitoring peta (Leaflet)
- Export PDF laporan
- Export Excel temuan
- Audit log aktivitas

### Supervisor
- Check-in selfie dengan GPS
- Buat laporan inspeksi + foto multiple
- Tambah temuan + foto multiple
- Lihat progres & timeline temuan
- Lihat hasil tindak lanjut

## Struktur Folder

```
app/
├── Enums/UserRole.php
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Dashboard, Lokasi, CheckIn, Laporan, Temuan, dll
│   │   ├── Auth/           # LoginController
│   │   └── Supervisor/     # Dashboard, CheckIn, Laporan, Temuan
│   ├── Middleware/
│   │   ├── RoleMiddleware.php
│   │   └── CheckActiveCheckIn.php
│   └── Requests/           # Form validation
├── Models/                 # User, Lokasi, CheckIn, LaporanInspeksi, Temuan, dll
└── Services/
    ├── GpsService.php          # Haversine formula
    ├── AuditLogService.php
    ├── ImageUploadService.php
    └── NumberGeneratorService.php

database/migrations/        # 10 migration files
database/seeders/           # Admin & Supervisor default

resources/views/
├── layouts/                # app.blade.php, guest.blade.php
├── admin/                  # AdminLTE views
├── supervisor/             # Supervisor views
├── auth/                   # Login
├── exports/                # PDF template
└── partials/               # Timeline, sidebar
```

## Instalasi (Development)

### Prasyarat
- PHP 8.2+ dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `fileinfo`
- Composer
- MySQL 8
- Web server (Laragon/XAMPP) atau `php artisan serve`

### Langkah Instalasi

```bash
# 1. Clone / masuk ke folder project
cd rpn_supervisor

# 2. Install dependencies
composer install

# 3. Copy environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rpn_supervisor
DB_USERNAME=root
DB_PASSWORD=

# 5. Buat database MySQL
mysql -u root -e "CREATE DATABASE rpn_supervisor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Migrasi & seeder
php artisan migrate --seed

# 7. Storage link (untuk upload foto)
php artisan storage:link

# 8. Jalankan server
php artisan serve
```

Buka: http://localhost:8000

### Akun Default (Seeder)

| Role       | Email               | Password  |
|------------|---------------------|-----------|
| Admin      | admin@rpn.com       | password  |
| Supervisor | supervisor@rpn.com| password  |

### Lokasi Demo (Seeder)
- Gudang A, Gudang B, Workshop, Area Produksi (koordinat Jakarta)

> **Catatan GPS Testing:** Untuk testing di localhost, sesuaikan koordinat lokasi di database agar sesuai posisi GPS perangkat Anda, atau gunakan tools browser dev untuk mock geolocation.

## Deployment (Production)

### 1. Server Requirements
- PHP 8.2+ FPM
- Nginx / Apache
- MySQL 8
- SSL certificate (wajib untuk GPS & kamera di browser)

### 2. Setup Aplikasi

```bash
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
```

### 3. Konfigurasi `.env` Production

```env
APP_NAME="RPN Supervisor"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=rpn_supervisor
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

### 4. Optimasi

```bash
php artisan migrate --force
php artisan db:seed --force   # hanya pertama kali
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Permission Folder

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Nginx Config (contoh)

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/rpn_supervisor/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 7. HTTPS
GPS dan akses kamera **memerlukan HTTPS** di production (kecuali localhost).

## Middleware

| Alias            | Class                  | Fungsi                              |
|------------------|------------------------|-------------------------------------|
| `role:admin`     | RoleMiddleware         | Akses khusus admin                  |
| `role:supervisor`| RoleMiddleware         | Akses khusus supervisor             |
| `checkin.active` | CheckActiveCheckIn     | Wajib punya check-in aktif          |

## API GPS Validation

Endpoint: `POST /supervisor/checkin/validate-gps`

```json
{
  "latitude": -6.200000,
  "longitude": 106.816666,
  "lokasi_id": 1
}
```

Response:
```json
{
  "valid": true,
  "distance": 45.23,
  "radius": 200,
  "message": "Lokasi valid."
}
```

## License

MIT
