# AetherCP — Next-Generation Ragnarok Online Control Panel

<p align="center">
  <img src="https://raw.githubusercontent.com/lucide-icons/lucide/main/icons/shield.svg" width="80" height="80" alt="AetherCP Shield">
</p>

AetherCP is an independent, modern, high-performance web-based control panel engineered for **rAthena** and **Hercules** Ragnarok Online private servers. Built from the ground up on PHP 8.4+, Laravel 12, Tailwind CSS, Alpine.js, and Lucide Icons, AetherCP delivers an extensible architecture with zero legacy baggage.

---

## Key Highlights

- **Dual Database Architecture**: Complete isolation between the AetherCP web application database (`aether_cp`) and the live game server databases (`login`, `char`, `map`).
- **Emulator Abstraction Layer**: Pluggable driver system supporting **rAthena** and **Hercules** across both **Pre-Renewal** and **Renewal** modes.
- **Universal Password Hashing**: Auto-detects and verifies password hashes across Bcrypt, SHA256 (Hercules), MD5 (Standard RO), and plaintext, completely preventing account lockouts during migrations.
- **Modern MMORPG Dark Theme ("Aether Dark")**: Sleek deep-navy slate background, ethereal cyan and electric blue glowing accents, responsive on desktop, tablet, and mobile.
- **Live Server Pings**: High-precision cluster pings (Login, Character, Map servers) and live online player tracking.
- **Player & Guild Leaderboards**: High-performance cached rankings for Base Level, Job Level, Zeny, MVP Fame, and Guild EXP.
- **Player Tools**: Self-service unstuck (reset position to Prontera), in-game 4-digit security PIN changes, password updates, and character inspection.
- **Comprehensive Administration**: Full GM dashboard, account search with ban/unban toggles, character viewer, news and announcement CMS, donation claim reviews, and system audit logging.
- **Extensible Module & Theme System**: Declare and manage modules and themes with clean JSON manifests and admin control.

---

## Technical Stack

- **Backend**: PHP 8.4+ / Laravel 12 (Eloquent ORM, robust routing, CSRF & XSS protection, rate limiting)
- **Frontend**: Blade Components, Tailwind CSS 4, Alpine.js, Lucide Icons, Vite
- **Database**: MariaDB 10.11+ / MySQL 8.0+
- **Supported Emulators**: rAthena (PR/Renewal), Hercules (PR/Renewal)

---

## Directory Architecture

```text
aether/
├── app/
│   ├── Console/Commands/       # Artisan commands (aether:install)
│   ├── Contracts/              # EmulatorInterface, PasswordHasherInterface, Repositories
│   ├── Http/
│   │   ├── Controllers/        # Auth, Account, Public, and Admin controllers
│   │   └── Middleware/         # EnsureUserIsAdmin
│   ├── Models/                 # User, News, Download, Donation, DonationPackage, SiteSetting, ActivityLog
│   ├── Providers/              # EmulatorServiceProvider, AppServiceProvider
│   ├── Repositories/           # AccountRepository, CharacterRepository, GuildRepository, RankingRepository, ItemRepository
│   └── Services/
│       ├── Emulator/           # EmulatorManager, EmulatorPasswordHasher, RAthenaAdapter, HerculesAdapter
│       ├── Module/             # ModuleManager
│       └── Theme/              # ThemeManager
├── config/
│   ├── aether.php              # Site settings, rates, social links, download mirrors
│   ├── emulator.php            # Driver selection, renewal flag, hashing, server sockets, table maps
│   └── modules.php             # Module feature flags and declarations
├── database/
│   ├── migrations/             # AetherCP web application database migrations
│   └── seeders/                # AetherDatabaseSeeder (admin, default news, downloads, packages)
├── docs/                       # Technical specs, compatibility matrices, deployment guides
├── modules/                    # Pluggable community modules
├── resources/
│   ├── css/                    # Tailwind CSS configuration and dark theme utilities
│   ├── js/                     # Alpine.js & Lucide icon initializers
│   └── views/                  # Blade templates (layouts, components, auth, account, public, admin)
├── routes/
│   ├── web.php                 # Public, Player, and Admin routes
│   └── console.php             # Artisan console schedule and commands
├── tests/
│   ├── Feature/                # PublicPagesTest, AdminManagementTest
│   └── Unit/                   # EmulatorPasswordHasherTest, EmulatorAdapterTest
└── themes/                     # Pluggable theme directory
```

---

## Quick Installation Guide

### 1. Requirements
Ensure your environment meets the minimum requirements:
- PHP 8.3+ with `pdo_mysql`, `mbstring`, `openssl`, `curl` extensions enabled
- MariaDB 10.4+ or MySQL 8.0+
- Node.js 20+ and NPM
- Composer 2+

### 2. Configure Environment
Copy the example environment file and configure your credentials:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your `.env` database connections:
```env
# AetherCP Web Application Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aether_cp
DB_USERNAME=admin
DB_PASSWORD=your_password

# Ragnarok Live Game Database
RO_DB_HOST=127.0.0.1
RO_DB_PORT=3306
RO_DB_DATABASE=freynix
RO_DB_USERNAME=admin
RO_DB_PASSWORD=your_password

# Emulator Configuration
RO_SERVER_NAME="Freynix RO"
RO_EMULATOR=rathena
RO_RENEWAL=true
RO_PASSWORD_HASH=md5
```

### 3. Run One-Click Installation
```bash
php artisan aether:install
```

### 4. Build Frontend Assets
```bash
npm install
npm run build
```

### 5. Access the Panel
- Public Home: `http://localhost/aether/public/`
- Default Admin Login:
  - **Username**: `admin`
  - **Password**: `Admin1234!`
  - **Admin URL**: `http://localhost/aether/public/admin`

---

## Testing

Run the automated test suite with PHPUnit:
```bash
php artisan test
```

All 22 unit and feature tests validate:
- Emulator password auto-detection (MD5, Bcrypt, SHA256)
- Pre-Renewal and Renewal stat column differences
- Public and account routes
- Role-based admin guards and moderation tools

---

## License

AetherCP is open-source software licensed under the [MIT license](LICENSE).
