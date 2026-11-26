# AetherCP — Next-Generation Ragnarok Online Control Panel

<p align="center">
  <img src="https://raw.githubusercontent.com/lucide-icons/lucide/main/icons/shield.svg" width="80" height="80" alt="AetherCP Shield">
</p>

AetherCP is an independent, modern, high-performance web-based control panel engineered for **rAthena** and **Hercules** Ragnarok Online private servers. Built from the ground up on PHP 8.3+, Laravel 12, Tailwind CSS 4, Alpine.js, and Lucide Icons, AetherCP delivers an extensible architecture with zero legacy baggage.

---

## Key Highlights & Features

### 1. Centralized Escrow Inbox (Web-to-Game Item Transfer)
- **Unified Inventory**: All rewards and web transactions—including **Daily Attendance Rewards**, **Marketplace Purchases**, **PvP / MVP Season Ranking Rewards**, and **Donation Package Bonus Items**—are safely held in the player's account **Escrow Inbox**.
- **Dual Transfer Destinations**: Players choose whether to transfer held items directly to:
  - **Account Kafra Storage** (`storage` table)
  - **Character Inventory** (`inventory` table)
- **Online Safety Checks**: Validates character offline status before performing inventory writes to prevent item duplication or emulator state overwrites.
- **Audit & History**: Comprehensive tracking of claim dates, item IDs, quantities, source campaign/system, and transfer logs.

### 2. Configurable Daily Rewards & Attendance Engine
- **Arbitrary Campaign Durations**: Fully flexible campaign schedules not limited to 7 days—supports **5, 7, 14, 30, 60, 90, 180, 365+ days** or any custom duration.
- **Reward Schedule Generator**: Administrators can automatically generate daily reward tiers with custom milestone multipliers (e.g. Day 7, 14, 21, 30 jackpot rewards).
- **Multi-Item & Zeny Packages**: Each day can contain multiple items and Zeny bundles delivered atomically into the Escrow Inbox.
- **Account-Wide Eligibility**: Tracks attendance at the account level so rewards cannot be multi-claimed across alternate characters.
- **Flexible Progression Modes**: Supports both consecutive login streaks (reset on missed day) and cumulative attendance modes.

### 3. Live Rankings & Categorized Badges
- **Real-Time Leaderboards**: Cached, low-overhead ranking views for **Base Level**, **Job Level**, **PvP Kills & KDR**, **MVP Boss Kills**, and **Guild EXP / Castle Castles**.
- **Category-Isolated Badges**: Badges are strictly segmented by domain—Base Level leaderboards only show Base progression badges; PvP leaderboards only show PvP combat badges; MVP leaderboards only show MVP conqueror badges.
- **Automated Ranking Seasons**: Configurable season reward schedules with automatic prize distribution into winners' Escrow Inboxes.

### 4. Character Management, Self-Services & Privacy Controls
- **Player Character Management (`/account/characters/{id}/manage`)**: Tabbed character console featuring full **Profile Overview, Attributes, Equipment**, **Self-Services** (Character Rename, Appearance Change, Stat Reset, Skill Reset, Account-Level Gender Change, Self-Lock, and Waiting-Period Deletion/Cancellation), **Profile Customization & Privacy** (featured portrait/avatar, bio, and granular visibility toggles), and **Service History**.
- **Admin Character Administration (`/admin/characters` & `/admin/characters/settings`)**: Searchable/filterable character roster, full inventory/equipment/cart/storage inspection & item delivery (via Escrow Inbox or offline insert), offline-guarded attribute editing, moderation actions (lock/unlock, move to save point, reset stats/skills, recovery approval, internal notes), Cash Point service refunds, configurable pricing/cooldowns, and complete audit logs.

### 5. Redeem Code & Campaign System
- **Admin Campaign & Code Generator (`/admin/redeem`)**: Multi-campaign management, single/bulk secure hashed code generation with one-time CSV export, configurable eligibility restrictions (level, job class, account age, per-account/per-character limits), and Cash Point, Zeny, Item, and Custom rewards.
- **Escrow-First Redemption**: Player-facing `/account/redeem` validates eligibility, previews rewards, and deposits Zeny and Item rewards directly into the **Escrow Inbox** so players choose the receiving character safely.

### 6. Enhanced Item Database & Player Marketplace
- **Visual Divine Pride Integration**: Dynamic sprite and collection image lookups for weapons, armor, headgears, and cards.
- **Player-to-Player Web Marketplace**: Browse, list, and buy in-game items through the web control panel, with items escrowed automatically.

