# Mora Lenz Laravel

This is the public Laravel/MySQL conversion of the original Next.js + NestJS/Prisma project. Admin editing is handled by the separate Node.js app in `../admin`.

## Stack

- PHP 8.3+
- Laravel 13
- MySQL 8
- Blade + Tailwind CSS through Vite

## Setup

1. Install PHP dependencies:

```bash
composer install
```

2. Install frontend dependencies:

```bash
npm install
```

3. Copy and configure the environment:

```bash
cp .env.example .env
php artisan key:generate
```

4. Create a MySQL database named `mora_lenz`, then set these values in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mora_lenz
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. Run migrations and seed the converted sample data:

```bash
php artisan migrate --seed
```

6. Build assets and start Laravel:

```bash
npm run build
php artisan serve
```

## Development

Run the PHP server and Vite watcher in separate terminals:

```bash
php artisan serve
npm run dev
```

Run tests:

```bash
php artisan test
```

## Uploads

The Vercel admin app uploads images to this Laravel app through:

```text
POST /api/admin/uploads
```

Set the same random token in both apps:

```env
ADMIN_UPLOAD_TOKEN=your-long-random-token
```

Uploaded files are saved under:

```text
public/uploads/events
public/uploads/members
public/uploads/gallery
```

The database stores image URLs. The image binary files stay in the server file manager.

Create the first super admin in MySQL without putting the password in `.env`:

```bash
php artisan migrate
php artisan admin:create-super
```
