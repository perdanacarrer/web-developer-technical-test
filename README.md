# Movie App — Web Developer Technical Test

A Laravel 5.8 movie search app built against the [OMDb API](http://www.omdbapi.com/). Login → search/browse movies with infinite scroll → view details → save/remove favorites.

> 🇮🇩 Ringkasan Bahasa Indonesia ada di bagian [bawah](#bahasa-indonesia) file ini.

## Login credentials

```
Username: aldmic
Password: 123abc123
```

## Libraries / packages used

| Package | Purpose |
|---|---|
| `laravel/framework` 5.8 | Core framework (as required by the test brief) |
| `guzzlehttp/guzzle` | HTTP client used by `OmdbService` to call the OMDb API |
| SQLite (`pdo_sqlite`) | Zero-config storage for the favorites list, no DB server needed |
| Bootstrap 5 (CDN) | UI styling/components — no npm/webpack build step required |
| Vanilla JS (`fetch`, `IntersectionObserver`) | AJAX search, infinite scroll, favorite toggling |
| Native `loading="lazy"` on `<img>` | Lazy-loads movie posters |
| PHPUnit | A few feature tests for the login flow |

No Laravel Mix / Node build step is required — the front end is plain Blade + vanilla JS + a Bootstrap CDN link, so `npm install` is never needed.

## Architecture

- **MVC** via Laravel: `routes/web.php` → `Controllers` → `Blade views`.
- **Service layer** — `App\Services\OmdbService` wraps all Guzzle calls to OMDb (search + detail lookup) so controllers never deal with the raw HTTP/JSON shape directly.
- **Auth** — the brief specifies one fixed credential pair rather than a real user table, so login is a simple session flag (`App\Http\Middleware\CheckLogin`) instead of Laravel's full auth scaffold. Credentials live in `config/app_auth.php`, sourced from `.env`.
- **Favorites** — an Eloquent model (`App\Favorite`) backed by a single SQLite table (`favorites`). No `user_id` column since there's only one login.
- **Localization** — `resources/lang/en` / `resources/lang/id` hold every static UI string; `App\Http\Middleware\SetLocale` applies the language stored in session. Data returned by OMDb is **never** translated, per the brief.
- **Infinite scroll** — `GET /movies` server-renders page 1; `GET /movies/search` (JSON) is polled by an `IntersectionObserver` in `resources/views/movies/index.blade.php` as the user scrolls.
- **Empty states** — `resources/views/partials/empty-state.blade.php` is reused for "no search yet", "no results" and "no favorites yet".

## Screenshots

*(Add your own screenshots here after running the app locally — e.g. login page, movie list with search, movie detail, favorites page.)*

```
docs/screenshot-login.png
docs/screenshot-movies.png
docs/screenshot-detail.png
docs/screenshot-favorites.png
```

---

## Running it locally

You have **XAMPP** and **Docker** available — **use Docker.** Laravel 5.8 needs PHP `^7.1.3` (ideally 7.3/7.4), but XAMPP on modern macOS (especially Apple Silicon) ships PHP 8.1+, which Laravel 5.8 does not support — you'd have to hunt down and manually install an old XAMPP/PHP build. Docker sidesteps that entirely: the `Dockerfile` here pins PHP 7.4, so it works identically regardless of what's on your Mac.

### Option A — Docker (recommended)

Requirements: Docker Desktop running.

```bash
cd web-developer-technical-test
docker compose up --build
```

First run downloads PHP dependencies via Composer (needs internet) and runs migrations automatically. Once it says the container is up:

```
http://localhost:8080
```

Stop it with `Ctrl+C`, or `docker compose down` (add `-v` to also wipe the saved favorites volume).

The `.env` file is already filled in with a demo OMDb API key (`cab62d73`, taken from the confirmation email you shared) — swap in your own key from http://www.omdbapi.com/apikey.aspx if you prefer.

### Option B — XAMPP

Only do this if you install a **PHP 7.3 or 7.4** build of XAMPP (the current default macOS XAMPP is PHP 8.x and will not boot Laravel 5.8).

1. Install [Composer](https://getcomposer.org/) if you don't have it.
2. `cd web-developer-technical-test && composer install`
3. Edit `.env`: set `DB_DATABASE` to an absolute path such as `/Applications/XAMPP/xamppfiles/htdocs/web-developer-technical-test/database/database.sqlite`, and create that empty file (`touch database/database.sqlite`).
4. `php artisan key:generate`
5. `php artisan migrate`
6. Point an XAMPP vhost's document root at this project's `public/` folder (or run `php artisan serve` instead of using XAMPP's Apache at all, which is simplest: `php artisan serve --port=8080`, then open `http://localhost:8080`).

## Feature checklist (per the test brief)

- [x] Login page with the fixed credentials above; wrong credentials show an error message.
- [x] List Movie page, gated behind login.
- [x] Detail Movie page, gated behind login.
- [x] Search / filter by title, type and year.
- [x] Infinite scroll on the List Movie page.
- [x] Lazy-loaded poster images.
- [x] Add to Favorites from both List and Detail pages.
- [x] Dedicated Favorites page, with removal.
- [x] Empty-state layout when there's no data (no search yet, no results, no favorites).
- [x] Multi-language UI (EN default, ID switchable) for static strings only.

---

## Bahasa Indonesia

**Login:** `aldmic` / `123abc123`

**Menjalankan secara lokal (disarankan Docker):** Laravel 5.8 butuh PHP 7.1–7.4, sementara XAMPP terbaru di Mac biasanya sudah PHP 8.x sehingga tidak kompatibel. Docker sudah mengunci PHP 7.4 lewat `Dockerfile`, jadi tinggal jalankan:

```bash
docker compose up --build
```

lalu buka `http://localhost:8080`. Kredensial OMDb API key contoh (`cab62d73`) sudah diisi di `.env`; silakan ganti dengan API key Anda sendiri bila perlu.

Jika tetap ingin pakai XAMPP, pastikan versi PHP-nya 7.3/7.4 (bukan default 8.x), lalu jalankan `composer install`, atur `DB_DATABASE` di `.env` ke path absolut file SQLite, `php artisan key:generate`, `php artisan migrate`, dan arahkan document root ke folder `public/` (atau paling gampang jalankan `php artisan serve` saja).

**Library yang dipakai:** Laravel 5.8, Guzzle (HTTP client ke OMDb API), SQLite (penyimpanan favorit tanpa perlu setup DB server), Bootstrap 5 via CDN, vanilla JS untuk AJAX/infinite scroll/lazy load.

**Arsitektur:** MVC standar Laravel + service layer (`OmdbService`) untuk semua panggilan ke OMDb API, middleware sesi sederhana untuk login (karena hanya ada satu kredensial tetap, bukan tabel user), model `Favorite` + tabel SQLite untuk data favorit, dan middleware `SetLocale` untuk multi-bahasa (EN/ID) yang hanya berlaku ke teks statis UI, bukan data dari OMDb API.
