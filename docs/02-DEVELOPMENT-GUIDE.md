# Development Guide

**Последна актуализация:** 21 Октомври 2025

---

## 🎯 Философия на Разработката

### Ключови Принципи
1. **Модулност** - малки, focused файлове
2. **Читаемост** - кодът се чете повече от колкото се пише
3. **Простота** - избягвай over-engineering
4. **Документация** - всяка сложна логика се документира
5. **AI-Assisted** - работим с AI, но разбираме кода

---

## 📏 Coding Standards

### PHP & Laravel
- **PSR-12** coding standard
- **Laravel naming conventions**
- **Type hints** навсякъде (PHP 8.3)
- **Strict types** в началото на файловете:
```php
  <?php

  declare(strict_types=1);
```

### File Size Limits
- **Models:** Max 200 реда
- **Controllers:** Max 150 реда  
- **Services:** Max 200 реда
- **Resources (Filament):** Max 300 реда

**Ако файлът надхвърли лимита → разделяй на по-малки класове!**

---

## 🗂️ Project Structure

### App Organization
```
app/
├── Filament/
│   └── Resources/           ← Filament admin resources
│       └── ProductResource/
│           ├── Pages/       ← Custom pages
│           └── RelationManagers/
├── Models/                  ← Eloquent models
│   ├── Product.php
│   ├── Category.php
│   └── ...
├── Services/                ← Business logic (ако е сложна)
│   └── OrderService.php
├── Actions/                 ← Single-purpose action classes
│   └── CreateOrderAction.php
└── Enums/                   ← PHP 8.3 enums
    └── OrderStatus.php
```

### Naming Conventions

**Models:**
- Singular, PascalCase
- `Product.php`, `OrderItem.php`

**Filament Resources:**
- Model name + "Resource"
- `ProductResource.php`, `CategoryResource.php`

**Services:**
- Descriptive name + "Service"
- `OrderService.php`, `PaymentService.php`

**Actions:**
- Verb + Noun + "Action"
- `CreateOrderAction.php`, `SendInvoiceAction.php`

**Migrations:**
- Laravel convention
- `2025_10_21_create_products_table.php`

**Variables:**
- camelCase
- Descriptive: `$productPrice` не `$pp`

**Database Tables:**
- Plural, snake_case
- `products`, `order_items`

**Database Columns:**
- snake_case
- `created_at`, `product_name`

---

## 🏗️ Модулен Подход

### Правило: Една Функция = Една Отговорност

**❌ Лошо - всичко на едно място:**
```php
public function store(Request $request)
{
    // Валидация
    $validated = $request->validate([...]);
    
    // Бизнес логика
    $product = Product::create($validated);
    
    // Още логика
    $this->updateStock($product);
    
    // Email
    Mail::to($user)->send(new ProductCreated($product));
    
    // Logging
    Log::info('Product created', ['id' => $product->id]);
    
    return redirect()->route('products.index');
}
```

**✅ Добре - разделено на модули:**
```php
public function store(StoreProductRequest $request)
{
    $product = app(CreateProductAction::class)->execute($request->validated());
    
    return redirect()->route('products.index');
}

// В отделен файл: app/Actions/CreateProductAction.php
class CreateProductAction
{
    public function execute(array $data): Product
    {
        $product = Product::create($data);
        
        event(new ProductCreated($product));
        
        return $product;
    }
}
```

### Правило: Max 3 нива на индентация

**❌ Лошо:**
```php
if ($condition1) {
    if ($condition2) {
        foreach ($items as $item) {
            if ($item->active) {
                // Твърде дълбоко!
            }
        }
    }
}
```

**✅ Добре:**
```php
if (!$condition1 || !$condition2) {
    return;
}

foreach ($items as $item) {
    $this->processItem($item);
}

private function processItem($item): void
{
    if (!$item->active) {
        return;
    }
    
    // Логика тук
}
```

---

## 🔄 Git Workflow

### Branch Strategy
- **main** - production ready код
- Работим директно на `main` (засега, докато сме сами)
- При нужда от experimental features → feature branches

### Commit Messages
**Формат:**
```
[Тип] Кратко описание

Детайлно описание (optional)
```

**Типове:**
- `[Feature]` - нова функционалност
- `[Fix]` - поправка на bug
- `[Refactor]` - подобрение на код без промяна на функционалност
- `[Docs]` - само документация
- `[Style]` - форматиране, semicolons, и т.н.
- `[Test]` - добавяне на тестове

