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

## 2. Production Optimizations

Before launching your live server:
```bash
# 1. Cache configuration and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Build production assets
npm run build

# 3. Disable debug mode in .env
APP_ENV=production
APP_DEBUG=false
```

---

## 3. Security Checklist
- Ensure `APP_DEBUG=false` in production.
- Keep `DB_DATABASE` (`aether_cp`) separate from the game emulator database.
- Use a database user with `SELECT`, `INSERT`, `UPDATE` permissions on emulator tables, avoiding unnecessary `DROP` privileges.
- Change the default administrator password immediately upon first login.

