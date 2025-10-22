# Project Architecture

**Последна актуализация:** 21 Октомври 2025

---

## 🏛️ Обща Архитектура

### High-Level Overview
```
┌─────────────────────────────────────────────┐
│           Browser (Customer)                │
└────────────────┬────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────┐
│         Frontend (Laravel Blade)            │
│         (В бъдеще - Фаза 6)                 │
└────────────────┬────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────┐
│      Laravel Backend (API & Logic)          │
│  ┌──────────────────────────────────────┐   │
│  │   Filament Admin Panel               │   │
│  │   /admin                             │   │
│  └──────────────────────────────────────┘   │
└────────────────┬────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────┐
│         PostgreSQL Database                 │
│         + Redis Cache                       │
└─────────────────────────────────────────────┘
```

---

## 📦 Laravel Structure

### Core Directories
```
app/
├── Enums/              ← PHP 8.3 Enums (статуси, типове)
├── Filament/           ← Admin панел
│   ├── Pages/          ← Custom admin pages
│   ├── Resources/      ← CRUD за модели
│   └── Widgets/        ← Dashboard widgets
├── Http/
│   ├── Controllers/    ← (Minimal - повечето в Filament)
│   ├── Middleware/
│   └── Requests/       ← Form Request validation
├── Models/             ← Eloquent models
├── Actions/            ← Single-purpose action classes
├── Services/           ← Complex business logic
├── Observers/          ← Model lifecycle events
└── Policies/           ← Authorization
```

---

## 🎨 Filament Architecture

### Resource Structure (Пример: Product)
```
app/Filament/Resources/
└── ProductResource.php                 ← Main resource file
    ├── Pages/
    │   ├── CreateProduct.php          ← Create page
    │   ├── EditProduct.php            ← Edit page
    │   └── ListProducts.php           ← List/table page
    └── RelationManagers/
        └── ImagesRelationManager.php  ← Manage related images
```

### Resource Components

**ProductResource.php** съдържа:
```php
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    
    // 1. Форма (за create/edit)
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name'),
            // ...
        ]);
    }
    
    // 2. Таблица (за listing)
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name'),
            // ...
        ]);
    }
    
    // 3. Relations
    public static function getRelations(): array
    {
        return [
            ImagesRelationManager::class,
        ];
    }
    
    // 4. Pages
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
```

---

## 🗄️ Database Architecture Principles

### Table Naming
- **Plural, snake_case**
- `products`, `order_items`, `product_categories`

### Primary Keys
- **`id`** - BIGINT unsigned, auto-increment
- UUID за public-facing IDs (optional, за orders)

### Foreign Keys
- **Pattern:** `{model}_id`
- Examples: `category_id`, `user_id`, `warehouse_id`
- Always indexed
- Use constraints with `onDelete()` cascades

### Timestamps
- **All tables** have: `created_at`, `updated_at`
- Use `SoftDeletes` за важни данни: `deleted_at`

### JSON Columns
- За flexible data (характеристики, meta data)
- PostgreSQL има отлична JSON поддръжка

---

## 🔗 Relationships Pattern

### Common Patterns

**One-to-Many (1:N)**
```php
// Category has many Products
class Category extends Model
{
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

class Product extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
```

**Many-to-Many (N:M)**
```php
// Product belongs to many Warehouses (stock tracking)
// Pivot table: product_warehouse (quantity, reserved)
class Product extends Model
{
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class)
            ->withPivot('quantity', 'reserved')
            ->withTimestamps();
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}

class Warehouse extends Model
{
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'reserved')
            ->withTimestamps();
    }
}

class Brand extends Model
{
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

class Supplier extends Model
{
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
```

**Polymorphic (за images, comments, и т.н.)**
```php
class Image extends Model
{
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}

class Product extends Model
{
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
```

---

## 🎯 Service Layer Pattern

### Кога да използваме Services

**НЕ за прости CRUD:**
```php
// ❌ Не трябва Service за това
class ProductService
{
    public function create(array $data): Product
    {
        return Product::create($data);
    }
}
```

**ДА за сложна логика:**
```php
// ✅ Сложна бизнес логика заслужава Service
class OrderService
{
    public function __construct(
        private PaymentService $paymentService,
        private InventoryService $inventoryService,
        private NotificationService $notificationService,
    ) {}
    
    public function createOrder(array $data): Order
    {
        DB::transaction(function () use ($data) {
            $order = Order::create($data);
            
            // Намаляваме stock
            $this->inventoryService->reserveStock($order);
            
            // Process payment
            $this->paymentService->charge($order);
            
            // Send emails
            $this->notificationService->sendOrderConfirmation($order);
            
            return $order;
        });
    }
}
```

---

## ⚡ Action Classes Pattern

### Single-Purpose Actions

Използваме за **една конкретна задача**:
```php
// app/Actions/CreateProductAction.php
class CreateProductAction
{
    public function execute(array $data): Product
    {
        $product = Product::create([
            'name' => $data['name'],
            'sku' => $data['sku'] ?? $this->generateSku(),
            'price' => $data['price'],
        ]);
        
        if (isset($data['images'])) {
            $this->attachImages($product, $data['images']);
        }
        
        event(new ProductCreated($product));
        
        return $product;
    }
    
    private function generateSku(): string
    {
        return 'PRD-' . strtoupper(Str::random(8));
    }
    
    private function attachImages(Product $product, array $images): void
    {
        foreach ($images as $image) {
            $product->images()->create(['path' => $image]);
        }
    }
}
```

