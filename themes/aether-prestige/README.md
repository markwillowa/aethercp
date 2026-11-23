# AETHER PRESTIGE — Commercial Theme for AetherCP

> **"Elevate Your World. Define Your Legacy."**

**Aether Prestige** is the flagship premium commercial theme designed specifically for **AetherCP** and modern Ragnarok Online private server communities. Crafted with a luxury dark MMORPG visual identity, cinematic widescreen hero presentation, champagne gold metallic accents, and deep glassmorphic surfaces, Aether Prestige transforms any rAthena or Hercules server into a premier gaming destination.

---

## 💎 Product Specifications

| Metric | Specification |
| :--- | :--- |
| **Product Name** | Aether Prestige |
| **Category** | Premium Commercial Theme |
| **Suggested Price** | $29 – $49 USD |
| **Platform** | AetherCP v1.x+ |
| **Emulator Compatibility** | rAthena & Hercules (Renewal & Pre-Renewal) |
| **Design Positioning** | Luxury / Cinematic / High-End MMORPG |
| **Current Version** | 1.0.0 |
| **License** | Commercial Single-Server License |

---

## 🎨 Visual Design System

- **Background Palette**: Midnight Navy (`#101725`), Deep Charcoal (`#151B26`)
- **Surfaces**: Secondary Slate (`#1B2432`), Elevated Card (`#222D3C`)
- **Accents**: Champagne Gold (`#C6A76A`), Soft Gold (`#D8BE8A`), Platinum (`#D9DEE7`), Ethereal Cyan (`#2DD4BF`)
- **Typography**: 
  - Headings & Titles: `Cinzel` / `Cinzel Decorative` (Classic Roman Serif)
  - Interface & Body: `Plus Jakarta Sans` & `Inter`
- **Borders & Overlays**: Ultra-subtle frosted white (`rgba(255, 255, 255, 0.08)`) with selective champagne luminescence

---

## 🚀 Key Features

1. **Cinematic Hero Presentation**:
   - High-definition dark fantasy castle citadel background with atmospheric volumetric lighting and a lone plate-armored knight facing the fortress.
   - Elegant typography with luxury letter-spacing.
   - Dual Call-to-Actions (Glow "Register Free" + Frosted Glass "Download Client").
   - Integrated server rate telemetry indicators.

2. **Floating Interactive Trailers Widget**:
   - Right-side stacked floating trailer preview cards (`01`, `02`, `03`).
   - Built-in interactive modal video player supporting YouTube and custom media embeds.

3. **Live Server Telemetry Dashboard**:
   - Real-time daemon monitoring for Login, Char, and Map servers.
   - Live connected player count.
   - Gepard Shield anti-cheat status verification badge.

4. **Hall of Fame & Leaderboards**:
   - Dynamic tabs for Base Level, Job Level, Zeny Wealth, MVP Kills, and Guild Rankings.
   - Top 3 luxury podium badges (Champion Gold, Platinum Silver, Bronze).

5. **Integrated Codex & Item Database**:
   - Full search interface querying rAthena / Hercules item tables.
   - Category filtering, item scripts, card slots, and pricing details.

6. **War of Emperium (WoE) Fortress Standings**:
   - Real-time castle occupancy tracking with guild crest emblems.
   - Economy and defense metrics per agit.

7. **Sovereign Patronage & Cash Shop**:
   - Tiered donation package cards with "Most Favored Tier" highlights.
   - Native PayPal IPN checkout and manual payment instructions.

---

## 📦 Installation Guide

### Option A: Via AetherCP Admin Panel (Recommended)
1. Copy the `aether-prestige/` folder into your AetherCP installation under `themes/aether-prestige/`.
2. Log into the AetherCP Admin Panel at `/admin/themes`.
3. Locate **Aether Prestige** under **Installed Community Themes**.
4. Click **Activate Theme**. The theme views and assets will take effect immediately.

### Option B: Via Command Line / Environment
1. Set the active theme in your `.env` file:
   ```env
   AETHER_THEME=aether-prestige
   ```
2. Clear the compiled view cache:
   ```bash
   php artisan view:clear
   ```

---

## ⚙️ Customization & Branding

All branding and configuration is managed through `themes/aether-prestige/theme.json` or `.env`. No core code modifications are necessary.

### `theme.json` Configuration Options:
```json
{
    "name": "Aether Prestige",
    "slug": "aether-prestige",
    "customization": {
        "server_tagline": "Elevate Your World. Define Your Legacy.",
        "hero_title": "MYTHERA",
        "hero_subtitle": "The Lost Realms",
        "hero_description": "An epic open-world fantasy MMORPG where ancient powers clash, kingdoms fall, and heroes rise."
    }
}
```

### Media & Assets:
- Hero Background: Replace `assets/images/hero-bg.jpg` (Recommended size: 1920x1080 or 2560x1440).
- Trailer Thumbnails: Replace `assets/images/trailer-1.jpg`, `trailer-2.jpg`, `trailer-3.jpg` (16:9 ratio).

---

## 🛡️ Security & Performance

- **Zero Core Tampering**: Pure view and asset override architecture. Does not touch auth guards, database drivers, or emulator sockets.
- **CSRF & XSS Protection**: All forms utilize standard Blade `@csrf` directives and sanitized string output.
- **Optimized Assets**: Assets served through AetherCP's dynamic theme asset route with file-modification cache headers (`?v=TIMESTAMP`).

---

## 📜 Changelog

### v1.0.0 (Initial Commercial Release)
- Initial release of Aether Prestige.
- Complete suite of public and account views (Home, News, Rankings, Characters, Guilds, Item DB, Downloads, Donations, Auth).
- Built-in cinematic trailer modal system.
- rAthena and Hercules emulator compatibility tested.

