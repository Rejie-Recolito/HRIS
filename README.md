<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## LibreOffice (DOCX → PDF) configuration

This application can convert generated DOCX files to PDF using LibreOffice in headless mode.

1. Set the `LIBREOFFICE_PATH` environment variable in your `.env` file to the full path of the
   `soffice` executable if it's not available on your system PATH. Examples:

    - Windows:

        LIBREOFFICE_PATH="C:\\Program Files\\LibreOffice\\program\\soffice.exe"

    - Linux/macOS (if installed system-wide):

        LIBREOFFICE_PATH="/usr/bin/soffice"

2. If `LIBREOFFICE_PATH` is empty, the app will try a set of common install locations and then
   fall back to looking for `soffice` on the PATH.

Troubleshooting:

-   If conversion doesn't occur, check `storage/logs/laravel.log` for messages from the
    conversion helper — it logs the candidate binary used, stdout, stderr, and the exit code.
-   You can also run the conversion command directly to reproduce errors manually. Example:

```bash
"C:\\Program Files\\LibreOffice\\program\\soffice.exe" --headless --convert-to pdf --outdir C:\\Temp C:\\Temp\\file.docx
```

Only set `LIBREOFFICE_PATH` in environments where you trust the runtime (do not expose this
setting publicly).

Additional notes (new converter behavior):

-   The application now uses a centralized `App\Services\LibreOfficeConverter` service to run
    DOCX→PDF conversions. The converter creates a per-conversion LibreOffice user profile
    directory (inside `storage/app/tmp`) and passes it to LibreOffice via
    `-env:UserInstallation=file://...` while also setting `HOME` and `XDG_RUNTIME_DIR`.

-   This avoids common headless-server failures like dconf/javaldx errors and ensures the
    conversion runs under the same user that created the profile (usually the web user,
    `www-data`).

-   Recommended `LIBREOFFICE_PATH` value on Linux: point to the real binary when available
    (preferred). For example:

          LIBREOFFICE_PATH="/usr/lib/libreoffice/program/soffice.bin"

    Using `soffice.bin` avoids wrapper scripts and provides the most consistent behavior
    on headless servers. If unset, the converter will fall back to common locations.

If you run into permission issues, ensure `storage/app/tmp` and `/var/www/.cache/dconf`
are writable by the web user (`www-data`) or let the converter create per-conversion
profiles which it will remove after conversion.

## Persistent profile (recommended for servers)

On headless servers it's more robust to use a dedicated persistent LibreOffice profile
directory owned by the web user to avoid first-run installers or extension prompts that
may try to escalate privileges. Set the `LIBREOFFICE_PROFILE_DIR` environment variable
to point to that directory and the converter will use it instead of creating a transient
profile.

Example commands to create a persistent profile directory:

```bash
sudo mkdir -p /var/lib/libreoffice-www/runtime
sudo chown -R www-data:www-data /var/lib/libreoffice-www
sudo chmod -R 700 /var/lib/libreoffice-www

# (optional) run once to initialize the profile
sudo -u www-data /usr/lib/libreoffice/program/soffice.bin --headless --invisible -env:UserInstallation=file:///var/lib/libreoffice-www --version

# then set in your .env
LIBREOFFICE_PROFILE_DIR=/var/lib/libreoffice-www
LIBREOFFICE_PATH=/usr/lib/libreoffice/program/soffice.bin
```

With `LIBREOFFICE_PROFILE_DIR` set, the converter preserves the profile directory on
success and will reuse it for subsequent conversions (faster and more stable on servers).
