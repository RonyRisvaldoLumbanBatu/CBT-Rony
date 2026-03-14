# CBT Rony - Computer Based Test

Aplikasi Computer Based Test (CBT) modern yang dibangun dengan **Laravel 12**, **Livewire 3**, dan **PostgreSQL**.

## 🚀 Fitur Utama
- **Modern Stack:** Laravel 12 + Livewire 3 + Tailwind CSS.
- **Reactivity:** Menggunakan Livewire Volt untuk komponen single-file yang kencang.
- **Real-time:** Integrasi Laravel Reverb untuk monitoring ujian secara langsung.
- **Modern UI:** Tailwind CSS v3 dengan desain yang responsif.
- **Strict Mode:** Mengaktifkan Laravel Strict Database Mode untuk kualitas kode terbaik.

## 🛠️ Persyaratan Sistem
- PHP 8.2 atau lebih baru.
- Composer.
- Node.js & NPM.
- PostgreSQL (Disarankan menggunakan **DBngin** jika di Windows/Mac).
- **Laravel Herd** (Disarankan untuk pengembangan lokal yang cepat).

## ⚙️ Instalasi Lokal

1.  **Clone Repository:**
    ```bash
    git clone https://github.com/username/cbt-rony.git
    cd cbt-rony
    ```

2.  **Instal Dependensi Backend:**
    ```bash
    composer install
    ```

3.  **Instal Dependensi Frontend:**
    ```bash
    npm install
    npm run build
    ```

4.  **Konfigurasi Environment:**
    Salin file `.env.example` menjadi `.env` dan sesuaikan pengaturan database Anda:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    Contoh pengaturan **PostgreSQL (DBngin)**:
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=cbt_rony
    DB_USERNAME=postgres
    DB_PASSWORD=
    ```

5.  **Jalankan Migrasi & Seeder:**
    ```bash
    php artisan migrate --seed
    ```

6.  **Hubungkan Storage:**
    ```bash
    php artisan storage:link
    ```

## 🔐 Akun Login Default
Setelah menjalankan seeder, Anda dapat login menggunakan akun berikut:
- **Email:** `guru@cbt.test`
- **Password:** `password`
- **Role:** `guru`

## 👨‍💻 Pengembangan
Untuk menjalankan server pengembangan lokal:
```bash
php artisan serve
# atau gunakan Laravel Herd
```

Untuk melihat perubahan frontend secara real-time:
```bash
npm run dev
```

---
Dibuat dengan ❤️ menggunakan **Laravel 12**.
