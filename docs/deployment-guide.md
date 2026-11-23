# AetherCP Deployment & Production Guide

## 1. Web Server Configuration

### Apache (.htaccess)
AetherCP ships with standard Laravel `.htaccess` in the `public/` folder with `mod_rewrite` enabled.

Recommended VirtualHost:
```apache
<VirtualHost *:80>
    ServerName ro.yourdomain.com
    DocumentRoot "C:/xampp/htdocs/aether/public"

    <Directory "C:/xampp/htdocs/aether/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name ro.yourdomain.com;
    root /var/www/aether/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 2. Dependency Installation & Environment Setup

When cloning from Git, third-party libraries (`vendor/`), frontend bundles (`public/build/`), and environment configuration (`.env`) must be initialized:

```bash
# 1. Install production PHP packages (creates vendor/autoload.php)
composer install --no-dev --optimize-autoloader

# 2. Setup environment configuration
cp .env.example .env

# 3. Generate application encryption key
php artisan key:generate

# 4. Install Node packages & compile production assets (creates public/build/manifest.json)
npm install
npm run build

# 5. Set directory permissions (Linux)
sudo chown -R www-data:www-data /var/www/aether
sudo chmod -R 775 /var/www/aether/storage /var/www/aether/bootstrap/cache
```

> [!IMPORTANT]
> - If you encounter `Fatal error: Failed opening required vendor/autoload.php`, run `composer install`.
> - If you encounter `Illuminate\Foundation\ViteManifestNotFoundException: Vite manifest not found at: .../public/build/manifest.json`, run `npm install && npm run build`.

---

## 3. Production Optimizations

Before launching your live server:
```bash
# 1. Cache configuration and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Disable debug mode in .env
APP_ENV=production
APP_DEBUG=false
```

---

## 4. Security Checklist & Admin Access
- **Record Administrator Credentials**: The final step of the Web Installation Wizard explicitly displays your configured **Admin Username** and **Admin Password** (or reminder of in-game password). If installed via CLI `php artisan aether:install`, the default credentials are `admin` / `Admin1234!`.
- **Change Default Password**: Change any default or temporary administrator password immediately upon first login at `/admin`.
- Ensure `APP_DEBUG=false` in production.
- Keep `DB_DATABASE` (`aether_cp`) separate from the game emulator database.
- Use a database user with `SELECT`, `INSERT`, `UPDATE` permissions on emulator tables, avoiding unnecessary `DROP` privileges.

