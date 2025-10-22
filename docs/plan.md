# План за Разработка - dshome.dev E-commerce

**Дата започване:** 21 Октомври 2025  
**Последна актуализация:** 22 Октомври 2025

---

## 📊 Общ Прогрес

**Фаза:** Setup & Infrastructure ✅ (ЗАВЪРШЕНА)
**Текуща фаза:** MVP Development 🔄
**Процент изпълнение:** ~50% (Products + Categories ГОТОВИ, липсват Warehouses + Orders)

---

## ✅ Фаза 0: Setup & Infrastructure (ЗАВЪРШЕНА)

### Infrastructure
- [x] Ubuntu 24.04 на dedicated сървър
- [x] Laravel Forge setup (App Server)
- [x] PHP 8.3 + PostgreSQL 17 + Redis
- [x] SSL сертификат (Let's Encrypt)
- [x] DNS конфигурация (dshome.dev)

### Development Environment
- [x] Laravel Herd инсталиран (офис компютър)
- [x] Laravel Herd инсталиран (домашен компютър)
- [x] Git + GitHub repository
- [x] SSH keys за GitHub (двата компютъра)
- [x] VS Code setup
- [x] Auto-deployment workflow

### Laravel Project
- [x] Laravel 11 проект създаден
- [x] Filament 4.1 инсталиран
- [x] Admin панел конфигуриран (/admin)
- [x] Database migrations работят
- [x] Storage permissions настроени

### Documentation
- [x] PROJECT-OVERVIEW.md създаден
- [x] plan.md създаден

---

## 🔄 Фаза 1: MVP Development (В ПРОЦЕС)

### 1.1 Database Architecture
- [x] Проектиране на database schema
- [x] Документиране на relationships
- [x] Създаване на migrations
  - [x] Users & roles
  - [x] Products базова структура
  - [x] Categories (йерархични)
  - [ ] Warehouses (складове)
  - [ ] Orders базова структура

### 1.2 Модул: Продукти (Базови функции) ✅
- [x] Product Model
- [x] ProductResource (Filament)
- [x] Основни полета:
  - [x] Заглавие
  - [x] Референция (SKU)
  - [x] Цена
  - [x] Количество
  - [x] Статус
  - [x] Meta title/description
  - [x] Категория (Select with relationship)
- [x] CRUD операции (Create, Edit, Delete, Restore)
- [x] Soft Deletes
- [x] Bulk actions
- [x] Качване на изображения ✅ (22.10.2025)
  - [x] ProductImage Model
  - [x] product_images migration
  - [x] ImagesRelationManager
  - [x] FileUpload с image editor
  - [x] Auto ALT text generation
  - [x] Primary image selection
  - [x] Drag & drop reordering
  - [x] ProductObserver за auto-logic
  - [x] ProductImageObserver за image management
  - [x] Placeholder SVG
- [ ] WYSIWYG редактор за описание (TODO: RichEditor за short/long description)

### 1.3 Модул: Категории ✅
- [x] Category Model
- [x] CategoryResource (Filament)
- [x] Йерархична структура (parent/child)
- [x] Полета:
  - [x] Име
  - [x] Slug (SEO URL)
  - [x] Описание
  - [x] Изображение (FileUpload)
  - [x] Meta title/description
  - [x] Sort order
  - [x] Parent category (Select)
- [x] CRUD операции
- [x] Soft Deletes
- [ ] Tree view в админ панела (TODO: Consider Filament Tree plugin)

### 1.4 Модул: Складове (Базово)
- [ ] Warehouse Model
- [ ] WarehouseResource (Filament)
- [ ] Полета:
  - [ ] Име на склад
  - [ ] Адрес
  - [ ] Телефон
  - [ ] Работно време
  - [ ] Физически магазин (checkbox)

### 1.5 Връзки между Модули
- [x] Product belongsTo Category
- [x] Category hasMany Products (with parent/child hierarchy)
- [ ] Product belongsToMany Warehouses (stock levels)
- [ ] Filament Relations визуализация

---

## 🎯 Фаза 2: Разширени Функции за Продукти

### 2.1 Атрибути
- [ ] Attribute Model
- [ ] AttributeValue Model
- [ ] AttributeResource (Filament)
- [ ] Типове показване:
  - [ ] Radio button
  - [ ] Dropdown
  - [ ] Color picker

### 2.2 Характеристики
- [ ] Feature Model
- [ ] FeatureGroup Model
- [ ] FeatureResource (Filament)
- [ ] Асоцииране с продукти

### 2.3 Продуктови Комбинации
- [ ] ProductVariant Model
- [ ] Генератор на комбинации
- [ ] Цена/Реф. номер per вариант
- [ ] Тегло per вариант
- [ ] Stock management per вариант

### 2.4 Марки и Доставчици
- [ ] Brand Model
- [ ] BrandResource (Filament)
- [ ] Supplier Model
- [ ] SupplierResource (Filament)

### 2.5 SEO Оптимизация
- [ ] Meta fields за продукти
- [ ] Canonical URLs
- [ ] Auto-generate SEO URLs
- [ ] Sitemap generation

---

## 🛒 Фаза 3: Модул Поръчки

### 3.1 Базова Структура
- [ ] Order Model
- [ ] OrderItem Model
- [ ] OrderResource (Filament)
- [ ] Полета:
  - [ ] Номер на поръчка
  - [ ] Клиент
  - [ ] Сума
  - [ ] Статус
  - [ ] Дата

### 3.2 Клиенти
- [ ] Customer Model
- [ ] CustomerResource (Filament)
- [ ] Адреси (множество)
- [ ] История на поръчки
- [ ] Email промоции (opt-in)

### 3.3 Статуси на Поръчки
- [ ] OrderStatus Model
- [ ] Персонализируеми статуси
- [ ] Цветове (text/background)
- [ ] Email notifications per статус
- [ ] Email templates

### 3.4 Управление на Поръчки
- [ ] Преглед на поръчка (детайлен)
- [ ] Смяна на статус
- [ ] Комуникация с клиента
- [ ] История на промените
- [ ] Бележки (само за админи)

---

## 🚚 Фаза 4: Модул Доставка

### 4.1 Куриери - Основно
- [ ] Courier Model
- [ ] CourierResource (Filament)
- [ ] Полета:
  - [ ] Име
  - [ ] Лого
  - [ ] URL за проследяване
  - [ ] Статус

### 4.2 Ценообразуване
- [ ] По тегло (таблица с диапазони)
- [ ] Палетна доставка
- [ ] Фиксирана цена
- [ ] Вземане от магазин

### 4.3 API Интеграции
- [ ] Speedy API
- [ ] Econt API
- [ ] Офиси на куриерите (caching)
- [ ] Генериране на товарителници

### 4.4 Правила за Безплатна Доставка
- [ ] Минимална сума
- [ ] Максимално тегло
- [ ] Комбинирани условия

---

## 💳 Фаза 5: Модул Плащане

### 5.1 Методи на Плащане
- [ ] Наложен платеж
  - [ ] Такса (сума/процент)
- [ ] Банков превод
  - [ ] Банкови детайли в инструкции
- [ ] Онлайн плащания
  - [ ] Stripe integration
  - [ ] ePay integration
  - [ ] DSK integration

### 5.2 Правила за Плащане
- [ ] Скриване на методи при условия
- [ ] Минимална/максимална сума
- [ ] Ограничения по тегло

---

## 🎨 Фаза 6: Frontend (Customer-Facing)

### 6.1 Архитектура
- [ ] Избор на подход (Blade/Livewire vs React/Next.js)
- [ ] Routing strategy
- [ ] Layout & компоненти

### 6.2 Основни Страници
- [ ] Homepage
- [ ] Каталог (category pages)
- [ ] Продуктова страница
- [ ] Кошница
- [ ] Checkout
- [ ] User account

### 6.3 Търсене & Филтриране
- [ ] Search functionality
- [ ] Filters по характеристики
- [ ] Filters по цена
- [ ] Sorting options

### 6.4 SEO
- [ ] Meta tags
- [ ] Structured data (schema.org)
- [ ] Sitemap
- [ ] Robots.txt
- [ ] 301 redirects (миграция от PrestaShop)

---

## 🔄 Фаза 7: Миграция от PrestaShop

### 7.1 Подготовка
- [ ] Export на данни от PrestaShop
- [ ] URL mapping (стари → нови URLs)
- [ ] 301 redirects setup

### 7.2 Import Scripts
- [ ] Продукти import
- [ ] Категории import
- [ ] Клиенти import
- [ ] Поръчки (история) import
- [ ] Images migration

### 7.3 SEO Запазване
- [ ] URL struktura matching
- [ ] Meta data migration
- [ ] Canonical URLs
- [ ] Testing на всички important URLs

---

## 🚀 Фаза 8: Performance & Optimization

### 8.1 Caching
- [ ] Redis cache strategy
- [ ] Query optimization
- [ ] Eager loading
- [ ] Cache warming

### 8.2 Database Optimization
- [ ] Indexes
- [ ] Query profiling
- [ ] N+1 query fixes

### 8.3 Assets
- [ ] Image optimization
- [ ] Lazy loading
- [ ] CDN setup (optional)

---

## 🧪 Фаза 9: Testing & QA

### 9.1 Testing
- [ ] Unit tests (критични функции)
- [ ] Feature tests
- [ ] Browser testing

### 9.2 Load Testing
- [ ] 100K продукти performance
- [ ] Concurrent users testing
- [ ] Database stress testing

---

## 📈 Фаза 10: Production Launch

### 10.1 Pre-Launch
- [ ] Final QA на staging (dshome.dev)
- [ ] Backup strategy
- [ ] Rollback plan
- [ ] DNS preparation

### 10.2 Launch Day
- [ ] DNS switch
- [ ] 301 redirects активни
- [ ] Monitoring setup
- [ ] Error tracking

### 10.3 Post-Launch
- [ ] SEO monitoring
- [ ] Performance monitoring
- [ ] User feedback
- [ ] Bug fixes

---

## 📝 Бележки

### Технически Решения
- Database: PostgreSQL за scalability
- Cache: Redis за performance
- Queue: Laravel Queue за background jobs
- Search: Meilisearch (или Algolia) за advanced search

### Timeline (Приблизителен)
- Фаза 1 (MVP): 2-3 месеца
- Фаза 2-5 (Пълна функционалност): 4-6 месеца
- Фаза 6 (Frontend): 2-3 месеца
- Фаза 7-10 (Миграция & Launch): 2-3 месеца
- **Общо: ~12-15 месеца**

---

*Този план се актуализира постоянно с прогреса на проекта.*
