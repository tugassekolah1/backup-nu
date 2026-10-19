# Website NU Banjaranyar

Website resmi **Nahdlatul Ulama Banjaranyar, Cilongok** — berisi profil organisasi, berita,
agenda, galeri, kotak aspirasi, infaq, serta pendaftaran & pencetakan Kartu Tanda Anggota (KTA).

Dokumen ini adalah panduan untuk developer/pengelola teknis: cara menjalankan, mengatur
environment, dan men-deploy ke server produksi.

---

## 1. Fitur

**Halaman publik**

| Halaman | URL | Keterangan |
| --- | --- | --- |
| Beranda | `/` | Berita unggulan, agenda terdekat, statistik |
| Berita | `/berita`, `/berita/{slug}` | Daftar & detail berita |
| Agenda | `/agenda` | Agenda kegiatan |
| Galeri | `/galeri` | Galeri foto |
| Organisasi | `/organisasi`, `/organisasi/{slug}` | Profil, struktur, pengurus per banom |
| Kontak | `/kontak` | Alamat, telepon, WhatsApp |
| Aspirasi | `/aspirasi`, `/aspirasi/lacak` | Kirim & lacak aspirasi warga |
| Infaq | `/infaq` | Form donasi, instruksi bayar, kirim bukti transfer |
| Pendaftaran anggota | `/daftar` | Form pendaftaran anggota baru |
| Kartu anggota | `/kartu-anggota` | Cek status, pratinjau, cetak & unduh KTA |

**Panel admin** (`/dashboard`, wajib login)

Kelola berita, agenda, galeri, pengurus, infaq (termasuk tandai lunas & rekapitulasi),
aspirasi, permintaan pendaftaran anggota, serta cetak KTA massal.

---

## 2. Kebutuhan sistem

- **PHP >= 8.3** dengan ekstensi:
  - **`iconv`** — wajib untuk membuat QR code KTA (dipakai `bacon/bacon-qr-code`)
  - **`gd`** — wajib menurut `simplesoftwareio/simple-qrcode`
  - `mbstring`, `openssl`, `pdo` + `pdo_sqlite`/`pdo_mysql`, `fileinfo`, `zip`, `curl`
- **Composer 2**
- **Node.js 20+** & npm (untuk build aset Tailwind/Vite)

> **Penting (Linux/Arch):** `iconv` dan `gd` sering nonaktif secara default. Cek dengan
> `php -m | grep -E 'iconv|gd'`. Jika kosong, aktifkan di `php.ini`
> (mis. `/etc/php/php.ini`) dengan menghapus tanda komentar:
>
> ```ini
> extension=iconv
> extension=gd
> ```
>
> Tanpa `iconv`, tombol *Cetak KTA* dan *Unduh KTA* akan gagal dengan
> `Call to undefined function BaconQrCode\Encoder\iconv()`.

---

## 3. Menjalankan di lingkungan lokal

```bash
# 1. Dependensi PHP + aset + database (satu perintah)
composer run setup

# 2. Isi data contoh (opsional) + akun admin
php artisan db:seed

# 3. Jalankan server + queue + Vite sekaligus
composer run dev
```

`composer run setup` melakukan: `composer install`, menyalin `.env.example` → `.env`,
`key:generate`, membuat file SQLite bila belum ada, `migrate`, `storage:link`,
`npm install`, dan `npm run build`.

Buka `http://localhost:8000`.

### Akun admin

Akun admin dibuat oleh `php artisan db:seed` (`DatabaseSeeder`) dengan email dari
`SEEDER_ADMIN_EMAIL` (default `admin@gmail.com`).

- Jika `SEEDER_ADMIN_PASSWORD` di `.env` diisi, password itu yang dipakai.
- Jika kosong, password acak akan **dicetak sekali** ke terminal saat seeding.

Segera ganti password setelah login pertama.

### Perintah harian

```bash
composer run dev        # jalankan server + queue worker + Vite (sekali jalan)
npm run dev             # hanya Vite (hot reload aset)
npm run build           # build aset untuk produksi (wajib setelah ubah CSS/JS)
php artisan test        # jalankan seluruh test (Pest)
php artisan pail        # pantau log realtime
```

---

## 4. Konfigurasi environment (`.env`)

Mulai dari template: `cp .env.example .env && php artisan key:generate`.

Variabel yang paling sering disesuaikan:

| Variabel | Keterangan |
| --- | --- |
| `APP_NAME` | Nama yang tampil di panel admin & judul halaman |
| `APP_ENV`, `APP_DEBUG` | `production` / `false` di server |
| `APP_URL` | Alamat lengkap, mis. `https://nu-banjaranyar.sch.id` |
| `DB_CONNECTION`, `DB_*` | Database (default `sqlite`; disarankan MySQL/PostgreSQL untuk produksi) |
| `MAIL_*` | Pengiriman email (default `log` = hanya ditulis ke log) |
| `SESSION_SECURE_COOKIE=true` | Wajib bila situs sudah HTTPS |
| `SEEDER_ADMIN_EMAIL`, `SEEDER_ADMIN_PASSWORD` | Akun admin awal saat `db:seed` |

Daftar lengkap tersedia di `.env.example`.

---

## 5. Checklist deploy produksi

