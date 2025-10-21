# Troubleshooting & Error Log

**Последна актуализация:** 21 Октомври 2025

---

## 📋 Формат

Всяка грешка е документирана по следния начин:
```
### [Дата] Заглавие на проблема

**Грешка:**
[Точното съобщение за грешка или симптоми]

**Причина:**
[Защо се случва]

**Решение:**
[Стъпки за решаване]

**Превенция:**
[Как да избегнем в бъдеще]
```

---

## 🔥 Production Errors (dshome.dev)

### [21.10.2025] SSH Connection Failed - Forge Cannot Connect

**Грешка:**
```
Something went wrong when we tried to execute an action on your server.
Please make sure that the following SSH key is placed in both 
/home/forge/.ssh/authorized_keys and /root/.ssh/authorized_keys
```

**Причина:**
Forge SSH ключът не е добавен в `authorized_keys` файловете на сървъра.

**Решение:**
```bash
# SSH като root
ssh root@SERVER_IP

# За root
mkdir -p /root/.ssh
nano /root/.ssh/authorized_keys
# Paste Forge SSH key
chmod 700 /root/.ssh
chmod 600 /root/.ssh/authorized_keys

# За forge user
mkdir -p /home/forge/.ssh
nano /home/forge/.ssh/authorized_keys
# Paste СЪЩИЯ Forge SSH key
chmod 700 /home/forge/.ssh
chmod 600 /home/forge/.ssh/authorized_keys
chown -R forge:forge /home/forge/.ssh

# Restart SSH
systemctl restart ssh
```

**Превенция:**
При provision-ване на нов сървър във Forge, изчакай докато process завърши напълно преди да правиш промени.

---

### [21.10.2025] Deployment Failed - npm ci Error

**Грешка:**
```
npm error The `npm ci` command can only install with an existing 
package-lock.json or npm-shrinkwrap.json
```

**Причина:**
Laravel 11 default проект няма `package-lock.json`, но deployment скриптът опитва `npm ci`.

**Решение:**
Премахни `npm ci && npm run build` от deployment script във Forge:
```bash
# Forge → Sites → dshome.dev → Settings → Deployments
# Изтрий реда:
npm ci && npm run build
```

**Превенция:**
Добавяй npm build само когато има реално frontend assets за compile-ване.

---

### [21.10.2025] Deployment Failed - View Path Not Found

**Грешка:**
```
In ViewClearCommand.php line 58:
View path not found.
```

**Причина:**
`php artisan view:clear` се изпълнява преди `storage/framework/views` да е създадена.

**Решение:**
Премахни `$FORGE_PHP artisan optimize` от deployment script.

**Превенция:**
Използвай само необходимите artisan команди в deployment script.

---

### [21.10.2025] 500 Internal Server Error - Empty .env

**Грешка:**
```
Oops! An Error Occurred
The server returned a "500 Internal Server Error".
```

**Причина:**
`.env` файлът е празен - липсва `APP_KEY` и database конфигурация.

**Решение:**
```bash
# Forge → Sites → dshome.dev → Environment
# Попълни пълен .env файл

# След това в Commands:
php artisan key:generate
php artisan config:cache
```

**Превенция:**
Винаги настройвай `.env` веднага след създаване на site във Forge.

---

### [21.10.2025] 500 Error - Cache Path Invalid

**Грешка (в logs):**
```
Please provide a valid cache path.
InvalidArgumentException at Compiler.php:75
```

**Причина:**
Липсват `storage/framework` поддиректории.

**Решение:**
```bash
# SSH в сървъра
ssh forge@SERVER_IP
cd /home/forge/dshome.dev/current

# Създай директориите
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Clear cache
php artisan config:clear
php artisan cache:clear
```

**Превенция:**
Добави в deployment script:
```bash
mkdir -p storage/framework/{sessions,views,cache}
chmod -R 775 storage bootstrap/cache
```

---

### [21.10.2025] SSL Error - ERR_SSL_UNRECOGNIZED_NAME_ALERT

**Грешка:**
Browser показва `ERR_SSL_UNRECOGNIZED_NAME_ALERT` при отваряне на https://dshome.dev

**Причина:**
SSL сертификатът не е инсталиран или DNS не е propagated правилно.

**Решение:**
```bash
# Forge → Sites → dshome.dev → SSL
# LetsEncrypt → Activate

# Провери DNS:
ping dshome.dev
nslookup dshome.dev

# Изчакай 5-10 минути за SSL activation
```