### 6. Dynamic Theming System
- **Aether Chronicles (Commercial Premium)**: Faithful reproduction of classic Episode XIII & Noghaltz promotional composition. Features ornate medieval stone frames, engraved metallic borders, dark fantasy stone backgrounds, gold filigree buttons, aged parchment cards, and a 3-column desktop layout.
- **Aether Prestige (Flagship)**: Ultra-luxury AAA MMORPG aesthetic featuring ambient Ken-Burns hero motion, floating atmospheric light particles, staggered entrance animations, and a bespoke **Sovereign Treasury Vault** interactive donation console.
- **Aether Dark (Default)**: Sleek ethereal cyan & electric blue neon aesthetic.
- **Elysian Mythic**: Midnight sapphire and Nordic gold celestial design.
- **Ragnarok Classic**: Nostalgic parchment-and-wood aesthetic reminiscent of classic Ragnarok Online portals.

### 7. Dual Installation Modes (Automated CLI or phpMyAdmin)
- **Interactive Web Installation Wizard**: 6-step guided setup (`/install`) with pre-flight environment diagnostics, real-time database connection testing, and game server network rates configuration.
- **cPanel & Shared Hosting Plug & Play**: Pre-generated SQL dumps (`aether_schema.sql`, `aether_seeders.sql`, `community_seeders.sql`, `rewards_badges_seeders.sql`, `cash_shop_seeders.sql`) plus **non-destructive per-migration SQL files** in `database/sql/migrations/` allow safe incremental phpMyAdmin imports without affecting existing data.
- **Protected Session Handling**: Built-in `file` session driver default prevents the notorious `Table 'sessions' doesn't exist` error on uninstalled shared hosting environments.

### 8. Community Forum, Character Identity & Reputation System
- **Modern Gaming Forum**: Categorized discussion boards, server-side pagination, sorting (`Latest Activity`, `Newest`, `Most Replies`, `Most Viewed`), filtering (`All`, `Unanswered`, `Solved`, `Pinned`), topic subscriptions, content reporting, and staff moderation (`Pin`, `Lock`, `Approve Guide`, `Moderator Recognition`).
- **Rich Discussion Experience**: Safe XSS-protected post formatting with quote insertion, `@username` mentions, live preview, character counter, duplicate-submission protection, and validated file attachments.
- **Optional Ragnarok Character Identity**: Players can switch between **Aether Username Only** or **Display Selected Ragnarok Character** (sprite, name, job class, base/job levels) with strict server-side ownership verification against the emulator database.
- **Public Member Profiles**: Tabbed member profiles (`Overview`, `Discussions`, `Replies`, `Reputation History`, `Achievements`) with strict privacy controls (never exposing emails, passwords, or private account data).
- **Reputation Ledger, Levels & Leaderboard**: Transactional reputation ledger (`reputation_transactions`) with anti-farming protections, reaction reversal, configurable levels & badge appearances (`Newcomer` through `Community Legend`), period-filtered Community Leaderboard (`All Time`, `This Month`, `This Week`), and full Admin Reputation Settings & audited manual adjustments.

---

## Technical Stack

- **Backend**: PHP 8.3+ / PHP 8.4+ (Laravel 12 core, Eloquent ORM, CSRF & XSS protection, rate limiting)
- **Frontend**: Blade Components, Tailwind CSS 4, Alpine.js, Lucide Icons, Vite
- **Web Servers Supported**: Apache 2.4+, Nginx 1.20+, LiteSpeed, Caddy
- **Database**: MariaDB 10.4+ / MySQL 8.0+
- **Supported Emulators**: rAthena (Pre-Renewal & Renewal), Hercules (Pre-Renewal & Renewal)

---

## Database Architecture & Important Setup Rules

> [!IMPORTANT]
> **Do NOT create Laravel migrations for rAthena or Hercules game tables!**
> 
> AetherCP strictly isolates the emulator database from the web application database:
> 
> 1. **Ragnarok Game Database (e.g. `freynix` or `ragnarok`)**:
>    - **Requirement**: Import the official rAthena or Hercules SQL files (`sql-files/main.sql`, `sql-files/item_db.sql`, `sql-files/logs.sql`, etc.) directly into your game database using MySQL CLI or phpMyAdmin.
>    - **Zero Migrations**: Game tables (`login`, `char`, `guild`, `inventory`, `storage`, `item_db`, etc.) are 100% owned by the game server emulator and are **never** altered or generated by Laravel migrations.
>    - **Single Source of Truth**: The web control panel authenticates directly against the emulator `login` table. Any account in `login` (such as `s1`, `admin`, or newly registered players) can immediately log in on the website.
> 
> 2. **AetherCP Web Database (e.g. `aether_cp`)**:
>    - Contains web-specific tables (`news`, `downloads`, `donations`, `donation_packages`, `site_settings`, `activity_logs`, `player_inbox_items`, `attendance_campaigns`, `badge_definitions`, `forum_categories`, `forum_boards`, `forum_topics`, `forum_posts`, `reputation_levels`, `reputation_transactions`, `users`).
>    - Laravel migrations apply **only** to this separate web database.
>    - For manual phpMyAdmin updates on live servers, use the matching per-migration SQL files in `database/sql/migrations/` (1 migration = 1 `.sql` file) and the non-destructive SQL seeders (`community_seeders.sql`, `rewards_badges_seeders.sql`, `cash_shop_seeders.sql`)—or run `php artisan db:seed --class=CommunityRewardsBadgesSeeder`.

