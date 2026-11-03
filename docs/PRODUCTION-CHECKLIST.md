# Checklist Keamanan & Produksi — GISTA Payment

Dokumen ini merangkum konfigurasi wajib sebelum aplikasi dijalankan di production.

## 1. Environment

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.co.id

# Cookie sesi hanya dikirim lewat HTTPS
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_ENCRYPT=true

LOG_LEVEL=warning
```

- Jangan pernah commit `.env` production.
- Pastikan `APP_KEY` sudah di-generate (`php artisan key:generate`).
- Aktifkan HTTPS dan arahkan seluruh trafik HTTP ke HTTPS.

## 2. Cache & optimasi

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

Setelah mengubah `.env`, jalankan ulang `php artisan config:cache`.

## 3. Database & storage

- Gunakan user database dengan hak minimum (bukan `root`).
- Password database kuat dan hanya dapat diakses dari host aplikasi.
- Folder `storage/` dan `bootstrap/cache/` writable oleh web server, tetapi tidak dapat diakses langsung dari publik.
- Lampiran disimpan di disk `local` (private) dan hanya diunduh melalui route ber-otorisasi.

## 4. Keamanan aplikasi

- Rate limit login: 5 percobaan per menit per email + IP.
- Aksi approval/payment: rate limit 5–10 aksi per menit per user.
- Otorisasi dokumen berbasis penugasan (`created_by`, `checker_id`, `spv1_id`); SPV 2 dapat melihat semua dokumen.
- Semua unggahan dibatasi 10 MB dan hanya PDF/DOC/DOCX/XLS/XLSX/JPG/PNG; ekstensi file diturunkan dari MIME terverifikasi.
- Security headers aktif: CSP, X-Frame-Options, nosniff, Referrer-Policy, Permissions-Policy, HSTS.
- Route demo role switcher otomatis mengembalikan 404 di luar environment `local`.

## 5. Audit dependency

```bash
composer audit
npm audit --omit=dev
```

Jika Composer lokal masih versi lama (tanpa `audit`), gunakan Composer terbaru:

```bash
php /path/to/composer.phar audit --locked
```

## 6. Backup & monitoring

- Backup database terjadwal dan uji restore secara berkala.
- Simpan log aplikasi di luar web root dan pantau `storage/logs/laravel.log`.
- Pastikan `APP_DEBUG=false` agar stack trace tidak pernah tampil ke pengguna.
