# dshome.dev - Проект за Онлайн Магазин

## 📋 Основна Информация

**Проект:** Custom Laravel E-commerce Platform  
**Домейн:** dshome.dev (staging), dshome.bg (production)  
**Дата начало:** 21 Октомври 2025  
**Статус:** Development Setup Complete ✅

---

## 🎯 Цели на Проекта

### Текуща Ситуация
- **Платформа:** PrestaShop
- **Поръчки:** 400-500 месечно
- **Трафик:** 100,000 посещения месечно
- **Силна SEO позиция**

### Целева Визия (1-2 години)
- **Продукти:** 100,000+
- **Поръчки:** 1,500-2,000 месечно (300-400% ръст)
- **Трафик:** 300,000-400,000 посещения месечно
- **Запазване на SEO позициите**

### Причини за Custom Решение
- Скъпи PrestaShop модули
- Чести промени в PrestaShop
- Производителност при 100K продукти
- Точен контрол над функционалности
- AI-assisted development (vibe coding)

---

## 🏗️ Tech Stack

### Backend
- **Framework:** Laravel 11
- **PHP:** 8.3
- **Database:** PostgreSQL 17
- **Cache:** Redis
- **Queue:** Laravel Queue (Redis)

### Frontend (Admin)
- **Admin Panel:** Filament 4
- **Styling:** Tailwind CSS

### Infrastructure
- **Server:** Dedicated (i7-8700, 12 cores, 128GB RAM, NVME RAID 1)
- **OS:** Ubuntu 24.04 LTS
- **Web Server:** Nginx
- **Control Panel:** Laravel Forge
- **SSL:** Let's Encrypt

### Development
- **Local:** Laravel Herd (Windows)
- **Version Control:** Git + GitHub
- **Deployment:** Auto-deploy (push → production)
- **IDE:** VS Code

---

## 📦 Модули (от Техническото Задание)

### 1. Каталог
- Продукти (с комбинации)
- Категории (йерархични)
- Складове
- Атрибути и Характеристики
- Марки и Доставчици

### 2. Поръчки
- Управление на поръчки
- Статуси
- Клиенти
- История

### 3. Доставка
- Куриери (Speedy, Econt)
- Ценообразуване
- API интеграции
- Вземане от магазин

### 4. Плащане
- Наложен платеж
- Банков превод
- Онлайн плащания (карти)

---

## 🔄 Development Workflow
```
Локална разработка (VS Code)
         ↓
    Git commit
         ↓
  Git push → GitHub
         ↓
Forge Auto-Deploy → dshome.dev
         ↓
    Тестване
         ↓
(в бъдеще) → dshome.bg
```

---

## 📍 Текущ Статус

### ✅ Завършено
- Ubuntu 24.04 на dedicated сървър
- Laravel Forge setup (App Server)
- PHP 8.3 + PostgreSQL 17 + Redis инсталирани
- SSL сертификат активен
- Laravel проект създаден и deploy-нат
- Git workflow функционален
- Локална среда с Herd

### 🔜 Следващи Стъпки
1. Инсталиране на Filament 4 Admin Panel
2. Database архитектура
3. Първи модел (Продукти)
4. Първи CRUD в админ панела

---

## 👥 Екип

**Development:** Севдалин Панев + Claude (AI-assisted coding)  
**Подход:** Vibe coding - итеративна разработка с AI

---

## 📞 Контакти

**Email:** s.panev@gmail.com  
**Production:** https://dshome.bg  
**Staging:** https://dshome.dev

---

*Последна актуализация: 21 Октомври 2025*