**Примери:**
```
[Feature] Add product images upload to Filament

[Fix] Resolve N+1 query in products listing

[Refactor] Split ProductResource into smaller components

[Docs] Update database schema documentation
```

### Commit Frequency
- Commit-вай често (на всеки 30-60 мин работа)
- Всеки commit трябва да компилира без грешки
- Push към GitHub поне веднъж дневно

---

## 🤖 Работа с AI (Claude/ChatGPT)

### В Началото на Нов Chat
Казвай:
> "Прочети файловете в `/docs/` директорията - там е цялата информация за проекта."

### Как да питаш AI
**✅ Добри въпроси:**
- "Създай Filament Resource за Product model с тези полета..."
- "Как да оптимизирам този query за N+1 проблем?"
- "Направи migration за products таблица с тези колони..."

**❌ Лоши въпроси:**
- "Направи ми онлайн магазин" (твърде широко)
- "Поправи този код" (без контекст)

### Code Review с AI
- Винаги преглеждай кода който AI генерира
- Разбирай какво прави всеки ред
- Не copy-paste без да разбираш
- Питай "Защо този подход?" ако не е ясно

---

## 🧪 Testing Strategy (когато започнем)

### Какво тестваме
- Critical business logic
- Payment processing
- Order creation
- Stock management

### Какво НЕ тестваме (засега)
- Простите CRUD операции
- Filament UI (Filament е тестван от създателите)
- Eloquent relationships (Laravel ги тества)

---

## 🐛 Debugging

### Local Development
```bash
# Лог файлове
tail -f storage/logs/laravel.log

# Debug bar (ако инсталираме)
composer require barryvdh/laravel-debugbar --dev

# Tinker за testing
php artisan tinker
```

### Production (dshome.dev)
```bash
# SSH в сървъра
ssh forge@IP

cd /home/forge/dshome.dev/current

# Виж логове
tail -50 storage/logs/laravel.log

# Clear cache ако има проблем
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 📝 Documentation Standards

### Code Comments
**Кога да коментираш:**
- Сложна бизнес логика
- Workarounds за bugs
- TODO/FIXME notes

**Кога ДА НЕ коментираш:**
- Очевидни неща
- Всеки ред

**Добър коментар:**
```php
// Изчисляваме крайната цена с отстъпка, но само ако
// продуктът не е в promotional campaign (бизнес правило от 15.10.2025)
if (!$product->in_promotion) {
    $finalPrice = $this->calculateDiscount($product);
}
```

### PHPDoc
Използвай за:
- Public методи
- Model relationships
- Complex return types
```php
/**
 * Get all active products with their categories.
 *
 * @return \Illuminate\Database\Eloquent\Collection<Product>
 */
public function getActiveProducts(): Collection
{
    return Product::with('category')
        ->where('active', true)
        ->get();
}
```

---

## ⚡ Performance Guidelines

### Database Queries
- **Винаги eager load** relationships
```php
  // ❌ Лошо - N+1
  $products = Product::all();
  foreach ($products as $product) {
      echo $product->category->name; // Query за всеки product!
  }
  
  // ✅ Добре
  $products = Product::with('category')->get();
```

- **Use indexes** за често търсени колони
- **Paginate** големи резултати

### Caching Strategy
- Cache expensive queries (> 100ms)
- Cache API responses (куриери, payments)
- Don't cache user-specific data

---

## 🔒 Security Checklist

- [ ] Винаги използвай `$fillable` или `$guarded` в Models
- [ ] Validate всички user inputs
- [ ] Use FormRequests за валидация
- [ ] Never trust user input
- [ ] Use CSRF protection (built-in)
- [ ] Sanitize HTML content (използвай `strip_tags()` или HTML Purifier)

---

## 📞 Когато Имаш Проблем

1. **Провери документацията**
   - Laravel docs
   - Filament docs
   - `/docs/06-TROUBLESHOOTING.md`

2. **Debug стъпка по стъпка**
   - `dd()` е твой приятел
   - `Log::info()` за production debugging

3. **Попитай AI**
   - Дай context от `/docs/`
   - Покажи error log
   - Обясни какво си опитвал

4. **Документирай решението**
   - Запиши в `06-TROUBLESHOOTING.md`
   - Commit промените

---

*Този guide се актуализира с опита!*
