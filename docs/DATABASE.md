Основна Информация:

Mariadb 11.4.8
Host : 127.0.0.1
Port: 3306
DB NAME: dshome-dev
DB USER : dshome-dev
DB USER PASS: 1borabora2
ALL PREVILEGIES


## Схема на базата данни

### Таблица: users
Таблица за съхранение на потребители (ще се използва за NextAuth.js и Payload CMS)

```sql
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `name` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```