---

## Universal Installation Guides

AetherCP supports both a **Web-Based Setup Wizard** (no terminal needed) and a **CLI Artisan Command** for automated server deployments.

```
┌─────────────────────────────────────────────────────────────┐
│                   Select Deployment Target                  │
├─────────────────────────────────────────────────────────────┤
│ 1. Windows (XAMPP / Laragon / Native)                       │
│ 2. Linux & Ubuntu (Nginx + PHP-FPM / Apache2)               │
│ 3. Shared Web Hosting (cPanel / DirectAdmin / Plesk)        │
│ 4. Docker & Docker Compose                                  │
│ 5. Advanced Headless / CLI Deployment                       │
└─────────────────────────────────────────────────────────────┘
```

---

### 1. Windows Installation (XAMPP or Laragon)

#### Prerequisites
- Install [XAMPP for Windows](https://www.apachefriends.org/) (PHP 8.2, 8.3, or 8.4) or [Laragon](https://laragon.org/).
- Install [Composer for Windows](https://getcomposer.org/download/) (required to install PHP package dependencies).
- Install [Node.js & npm](https://nodejs.org/) (LTS version - **required** to compile frontend assets and generate the Vite manifest).
- Ensure Apache and MySQL services are started in the XAMPP Control Panel.

#### Step-by-Step Setup
1. **Clone or Extract AetherCP**:
   Extract or clone the project into your web root (`htdocs`):
   ```powershell
   Set-Location C:\xampp\htdocs
   git clone https://github.com/markwillowa/aethercp.git aether
   Set-Location C:\xampp\htdocs\aether
   ```

2. **Install Composer Dependencies**:
   > [!IMPORTANT]
   > Like standard PHP/Laravel applications, third-party libraries in `vendor/` are **excluded from Git**. You **must** run `composer install` once after cloning to generate `vendor/autoload.php` and avoid the error `Failed opening required vendor/autoload.php`.
   ```powershell
   composer install
   ```

3. **Initialize Environment & Application Key**:
   Create your local `.env` configuration file from the template and generate the application security key:
   ```powershell
   copy .env.example .env
   php artisan key:generate
   ```

4. **Install Node Dependencies & Compile Frontend Assets**:
   > [!IMPORTANT]
   > The `public/build/` directory is **excluded from Git**. You **must** run `npm install` and `npm run build` to compile the CSS, JavaScript, and generate `public/build/manifest.json`. Skipping this step causes `Illuminate\Foundation\ViteManifestNotFoundException: Vite manifest not found`.
   ```powershell
   npm install
   npm run build
   ```

5. **Verify PHP Extensions in `php.ini`**:
   Open `C:\xampp\php\php.ini` and verify that the following extensions are uncommented (no leading `;`):
   ```ini
   extension=pdo_mysql
   extension=mbstring
   extension=openssl
   extension=curl
   extension=fileinfo
   extension=gd
   extension=zip
   ```
   *Restart Apache in XAMPP after editing `php.ini`.*

6. **Import Emulator SQL Files**:
   Open **phpMyAdmin** (`http://localhost/phpmyadmin`) or MySQL CLI and create your game database (e.g. `freynix`). Import your emulator's SQL files:
   - For rAthena: Import `sql-files/main.sql` and `sql-files/item_db.sql`.
   - For Hercules: Import `sql-files/main.sql`.

7. **Run the Web Installation Wizard**:
   Open your browser and navigate to:
   ```
   http://localhost/aether/
   ```
   The interactive wizard will automatically launch:
   - **Step 1: Pre-flight Checks**: Confirms PHP runtime, extensions, and directory permissions.
   - **Step 2: Realm Settings**: Server name, control panel branding, and URL.
   - **Step 3: Database & Ragnarok Config**: Test Web DB connection and Ragnarok Game DB connection; configure game server pings and gameplay rates (Base/Job/Drop rates, Max Level).
   - **Step 4: Provisioning Stage**:
     - *Mode A*: Automated 1-Click Provisioning.
     - *Mode B*: Manual phpMyAdmin SQL Import.
   - **Step 5: Administrator Account Setup**:
     - *Elevate Existing Game Account (Recommended)*: Enter your existing game account userid to grant GM level 99 (uses your existing in-game password).
     - *Create Standalone Web Administrator*: Enter a custom admin username, email, and password.
   - **Step 6: Complete Installation**: Displays confirmation and direct links to the site and `/admin`.

---

### 2. Linux & Ubuntu (Nginx + PHP-FPM / Apache2)

#### Step-by-Step Setup
1. **Clone the Repository**:
   ```bash
   cd /var/www
   git clone https://github.com/markwillowa/aethercp.git aether
   cd /var/www/aether
   ```

2. **Install Dependencies & Build Assets**:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure File Permissions**:
   ```bash
   sudo chown -R www-data:www-data /var/www/aether
   sudo chmod -R 775 /var/www/aether/storage /var/www/aether/bootstrap/cache
   ```

4. **Nginx Server Block**:
   ```nginx
   server {
       listen 80;
       server_name ro.yourdomain.com;
       root /var/www/aether/public;

       index index.php index.html;
       charset utf-8;

       location ~ /\.(?!well-known).* {
           deny all;
       }

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }

       error_page 404 /index.php;

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
           fastcgi_hide_header X-Powered-By;
       }
   }
   ```
   Enable and restart Nginx:
   ```bash
   sudo ln -s /etc/nginx/sites-available/aether /etc/nginx/sites-enabled/
   sudo nginx -t && sudo systemctl reload nginx
   ```

5. Complete installation via `http://ro.yourdomain.com/install`.

---

### 3. Shared Web Hosting (cPanel / DirectAdmin / Plesk)

AetherCP is specifically designed to run on shared web hosting with **zero terminal or SSH access required**.

#### Step 1: Upload Files via File Manager or FTP
- Download or zip the AetherCP release package (including `vendor/` and `public/build/`).
- Open your hosting **File Manager** in cPanel.
- **Root Domain Deployment** (e.g. `https://yourserver.com`):
  - Upload and extract all files directly inside `public_html/`.
  - The included `.htaccess` automatically routes requests into `public/` while securing your `.env` and source code.
- **Subdirectory Deployment** (e.g. `https://yourserver.com/cp/`):
  - Upload and extract into `public_html/cp/`.
  - Works automatically out of the box with clean URLs.

#### Step 2: Create Databases in cPanel
1. Open **MySQL Databases** or **MySQL Database Wizard** in cPanel.
2. Create your AetherCP web database: `cpaneluser_aether`.
3. Create your Ragnarok game database: `cpaneluser_ragnarok`.
4. Create a database user, assign a password, and grant **ALL PRIVILEGES** to both databases.

#### Step 3: Import Ragnarok Emulator SQL
- Open **phpMyAdmin** from cPanel.
- Select your game database (`cpaneluser_ragnarok`).
- Click **Import** and upload your emulator's `main.sql` and `item_db.sql` (from your rAthena or Hercules repository).

#### Step 4: Run the Web Installer (Two Provisioning Choices)
Open `https://yourserver.com/` (or `https://yourserver.com/cp/`) in your browser:
- **Automatic `.env` Creation**: If `.env` is missing on a fresh upload, AetherCP automatically creates `.env` from `.env.example` with a freshly generated encryption key, file session driver, and file cache store.
- **Step 3 (Database Connection Enforcement)**:
  - Enter your database credentials for both the **AetherCP Web Application Database** and the **Ragnarok Game Server Database**.
  - Click **"Test Connection"** on both sections. Connectivity must be verified before the wizard allows proceeding to Step 4.
  - Server network status and gameplay rates are optional and will not block installation if your emulator server is currently offline.
- **Step 4 (Seeder Selection & Provisioning)**:
  - **Select Seeders & Starter Content**: Select which content packages you wish to upload/install (Core Site Settings, Badges & Ranking Tiers, Daily Attendance Rewards, Donation Packages, Sample News & Articles, and Client Download Mirrors).
  - **Option A (Automated 1-Click)**: Runs database migrations and seeds ONLY your selected seeders directly via PHP.
  - **Option B (phpMyAdmin Manual SQL Import)**:
    1. Download `aether_schema.sql` (all tables, indexes, and migrations tracking).
    2. Download your tailored `aether_seeders.sql` (dynamically filtered to contain only your chosen seeders).
    3. In phpMyAdmin, click your web database (`cpaneluser_aether`) and click **Import** for each file.
    4. Click **"I Have Imported the SQL Files • Verify & Proceed"** in the wizard.
- **Step 5**: Configure your administrator credentials, then click **Complete Installation**.

> [!TIP]
> **No `sessions` Table Crashes on cPanel**: Fresh uploads on cPanel default to `SESSION_DRIVER=file`, ensuring requests never query a non-existent database session table before installation!

---

### 4. Docker & Docker Compose Deployment

AetherCP includes production-ready containerization files out of the box:

```bash
docker compose up -d
```
- **`aether_control_panel`**: PHP 8.4 + Apache web application container on port `8080`.
- **`aether_database`**: MariaDB 10.11 container.

Navigate to `http://localhost:8080/` to configure the application.

---

### 5. Advanced Headless / CLI Deployment (Sysadmins)

For automated server provisioning or CI/CD pipelines:

```bash
# 1. Clone repository
git clone https://github.com/markwillowa/aethercp.git
cd aether

# 2. Install dependencies & build assets
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Setup environment file
cp .env.example .env
php artisan key:generate

# 4. Run automated installation command (creates database, tables, and default admin)
php artisan aether:install

# 5. Lock the installer
php -r "file_put_contents('storage/installed', json_encode(['installed_at' => date('c'), 'version' => '1.0.0']));"
```

---

## Background Task Scheduling (Cron Job)

To enable automatic ranking caching, server uptime heartbeats, donation polling, and daily attendance resets, add a single cron entry to your server:

```bash
crontab -e
```
Add the following line:
```cron
* * * * * cd /path/to/aether && php artisan schedule:run >> /dev/null 2>&1
```

*(On cPanel, navigate to **Cron Jobs**, set frequency to `* * * * *` (Every Minute), and enter `/usr/local/bin/php /home/cpaneluser/public_html/artisan schedule:run >/dev/null 2>&1`)*.

---

## Automated Test Suite

AetherCP maintains a comprehensive unit and feature test suite. Run tests via:

```bash
php artisan test
```

Automated tests validate:
- Emulator password auto-detection across Plaintext, MD5, SHA256, and Bcrypt
- Pre-Renewal and Renewal stat and column detection (preventing SQL 1054 errors)
- Public routes, ranking caches, database searches, and downloads
- Escrow Inbox transfers to Kafra Storage and Character Inventory with offline checks
- Attendance campaign claim validation, consecutive streak rules, and multi-day packages
- Categorized badge assignment and leaderboards isolation
- Community Forum, topic/reply permissions, XSS & upload validation, optional Ragnarok character ownership verification, transactional reputation ledger, and audited admin adjustments (`ForumAndReputationTest`)
- Dynamic theme switching (Aether Dark, Aether Chronicles, Aether Prestige, Elysian Mythic, and Ragnarok Classic)
- Installation wizard pre-flight validation, SQL downloads, and lock gating

---

## Troubleshooting & FAQ

### 1. `Table '...sessions' doesn't exist (Connection: mysql)`
- **Cause**: Laravel's session driver was set to `database` before the database tables were created.
- **Solution**: AetherCP is pre-configured with `SESSION_DRIVER=file` in `.env.example` and dynamic file session fallback during installation. If you encounter this on a manual setup, ensure `SESSION_DRIVER=file` and `CACHE_STORE=file` in your `.env` file until migrations have run.

### 2. `Fatal error: Failed opening required '.../vendor/autoload.php'`
- **Cause**: Third-party PHP libraries in `vendor/` have not been installed yet.
- **Solution**: Run `composer install` in your project root directory.

### 3. `Illuminate\Foundation\ViteManifestNotFoundException: Vite manifest not found`
- **Cause**: The compiled frontend asset directory `public/build/` is gitignored.
- **Solution**: Run `npm install && npm run build` to compile frontend CSS and JavaScript assets.

### 4. `No application encryption key has been specified`
- **Cause**: The application is missing its `.env` configuration file or security key.
- **Solution**: Run `copy .env.example .env` (or `cp .env.example .env`) and `php artisan key:generate`.

### 5. `Call to undefined function ...` or Missing PHP Extension
- **Cause**: Required PHP modules are disabled in `php.ini`.
- **Solution**: Ensure `extension=pdo_mysql`, `extension=mbstring`, `extension=openssl`, `extension=curl`, `extension=fileinfo`, `extension=gd`, and `extension=zip` are active (uncommented) in `php.ini`, then restart your web server.

---

## License

AetherCP is open-source software licensed under the [MIT license](LICENSE).
