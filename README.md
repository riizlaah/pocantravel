# PO CAN Travel

Test case untuk seleksi magang di CAN Creative

## Features
- Auth (login/register)
- View (search & filter) tickets
- Order ticket
- Save (bookmark) ticket
- Pagination

## Setup
0. Clone repo ini
1. Jalankan `composer install`
2. Jalankan `cp .env.example .env`
3. Jalankan `php artisan key:generate`
4. Jalankan `php artisan migrate:fresh --seed` untuk menjalankan migration beserta seednya
5. Jalankan `php artisan serve` untuk menjalankan proyeknya

## Note
> Kadang setup/install package terblokir karena security adversories pada versi ini (Laravel 10), gunakan flag `--no-blocking` agar tetap terinstal