**Използване във Filament:**
```php
// ProductResource.php
protected function mutateFormDataBeforeCreate(array $data): array
{
    return app(CreateProductAction::class)->execute($data);
}
```

---

## 🔢 Enums (PHP 8.3)

### За статуси, типове, константи
```php
// app/Enums/OrderStatus.php
enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    
    public function label(): string
    {
        return match($this) {
            self::Pending => 'Чака обработка',
            self::Processing => 'В обработка',
            self::Shipped => 'Изпратена',
            self::Delivered => 'Доставена',
            self::Cancelled => 'Отказана',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Processing => 'info',
            self::Shipped => 'primary',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }
}
```

**Използване в Model:**
```php
class Order extends Model
{
    protected $casts = [
        'status' => OrderStatus::class,
    ];
}
```

**Използване във Filament:**
```php
SelectInput::make('status')
    ->options(OrderStatus::class)
    ->default(OrderStatus::Pending)
```

---

## 📊 Query Optimization Patterns

### N+1 Problem Prevention

**❌ Проблем:**
```php
$products = Product::all(); // 1 query

foreach ($products as $product) {
    echo $product->category->name; // N queries (за всеки product!)
}
// Total: 1 + N queries
```

**✅ Решение:**
```php
$products = Product::with('category')->get(); // 2 queries total!

foreach ($products as $product) {
    echo $product->category->name;
}
```

**✅ Filament Resource:**
```php
public static function table(Table $table): Table
{
    return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->with('category'))
        ->columns([...]);
}
```

---

## 🗂️ File Structure Example (Full Module)

### Product Module Structure
```
app/
├── Models/
│   └── Product.php                      ← Model
├── Enums/
│   └── ProductStatus.php                ← Status enum
├── Actions/
│   ├── CreateProductAction.php          ← Create logic
│   └── UpdateProductStockAction.php     ← Stock update
├── Observers/
│   └── ProductObserver.php              ← Lifecycle events
└── Filament/Resources/
    └── ProductResource.php              ← Admin CRUD
        └── Pages/
            ├── ListProducts.php
            ├── CreateProduct.php
            └── EditProduct.php

database/migrations/
└── 2025_10_21_create_products_table.php

database/factories/
└── ProductFactory.php                    ← Testing data

database/seeders/
└── ProductSeeder.php                     ← Sample data
```

---

## 🔐 Authorization Pattern

### Policies (Laravel)
```php
// app/Policies/ProductPolicy.php
class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_products');
    }
    
    public function create(User $user): bool
    {
        return $user->can('create_products');
    }
    
    public function update(User $user, Product $product): bool
    {
        return $user->can('edit_products');
    }
    
    public function delete(User $user, Product $product): bool
    {
        return $user->can('delete_products');
    }
}
```

**Filament автоматично ги зачита!**

---

## 🎨 Frontend Architecture (Бъдеще - Фаза 6)

### Възможности

**Option 1: Laravel Blade + Livewire (Препоръчвам)**
- Server-side rendering
- SEO friendly
- По-бързо development
- Livewire за interactivity

**Option 2: React/Next.js + Laravel API**
- Client-side rendering (или SSR с Next.js)
- По-сложно, но по-модерно
- Laravel като headless CMS

**Решение:** Ще вземем когато дойде времето (Фаза 6)

---

## 🔄 Event-Driven Architecture (За бъдеще)

### Events & Listeners

Когато логиката стане по-сложна:
```php
// Event
class OrderCreated
{
    public function __construct(public Order $order) {}
}

// Listeners
class SendOrderConfirmationEmail
{
    public function handle(OrderCreated $event): void
    {
        Mail::to($event->order->customer)
            ->send(new OrderConfirmation($event->order));
    }
}

class UpdateInventory
{
    public function handle(OrderCreated $event): void
    {
        foreach ($event->order->items as $item) {
            $item->product->decrement('stock', $item->quantity);
        }
    }
}
```

**Filament integration:**
```php
protected function afterCreate(): void
{
    event(new OrderCreated($this->record));
}
```

---

## 📝 Architecture Decision Log

### Решения до момента

| Дата | Решение | Причина |
|------|---------|---------|
| 22.10.2025 | Warehouses many-to-many с Products | Позволява stock tracking по складове с pivot данни (quantity, reserved) |
| 22.10.2025 | Brands и Suppliers като отделни таблици | Brands с пълен SEO, Suppliers само за статистика |
| 22.10.2025 | Filament навигация групирана под "Каталог" | По-добра организация на админ панела |
| 22.10.2025 | Schema-based API във Filament Resources | Filament 4.1 изисква Schema вместо Form |
| 21.10.2025 | PostgreSQL вместо MySQL | По-добър JSON support, по-бърз при сложни queries, scalability |
| 21.10.2025 | Filament 4 за admin | Готов admin панел, спестява месеци разработка |
| 21.10.2025 | SQLite за локална разработка | По-просто, не изисква отделен DB сървър |
| 21.10.2025 | Modular approach | Малки файлове, лесна поддръжка |

---

*Архитектурата се евoluира с проекта!*
