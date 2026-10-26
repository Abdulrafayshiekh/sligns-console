# Aligna Suite Coaching Console

Laravel 13 · Blade · Bootstrap 5.3 · Chart.js

## Roles

| Role | Signs in at | Sees |
|---|---|---|
| Super Admin (Aligna Super Deployer) | `/deployer/sign-in` (email + master key) | Coaching companies, coach and client counts |
| Coaching Company | `/sign-in` | Its coaches, totals, monthly activity, client reassignment |
| Coach | `/sign-in` | Own clients, app activity, private notes, messages |
| Client | No console access | Uses the five apps |

## Setup with Laragon

1. Unzip into `D:\laragon\www\aligna-console`.
2. In Laragon, create the database `aligna_console`.
3. In the Laragon terminal:

```
cd D:\laragon\www\aligna-console
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

4. Reload Laragon and open http://aligna-console.test

## Sample accounts

| Role | Email | Password |
|---|---|---|
| Super Admin | admin@aligna.ai | aligna-master-key |
| Company | admin@proctorgallagher.test | password |
| Company | admin@johnstonemurphy.test | password |
| Coach | coach1@proctorgallagher.test (coach2, coach3) | password |
| Coach | coach1@johnstonemurphy.test (coach2, coach3) | password |

The master key comes from `DEPLOYER_MASTER_KEY` in `.env` when seeding. Change it before going live.

## Notes

- App activity (Lotus, Scintilla, Momentum, TargetPro, Script Builder) is sample data from `DemoSeeder`. Real data goes into the `app_activities` table.
- Emails use `MAIL_MAILER=log`, so they are written to `storage/logs/laravel.log`. With `APP_DEBUG=true` the reassign page also shows a test link for each pending consent.
- Console text is ready for translation: add `lang/es.json`, `lang/ur.json` and so on. Arabic, Hebrew and Urdu consoles switch to right-to-left.
- Put an Aligna logo at `public/images/aligna-logo.png` to show it on the Super Admin pages.
- Themes: Aligna (pink with a black header and footer, the default), Aligna Night, Blush, Atlas, and Company colours (the interface colour chosen when the company was deployed). Each user picks one from **Theme** in the header; it is saved on their account, and on the sign-in pages in a cookie. After updating, run `php artisan migrate` to add the `theme` column.
- The font is Suisse Intl from alignatech.ai (`public/fonts`), used under Aligna's own licence.

## Tests

```
php artisan test
```
