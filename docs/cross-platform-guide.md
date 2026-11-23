# AetherCP Cross-Platform Deployment & Compatibility Guide

This guide details how to install and run **AetherCP** on **Linux** (Ubuntu, Debian, AlmaLinux, CentOS, Alpine), **Windows** (XAMPP, IIS), **macOS**, and **Docker**.

---

## 1. Operating System Compatibility Matrix

| OS / Platform | Supported Web Server | PHP Version | Database | Notes |
| :--- | :--- | :--- | :--- | :--- |
| **Linux (Ubuntu / Debian)** | Nginx + PHP-FPM, Apache2 | 8.3, 8.4+ | MariaDB 10.4+, MySQL 8.0+ | Fully case-sensitive filesystem compliant |
| **Linux (RHEL / Alma / Rocky)** | Nginx, Apache (httpd) | 8.3, 8.4+ | MariaDB 10.5+, MySQL 8.0+ | SELinux file contexts configured |
| **Windows** | Apache (XAMPP), IIS | 8.3, 8.4+ | MariaDB, MySQL | Native pathing and service support |
| **macOS (Darwin)** | Valet, Homebrew Nginx/Apache | 8.3, 8.4+ | MariaDB, MySQL | Developer workstation compatible |
| **Docker / Containers** | Multi-stage Alpine container | 8.4+ | Any containerized MySQL/MariaDB | Single-command portable deployment |

---

## 2. Linux Setup (Ubuntu / Debian)

### Step 1: Install Required Packages
```bash
sudo apt update
sudo apt install -y php8.4 php8.4-fpm php8.4-mysql php8.4-mbstring php8.4-xml \
                    php8.4-curl php8.4-gd php8.4-zip php8.4-bcmath composer nginx
```

### Step 2: Clone / Copy AetherCP
```bash
cd /var/www/
git clone https://github.com/your-repo/aether.git aether
cd aether
```

### Step 3: Configure Linux File Permissions
On Linux, the web server user (`www-data` on Ubuntu/Debian, `nginx`/`apache` on RHEL) requires write permissions to `storage` and `bootstrap/cache`:
```bash
sudo chown -R www-data:www-data /var/www/aether
sudo chmod -R 775 /var/www/aether/storage /var/www/aether/bootstrap/cache
```

### Step 4: Environment & Database Setup
```bash
cp .env.example .env
php artisan key:generate
```

Configure your `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=aether_cp
DB_USERNAME=aether_user
DB_PASSWORD=your_password

# For Unix Domain Sockets on Linux (optional performance boost):
# DB_SOCKET=/var/run/mysqld/mysqld.sock

RO_DB_HOST=127.0.0.1
RO_DB_DATABASE=ragnarok
RO_DB_USERNAME=ragnarok_user
RO_DB_PASSWORD=your_game_db_pass
RO_EMULATOR=rathena
RO_RENEWAL=true
```

### Step 5: Install & Build
```bash
composer install --no-dev --optimize-autoloader
php artisan aether:install
npm install && npm run build
```

### Step 6: Nginx VirtualHost Configuration
Create `/etc/nginx/sites-available/aether`:
```nginx
server {
    listen 80;
    server_name ro.yourserver.com;
    root /var/www/aether/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```
Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/aether /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

## 3. SELinux Policies (RHEL / AlmaLinux / CentOS)

If running on a system with SELinux enabled, grant HTTP write permissions to cache and storage:
```bash
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/aether/storage(/.*)?"
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/aether/bootstrap/cache(/.*)?"
sudo restorecon -Rv /var/www/aether

# Allow Nginx / Apache to connect to network databases & sockets:
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_can_network_connect_db 1
```

---

## 4. Docker Deployment (Single Command)

For containerized environments on Linux, Windows, or Cloud VPS:
```bash
# Start AetherCP and MariaDB
docker compose up -d

# Run installer inside the container
docker compose exec aether-app php artisan aether:install
```

Access the panel immediately at `http://localhost:8080`.

---

## 5. Case-Sensitivity & Path Portability Rules

1. **Namespace & File Exact Casing**: All classes in `app/` strictly match file casing (`App\Http\Controllers\Public\GuildController` in `app/Http/Controllers/Public/GuildController.php`), eliminating Linux classloader mismatches.
2. **Directory Separators**: All internal path operations use `DIRECTORY_SEPARATOR` or URL forward-slashes (`/`), guaranteeing identical behavior across Windows NTFS and Linux ext4/btrfs.
3. **Database Reserved Word Quoting**: Emulator tables such as `` `char` `` are universally quoted across MariaDB and MySQL versions.

