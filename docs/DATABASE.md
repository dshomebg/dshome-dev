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
Таблица за съхранение на потребители - NextAuth.js authentication

```sql
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `name` VARCHAR(255),
  `password` VARCHAR(255) COMMENT 'Hashed password for credentials auth',
  `email_verified` TIMESTAMP NULL,
  `image` TEXT,
  `role` VARCHAR(50) DEFAULT 'user' COMMENT 'user, admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Колони:**
- `id` - Уникален идентификатор
- `email` - Email адрес (unique)
- `name` - Име на потребителя
- `password` - Hashed парола (bcrypt)
- `email_verified` - Дата на верификация на email
- `image` - URL към профилна снимка
- `role` - Роля (user/admin)
- `created_at` - Дата на създаване
- `updated_at` - Дата на последна промяна

---

### Таблица: accounts
Таблица за OAuth providers (Google, Facebook, etc.) - NextAuth.js

```sql
CREATE TABLE `accounts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `provider` VARCHAR(255) NOT NULL,
  `provider_account_id` VARCHAR(255) NOT NULL,
  `refresh_token` TEXT,
  `access_token` TEXT,
  `expires_at` INT,
  `token_type` VARCHAR(255),
  `scope` VARCHAR(255),
  `id_token` TEXT,
  `session_state` VARCHAR(255)
);
```

**Колони:**
- `id` - Уникален идентификатор
- `user_id` - Връзка към users таблица
- `type` - Тип на account (oauth, email)
- `provider` - OAuth provider (google, facebook)
- `provider_account_id` - ID от provider
- `refresh_token` - Refresh token от OAuth
- `access_token` - Access token от OAuth
- `expires_at` - Unix timestamp на изтичане
- `token_type` - Тип на token (Bearer)
- `scope` - OAuth scopes
- `id_token` - OpenID Connect ID token
- `session_state` - OAuth session state

---

### Таблица: sessions
Таблица за session-based authentication - NextAuth.js (опционално - използваме JWT)

```sql
CREATE TABLE `sessions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_token` VARCHAR(255) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `expires` TIMESTAMP NOT NULL
);
```

**Колони:**
- `id` - Уникален идентификатор
- `session_token` - Уникален session token
- `user_id` - Връзка към users таблица
- `expires` - Дата на изтичане на session

---

### Таблица: verification_tokens
Таблица за email verification и password reset tokens - NextAuth.js

```sql
CREATE TABLE `verification_tokens` (
  `identifier` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL UNIQUE,
  `expires` TIMESTAMP NOT NULL
);
```

**Колони:**
- `identifier` - Email адрес или user identifier
- `token` - Уникален verification token
- `expires` - Дата на изтичане на token

---

## Индекси и Foreign Keys

```sql
-- Indexes за по-бърза производителност
CREATE INDEX idx_accounts_user_id ON accounts(user_id);
CREATE INDEX idx_sessions_user_id ON sessions(user_id);
CREATE INDEX idx_sessions_expires ON sessions(expires);
CREATE INDEX idx_verification_tokens_identifier ON verification_tokens(identifier);
```

---

## Забележки

- **Authentication**: Използваме NextAuth.js с JWT strategy (sessions таблица е опционална)
- **Password hashing**: bcryptjs с salt rounds = 10
- **OAuth support**: accounts таблица готова за Google/Facebook OAuth (ще се имплементира по-късно)
- **Email verification**: verification_tokens таблица готова за email verification и password reset

---

**Последна актуализация:** 25 Октомври 2025 - NextAuth.js authentication tables added