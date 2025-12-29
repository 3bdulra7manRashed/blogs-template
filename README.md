# Demo Theme Blog — Laravel 12

A Laravel 12 + Tailwind CSS v4 project that replicates the “Writing Blog / Personal Blog (Writer)” theme. The project ships with Laravel Breeze authentication, custom theming, and all PRD requirements for building a Writer-inspired CMS.

---

## 1. Requirements

- PHP 8.3+
- Composer 2.6+
- MySQL 8.0+ (dev/prod)
- Node.js 20+ and npm 10+
- Git
- Recommended: Mailpit/Mailhog for local email testing

---

## 2. Local Setup

```bash
git clone <repo-url> writer-blog
cd writer-blog

cp .env.example .env
composer install
php artisan key:generate

# Configure DB_ values inside .env before migrating
php artisan migrate

npm install
npm run dev   # or npm run watch for continual rebuilt assets

php artisan serve
```

Access the site at http://127.0.0.1:8000.

---

## 3. Environment Notes

- `.env.example` is preconfigured for MySQL. For production use `.env.production` (not committed) with:
  - `APP_URL=https://your-domain.com`
  - `APP_DEBUG=false`
  - Real SMTP credentials.
- Run `php artisan config:cache` after changing env vars in production.

---

## 4. Authentication

Laravel Breeze (Blade stack) provides login, register, forgot/reset password, and email verification scaffolding.

Install (or reinstall) Breeze:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
php artisan migrate
```

---

## 5. Asset Pipeline

- Vite handles bundling with entry points `resources/js/app.js` and `resources/css/app.css`.
- Tailwind CSS v4 (alpha) configured in `tailwind.config.js`.
- Scripts:

```
npm run dev     # development server w/ HMR
npm run watch   # incremental rebuild
npm run build   # production build
```

---

## 6. Storage & Media

- Public disk targets `storage/app/public`.
- Create symlink once per environment:

```
php artisan storage:link
```

Uploads are available at `public/storage`.

---

## 7. Database & Seeding

Run the migrations (and soon seeders):

```
php artisan migrate
php artisan db:seed   # once seeders available
```

Reset database:

```
php artisan migrate:fresh --seed
```

---

## 8. Testing

```
php artisan test
# or
./vendor/bin/phpunit
```

---

## 9. Deployment Checklist

1. `composer install --no-dev --optimize-autoloader`
2. `npm run build`
3. `php artisan migrate --force`
4. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
5. `php artisan storage:link` (once)
6. Configure queue workers / cron for scheduled tasks if used.

---

## 10. Git Workflow

- Default branch: `main`
- Feature branches: `feature/<task>-short-desc` (example: `feature/01-project-setup`)
- Suggested first commits:
  1. `chore: bootstrap Laravel 12 project`
  2. `chore: configure Vite + Tailwind`
  3. `docs: add project README`

Open PRs into `main`, request review, squash merge.

---

## 11. Next Steps

- Implement domain models, migrations, policies, and seeders.
- Recreate Writer-themed Blade layouts + front-end pages.
- Build custom admin dashboard with CRUD, media library, scheduling.
- Add caching, SEO features, sitemap/robots, and automated tests.
