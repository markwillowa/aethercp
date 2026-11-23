# AetherCP – Database Compatibility & Architecture Specification

## 1. Overview
AetherCP operates with a **dual database architecture**:
1. **AetherCP Application Database (`aether`)**: Manages web-specific entities such as CMS news, downloads, donation transactions, site settings, audit logs, and web session states.
2. **Ragnarok Emulator Database (`ragnarok` / `freynix`)**: The live game server database containing player accounts, character records, guilds, inventories, and server state.

---

## 2. Ragnarok Emulator Schema Comparison

| Table / Feature | rAthena (Modern / Renewal) | Hercules | Pre-Renewal Notes |
| :--- | :--- | :--- | :--- |
| **Login Table** | `login` | `login` | Standardized across both |
| - Primary Key | `account_id` (INT UNSIGNED) | `account_id` (INT UNSIGNED) | Auto-increment starting at 2000000 |
| - Username Field | `userid` VARCHAR(23) | `userid` VARCHAR(23) | Case sensitivity configurable |
| - Password Field | `user_pass` VARCHAR(32) or VARCHAR(60) | `user_pass` VARCHAR(32) or VARCHAR(64) | MD5 (32), Bcrypt (60), SHA256 (64) |
| - Group/Level | `group_id` (TINYINT) | `group_id` (TINYINT) | 0 = Normal player, 1-99 = GM tiers |
| - Account State | `state` (INT UNSIGNED, 0=ok) | `state` (INT UNSIGNED, 0=ok) | Non-zero indicates ban/blocked |
| - Gender | `sex` ENUM('M', 'F', 'S') | `sex` ENUM('M', 'F', 'S') | 'S' = Server / Service account |
| **Character Table** | `` `char` `` (reserved SQL word) | `` `char` `` (reserved SQL word) | Must always be quoted with backticks |
| - Primary Key | `char_id` (INT UNSIGNED) | `char_id` (INT UNSIGNED) | Auto-increment |
| - Foreign Key | `account_id` | `account_id` | References `login.account_id` |
| - Trait Stats | `pow, sta, wis, spl, con, crt` | Absent in Pre-Renewal | Only present in Renewal (4th jobs) |
| - Max AP | `max_ap, ap` | Absent in Pre-Renewal | Trait action points |
| - Clothes/Body | `clothes_color, body` | `clothes_color, body` | Present in modern versions |
| **Guild Table** | `guild` | `guild` | |
| - Primary Key | `guild_id` (INT UNSIGNED) | `guild_id` (INT UNSIGNED) | |
| - Master Info | `char_id` + `master` VARCHAR(24) | `char_id` + `master` VARCHAR(24) | |
| - Emblem Data | `emblem_data` (BLOB) | `emblem_data` (BLOB) | Gzipped 24x24 BMP image |
| **Inventory** | `inventory` | `inventory` | Per-character item storage |
| - Slot / Location | `equip` INT | `equip` INT | Bitmask of equipment locations |
| - Random Options | `option_id*`, `option_val*` | Configurable | Modern renewal feature |
| **Item Database** | `item_db` / `item_db2` | `item_db` / `item_db2` | Server items or YAML-backed |

---

## 3. Password Hashing Specification

Ragnarok emulators support several password hashing algorithms:
1. **MD5 Hashing (Standard Legacy)**:
   - Output: 32 lowercase hex characters (`md5($password)`).
   - Supported by: rAthena default, Hercules legacy.
2. **Bcrypt Hashing (Modern rAthena)**:
   - Output: 60-character string starting with `$2y$` or `$2a$`.
   - Supported by: Modern rAthena with `password_hash($password, PASSWORD_BCRYPT)`.
3. **SHA256 Hashing (Hercules Modern)**:
   - Output: 64-character lowercase hex string (`hash('sha256', $password)`).
   - Supported by: Hercules with `password_encrypt: 2`.
4. **Plain Text**:
   - Used only in testing/debug environments.

### Hashing Strategy in AetherCP:
`App\Services\Emulator\EmulatorPasswordHasher` dynamically inspects the configured emulator driver and the stored hash format:
- When checking password on login:
  - If stored hash starts with `$2y$` or `$2a$`: verifies using `password_verify()`.
  - If length is 64 characters: verifies against `hash('sha256', $password)`.
  - If length is 32 characters: verifies against `md5($password)`.
  - Otherwise fallback to configured emulator default or plaintext comparison.
- When generating password on registration or change:
  - Formats according to `config('emulator.password_hash')` (`bcrypt`, `md5`, `sha256`).

---

## 4. Guild Emblem Rendering

Guild emblems in Ragnarok Online are stored as raw compressed binary data inside `guild.emblem_data`:
- Header: Gzip-compressed binary blob or raw 24x24 24-bit bitmap (`.bmp`).
- Processing in AetherCP:
  - Read `emblem_data` from `guild` table.
  - If compressed (starts with `\x1f\x8b`), decompress with `gzuncompress()` or `gzinflate()`.
  - Parse 24x24 BMP structure or use PHP GD / Imagick to render a high-quality PNG with transparency support for magenta (`#FF00FF`) mask.
  - Cache generated PNG files in `public/storage/emblems/` for blazing-fast web delivery.

---

## 5. AetherCP Application Database Tables

The AetherCP web database manages:
1. `aether_users`: Links to game `account_id` with web-specific profile, avatar, preferences, roles.
2. `aether_news`: News articles, announcements, patch notes with category and author.
3. `aether_downloads`: Client files, lite installers, cumulative patches with mirror URLs and file sizes.
4. `aether_donations`: Donation tiers, transaction logs, reward claims, payment status.
5. `aether_settings`: Key-value configuration for site title, discord links, rates display, maintenance mode.
6. `aether_activity_logs`: Administrative actions audit trail (bans, item edits, CMS updates).
