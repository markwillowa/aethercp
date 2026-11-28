# Security Policy

## Overview

AetherCP takes the security of its users, game servers, and player data seriously. This document outlines how to report vulnerabilities responsibly, what versions we actively support, and what to expect after a disclosure.

---

## Supported Versions

We actively patch security issues on the following versions:

| Version | Supported |
|---------|-----------|
| `main` (latest) | ✅ Yes — actively maintained |
| Older tagged releases | ⚠️ Best-effort only |
| Forks / unofficial builds | ❌ Not supported |

> [!IMPORTANT]
> Always run the latest commit on `main` or the most recent tagged release for security fixes.

---

## Reporting a Vulnerability

**Please do NOT open a public GitHub Issue for security vulnerabilities.** Doing so discloses the flaw to everyone before a fix is available.

### Preferred: GitHub Private Vulnerability Reporting

1. Go to the **Security** tab of this repository.
2. Click **"Report a vulnerability"** (GitHub's private advisory system).
3. Fill in the details — description, steps to reproduce, impact, and any proof-of-concept.
4. Submit. Only maintainers can see the report until it is disclosed.

> [!TIP]
> GitHub's private advisory is the fastest path. You'll receive confirmation within **48 hours** and a status update within **7 days**.

### Alternative: Email

If GitHub private reporting is unavailable, email the maintainers directly. Include `[AetherCP Security]` in the subject line.

---

## What to Include in Your Report

A good report helps us reproduce and fix the issue faster. Please include:

- **Description** — What is the vulnerability? (e.g., SQL injection, XSS, IDOR, auth bypass)
- **Affected component** — Which route, controller, model, or view is affected?
- **Steps to reproduce** — Minimal steps or a proof-of-concept (PoC)
- **Impact** — What could an attacker achieve? (data exfiltration, account takeover, RCE, etc.)
- **Environment** — PHP version, Laravel version, OS, database engine
- **Suggested fix** (optional but appreciated)

---

## Response Timeline

| Milestone | Target |
|-----------|--------|
| Initial acknowledgement | ≤ 48 hours |
| Triage & severity assessment | ≤ 7 days |
| Fix developed | ≤ 14–30 days (severity-dependent) |
| Coordinated public disclosure | After fix is released |

We follow **coordinated disclosure**: we will work with you on a mutually agreed disclosure date, typically after a patch is published.

---

## Scope

### In Scope

- Authentication and session management (`/login`, `/register`, token handling)
- Authorization and access control (admin routes, player account isolation)
- SQL injection or unsafe database queries affecting `ragnarok_db` or `aether_db`
- Cross-Site Scripting (XSS) in any public-facing or admin view
- Cross-Site Request Forgery (CSRF) bypasses
- Insecure Direct Object References (IDOR) on player accounts, characters, or items
- Remote Code Execution (RCE) via file upload, deserialization, or template injection
- Server-Side Request Forgery (SSRF)
- Sensitive data exposure (`.env`, credentials, player PII)
- Privilege escalation (player → staff, staff → admin)
- Pre-registration campaign data leakage
- Escrow inbox manipulation (unauthorized item claim or duplication)

### Out of Scope

- Vulnerabilities in third-party dependencies (report these upstream)
- Self-XSS or issues requiring full admin access to exploit admin features
- Rate limiting on non-sensitive endpoints
- Missing security headers without demonstrated impact
- Theoretical vulnerabilities without a working PoC
- Social engineering attacks
- Physical access attacks
- Issues on unofficial forks or custom deployments that modify core code

---

## Severity Classification

We use **CVSS v3.1** as a reference:

| Severity | CVSS Score | Examples |
|----------|-----------|---------|
| **Critical** | 9.0–10.0 | RCE, full auth bypass, mass data exposure |
| **High** | 7.0–8.9 | Account takeover, privilege escalation, SQL injection |
| **Medium** | 4.0–6.9 | CSRF, stored XSS, IDOR on non-sensitive data |
| **Low** | 0.1–3.9 | Reflected XSS, minor info disclosure |

---

## Security Best Practices for Deployers

If you are self-hosting AetherCP, follow these hardening steps:

### Environment
- [ ] Copy `.env.example` → `.env` and **never commit `.env`** to version control
- [ ] Set `APP_ENV=production` and `APP_DEBUG=false` in production
- [ ] Generate a strong app key: `php artisan key:generate`
- [ ] Use a long, random `APP_KEY` (32+ characters)

### Database
- [ ] Use a **dedicated MySQL user** for AetherCP with only the permissions it needs — do not use `root`
- [ ] Keep the Ragnarok DB credentials (`RO_DB_*`) separate from AetherCP's own DB credentials
- [ ] Enable `RO_DB_STRICT=false` only if required by your emulator; prefer strict mode

### Web Server (Apache/Nginx)
- [ ] Block direct access to `/storage`, `/bootstrap`, `/vendor`, `/database`, `/config`
- [ ] Serve only the `/public` directory as the document root
- [ ] Enable HTTPS with a valid TLS certificate (Let's Encrypt or equivalent)
- [ ] Add security headers: `X-Frame-Options`, `X-Content-Type-Options`, `Content-Security-Policy`, `Referrer-Policy`

### File Permissions
- [ ] `storage/` and `bootstrap/cache/` must be writable by the web server user
- [ ] All other directories should be readable but **not writable** by the web user
- [ ] File uploads must be stored **outside the public directory** (AetherCP defaults to `storage/app`)

### Session & Auth
- [ ] Use HTTPS to prevent session hijacking
- [ ] Keep `SESSION_LIFETIME` as short as practical
- [ ] Enable two-factor authentication (2FA) for admin accounts when available

### Updates
- [ ] Pull latest changes regularly: `git pull origin main`
- [ ] Run `composer install --no-dev` after updates in production
- [ ] Clear caches after updates: `php artisan optimize:clear`

---

## Known Security Features

AetherCP ships with the following security mechanisms by default:

| Feature | Implementation |
|---------|---------------|
| CSRF protection | Laravel's `VerifyCsrfToken` middleware on all state-changing routes |
| SQL injection prevention | Eloquent ORM + PDO prepared statements throughout |
| XSS prevention | Blade's auto-escaping (`{{ }}`) on all user-supplied output |
| Authentication | Laravel's `Auth` guard with bcrypt/argon2 password hashing |
| Authorization | Gate/Policy checks on all admin and player-sensitive actions |
| Rate limiting | Throttle middleware on login, registration, and pre-registration endpoints |
| File upload safety | MIME validation, size limits, storage outside public root |
| Character online check | Prevents item injection while character is logged into the game server |
| Session isolation | Per-user session tokens, invalidated on logout |

---

## Acknowledgements

We are grateful to the security researchers who responsibly disclose vulnerabilities. With your permission, we will credit you in the release notes or a Hall of Fame section.

---

## Legal

We ask that security researchers:

- **Act in good faith** — do not access, modify, or delete data beyond what is needed to demonstrate the vulnerability
- **Do not disrupt** live game servers or player data
- **Respect player privacy** — do not exfiltrate or retain real player PII
- **Give us reasonable time** to fix the issue before public disclosure

Researchers who follow this policy will not face legal action from us related to their security research.

---

*Last updated: November 2026*