1. **Environment**
   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-anda
   SESSION_SECURE_COOKIE=true
   LOG_LEVEL=warning
   ```
   Lalu `php artisan key:generate` (sekali saja, saat instalasi pertama).

2. **Database**: set kredensial MySQL/MariaDB/PostgreSQL, lalu
   ```bash
   php artisan migrate --force
   ```
   (tambahkan `--seed` bila ingin membuat akun admin pertama).

3. **Ekstensi PHP `iconv` & `gd` aktif** (lihat bagian 2).

4. **Storage**
   ```bash
   php artisan storage:link
   ```
   Wajib, agar foto berita/galeri, foto anggota, dan bukti transfer dapat diakses.
   Symlink ini **spesifik per server** — jika folder proyek dipindah, buat ulang.

5. **Build aset**
   ```bash
   npm ci && npm run build
   ```
   Aset tidak lagi memakai Tailwind CDN; seluruh utility (termasuk warna identitas NU)
   sudah dikompilasi ke `public/build`.

6. **Optimasi cache**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   Setelah mengubah `.env`, jalankan `php artisan config:clear` lalu cache ulang.
   Catatan: saat `config:cache` aktif, `.env` tidak lagi dibaca oleh `env()`;
   seeder akan memakai password acak bila `SEEDER_ADMIN_PASSWORD` tidak terbaca.

7. **Izin folder**: `storage/` dan `bootstrap/cache/` harus dapat ditulis oleh user web server.

8. **Queue**: `QUEUE_CONNECTION=database`. Jika fitur antrian dipakai, jalankan worker
   (systemd/supervisor):
   ```bash
   php artisan queue:work --tries=3
   ```

9. **Backup rutin**: database + `storage/app/public`.

10. **Ganti password admin** bawaan seeder setelah login pertama.

---

## 6. Catatan keamanan

- **Rute simulasi pembayaran** (`POST /infaq/simulate/{kode}`) hanya didaftarkan pada
  environment `local`/`testing`. Di produksi rute ini tidak ada, sehingga status donasi
  hanya bisa menjadi *lunas* melalui verifikasi admin.
- **Pembatasan laju (rate limit)**: form publik `/aspirasi`, `/daftar`, dan `/infaq`
  dibatasi per alamat IP untuk mencegah spam.
- **Halaman `/kartu-anggota`** menampilkan data anggota setelah nomor identitas
  diverifikasi (lihat bagian 7 untuk kebijakan data pribadi).
- **`APP_DEBUG=false` wajib di produksi** — selain menampilkan stack trace, versi Laravel
  yang terpasang memiliki advisory XSS pada halaman debug.

### Perbarui dependensi

Jalankan pemeriksaan berkala:

```bash
composer audit
npm audit
```

---

## 7. Data pribadi (UU PDP)

Aplikasi menyimpan NIK, nomor HP, dan foto anggota. Halaman `/kartu-anggota` hanya
menampilkan data setelah pengguna memasukkan nomor identitas yang benar, dan tetap
disarankan:

- Jangan membagikan berkas `database/*.sqlite` atau isi `storage/app/public` ke pihak lain.
- Ganti kredensial admin default dan batasi jumlah akun admin.
- Jika perlu, aktifkan pencarian dengan verifikasi tambahan (mis. tanggal lahir).

---

## 8. Pengujian

```bash
php artisan test --compact
```

Seluruh test harus hijau. Jika muncul `Call to undefined function ... iconv()`,
aktifkan ekstensi `iconv` (bagian 2).

Aplikasi mengaktifkan `Model::preventLazyLoading()` di luar produksi, sehingga
relasi yang diakses tanpa *eager loading* akan memunculkan
`LazyLoadingViolationException`. Ini disengaja agar masalah N+1 query tidak lolos ke produksi.

---

## 9. Troubleshooting

| Gejala | Penyebab & solusi |
| --- | --- |
| `Call to undefined function BaconQrCode\Encoder\iconv()` | Ekstensi `iconv` nonaktif → aktifkan di `php.ini` |
| Gambar/foto tidak muncul (404) | Symlink storage belum dibuat → `php artisan storage:link` |
| `Unable to locate file in Vite manifest` | Aset belum dibuild → `npm run build` |
| Perubahan tampilan tidak muncul | Jalankan `npm run dev` atau `composer run dev`, atau `npm run build` |
| `LazyLoadingViolationException` | Ada relasi yang belum di-*eager load*; tambahkan `with(...)` di query |
| Halaman tampil tanpa gaya (tanpa warna) | Aset belum dibuild atau cache lama → `php artisan view:clear` + `npm run build` |

---

## 10. Struktur singkat

```
app/
  Http/Controllers/     # controller publik & admin
  Http/Middleware/      # termasuk middleware admin
  Models/               # Berita, NuMember, Payment, Infaq, Agenda, Galeri, Pengurus, Aspirasi
resources/
  css/app.css           # Tailwind + style dasar
  js/tailwind-tokens.js # token warna/typography identitas NU (dipakai tailwind.config.js)
  views/                # Blade: halaman publik, panel admin, komponen
routes/web.php          # seluruh rute publik & admin
database/               # migrasi, seeder, factory
tests/                  # test Pest (termasuk regression N+1)
```
