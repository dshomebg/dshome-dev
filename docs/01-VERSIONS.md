# Versions & Dependencies

**Последна актуализация:** 21 Октомври 2025

---

## 🖥️ Server Environment (Production - dshome.dev)

### Operating System
- **OS:** Ubuntu 24.04 LTS (Noble Numbat)
- **Kernel:** Linux 6.8+

### Web Server
- **Nginx:** Latest (от Forge)
- **PHP-FPM:** 8.3

### Database
- **PostgreSQL:** 17.x
- **Database Name:** dshome_dev

### Cache & Queue
- **Redis:** Latest (от Forge)
- **Redis Client:** phpredis

### Control Panel
- **Laravel Forge:** Latest

### SSL
- **Let's Encrypt:** Auto-renew активен

---

## 💻 Local Development Environment

### Laravel Herd (Windows)
- **Herd Version:** Latest
- **PHP:** 8.3.x (управлява се от Herd)
- **Composer:** 2.x (управлява се от Herd)
- **MySQL:** Included (не се използва)
- **Local Database:** SQLite

### Development Tools
- **VS Code:** Latest
- **Git:** Latest
- **Node.js:** 20.x LTS (от Herd)
- **npm:** 10.x (от Herd)

---

## 🚀 Laravel Stack

### Core Framework
```json
{
  "php": "^8.3",
  "laravel/framework": "^12.34.0",
  "laravel/tinker": "^2.10"
}
```

### Admin Panel
```json
{
  "filament/filament": "^4.1.10"
}
```

Filament автоматично инсталира:
- `filament/actions`
- `filament/forms`
- `filament/infolists`
- `filament/notifications`
- `filament/tables`
- `filament/widgets`
- `livewire/livewire`

### Database
```json
{
  "doctrine/dbal": "^3.0" (ако се нуждае)
}
```

### Dev Dependencies
```json
{
  "laravel/pail": "^1.2",
  "laravel/sail": "^1.x",
  "nunomaduro/collision": "^8.x",
  "phpunit/phpunit": "^11.x"
}
```

---

## 📦 NPM Packages

*(Ще се добавят при нужда за frontend)*

---

## 🔄 Upgrade History

### 21 Октомври 2025
- Initial setup
- Laravel 12.34.0
- Filament 4.1.10
- PHP 8.3
- PostgreSQL 17

---

## ⚠️ Compatibility Notes

### PHP Requirements
- **Minimum:** PHP 8.3
- **Recommended:** PHP 8.3
- **Extensions Required:**
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - PDO_PGSQL (за PostgreSQL)
  - PDO_SQLITE (за локална разработка)
  - Tokenizer
  - XML

### Database Versions
- **Production:** PostgreSQL 17+
- **Local:** SQLite 3.x

### Browser Support (Frontend - в бъдеще)
- Chrome/Edge: Latest 2 versions
- Firefox: Latest 2 versions
- Safari: Latest 2 versions
- Mobile browsers: iOS Safari 15+, Chrome Mobile latest

---

## 🔍 Checking Versions

### Server (SSH в production)
```bash
php -v
psql --version
redis-cli --version
nginx -v
```

### Local
```bash
php -v
composer --version
node --version
npm --version
```

### Laravel Project
```bash
php artisan --version
composer show | grep laravel
composer show | grep filament
```

---

## 📝 Version Update Policy

- **Security patches:** Apply immediately
- **Minor updates:** Monthly review
- **Major updates:** Planned upgrades with testing
- **PHP version:** Follow Laravel requirements
- **Database version:** Stable releases only

---

*При промяна на версии, актуализирай този файл!*
