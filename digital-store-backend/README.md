# Backend (Laravel) - Drop-in Starter

**How to use**

1) Create a fresh Laravel app (Laravel 10/11) with Sanctum:
```
composer create-project laravel/laravel backend-app
cd backend-app
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```
2) Copy the contents of this `backend/` folder into your Laravel project (merge/overwrite `app/`, `database/`, `routes/`, `config/`, etc.).  
3) Update `.env` with your MySQL + Mail (SMTP) credentials.  
4) Run migrations & seeders:
```
php artisan migrate --seed
```
5) Serve:
```
php artisan serve
```
6) For token auth, we use Sanctum SPA tokens (or use `/api/login` that returns a token).

**Admin login (after seed):**  
Email: `admin@example.com`  
Password: `password`

**User login (after seed):**  
Email: `user@example.com`  
Password: `password`
