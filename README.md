# User-maker-checker

## Installation

```
git clone https://github.com/Naijabas/user-maker-checker.git
```
```
cd user-maker-checker
```
```
composer install
```
```
cp .env.example .env
```
```
create a database and inform .env
```  

```
php artisan migrate --seed
```
**Please use this header when testing `Accept: application/json`**
```
php artisan serve
``` 

#### Default users

- `superadmin1@admin.com` => `password`
- `superadmin2@admin.com` => `password`


