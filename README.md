# PO CAN Travel

Test case untuk seleksi magang di CAN Creative

## ERD
![PO CAN Travel ERD](pocantravel-erd.png)

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
> Menggunakan laravel 10, ada package yang masih memiliki beberapa security advisories