**Превенция:**
- Setup-вай DNS преди да създаваш site във Forge
- Изчакай DNS propagation (10-60 min)
- След това създавай site и инсталирай SSL

---

## 💻 Local Development Errors

### [21.10.2025] PowerShell - Command Not Found

**Грешка:**
```powershell
touch : The term 'touch' is not recognized...
composer : The term 'composer' is not recognized...
```

**Причина:**
- `touch` не съществува в PowerShell
- `composer` не е в PATH (Herd не е инсталиран или PowerShell не е рестартиран)

**Решение:**
```powershell
# Вместо touch:
New-Item -Path file.txt -ItemType File
# Или кратко:
ni file.txt

# За composer - рестартирай PowerShell след Herd инсталация
# Ако пак не работи:
herd --version  # Провери дали Herd е инсталиран
```

**Превенция:**
- Винаги рестартирай терминала след инсталация на нов софтуер
- Използвай PowerShell-specific команди

---

### [21.10.2025] Git - Permission Denied (publickey)

**Грешка:**
```
git@github.com: Permission denied (publickey).
fatal: Could not read from remote repository.
```

**Причина:**
SSH key не е добавен в GitHub или не е генериран.

**Решение:**
```powershell
# Генерирай SSH key
ssh-keygen -t ed25519 -C "your_email@example.com"
# Enter 3 пъти

# Покажи public key
cat ~\.ssh\id_ed25519.pub
# Или:
Get-Content $env:USERPROFILE\.ssh\id_ed25519.pub

# Копирай целия текст

# Добави в GitHub:
# https://github.com/settings/ssh/new
```

**Превенция:**
Setup-вай SSH keys веднага на всяка нова машина.

---

### [21.10.2025] Herd - Site Not Loading (404)

**Грешка:**
`http://dshome-dev.test` връща 404 или "Site Not Found"

**Причина:**
Проектът не е "park-нат" в Herd.

**Решение:**
```powershell
cd D:\APP-DSHOME
herd park

# Или през Herd UI:
# Herd → Parked Paths → Add Path → избери D:\APP-DSHOME
```

**Превенция:**
Винаги park-вай родителската директория с проекти.

---

### [21.10.2025] SQLite - Database File Missing

**Грешка:**
```
SQLSTATE[HY000]: General error: 14 unable to open database file
```

**Причина:**
`database/database.sqlite` файлът не съществува.

**Решение:**
```powershell
# Създай файла
ni database/database.sqlite

# Или:
New-Item -Path database/database.sqlite -ItemType File

# После мигрирай:
php artisan migrate
```

**Превенция:**
Винаги създавай SQLite файла преди първото migrate.

---

## 🔧 Common Issues & Quick Fixes

### Cache Issues
```bash
# Clear всички cache-ове
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Или всички наведнъж:
php artisan optimize:clear
```

### Permission Issues (Production)
```bash
# SSH в сървъра
chmod -R 775 storage bootstrap/cache
chown -R forge:forge storage bootstrap/cache
```

### Composer Issues
```bash
# Clear composer cache
composer clear-cache

# Update autoload
composer dump-autoload
```

### Git Issues
```bash
# Reset unstaged changes
git checkout .

# Undo last commit (keep changes)
git reset --soft HEAD~1

# Pull latest (overwrite local)
git fetch origin
git reset --hard origin/main
```

---

## 📞 Debugging Commands

### Local
```bash
# Tail logs в реално време
tail -f storage/logs/laravel.log

# Debug route
php artisan route:list

# Debug config
php artisan config:show

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo()
```

### Production (SSH)
```bash
ssh forge@SERVER_IP
cd /home/forge/dshome.dev/current

# Last 50 log lines
tail -50 storage/logs/laravel.log

# Search errors
grep -i "error\|exception" storage/logs/laravel.log | tail -20

# Check permissions
ls -la storage/
ls -la bootstrap/cache/
```

---

## 🎯 Prevention Checklist

### Before Creating New Feature
- [ ] Pull latest code: `git pull origin main`
- [ ] Check migrations: `php artisan migrate:status`
- [ ] Clear cache: `php artisan optimize:clear`

### Before Committing
- [ ] Test locally
- [ ] Check for errors in logs
- [ ] Run `composer dump-autoload`
- [ ] Write meaningful commit message

### Before Deploying to Production
- [ ] Test на localhost
- [ ] Review deployment script
- [ ] Check `.env` на production
- [ ] Backup database (optional)

---

*Добавяй нови грешки и решения тук при всеки проблем!*
