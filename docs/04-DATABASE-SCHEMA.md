# Database Schema

**Последна актуализация:** 21 Октомври 2025

---

## 📊 Overview

Това е документацията на database schema за dshome.dev e-commerce проекта.

---

## 🗂️ Tables

### Categories (Категории)

Йерархична структура за организиране на продукти.

**Table Name:** `categories`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT (PK) | NO | AUTO | Primary key |
| `parent_id` | BIGINT (FK) | YES | NULL | Parent category за йерархия |
| `name` | VARCHAR(255) | NO | - | Име на категорията |
| `slug` | VARCHAR(255) | NO | - | SEO-friendly URL (unique) |
| `description` | TEXT | YES | NULL | Описание на категорията |
| `image` | VARCHAR(255) | YES | NULL | Път към изображение |
| `meta_title` | VARCHAR(255) | YES | NULL | SEO meta title |
| `meta_description` | TEXT | YES | NULL | SEO meta description |
| `active` | BOOLEAN | NO | TRUE | Статус (активна/неактивна) |
| `sort_order` | INTEGER | NO | 0 | Сортиране |
| `created_at` | TIMESTAMP | YES | NULL | Дата на създаване |
| `updated_at` | TIMESTAMP | YES | NULL | Дата на последна промяна |
| `deleted_at` | TIMESTAMP | YES | NULL | Soft delete |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE: `slug`
- INDEX: `parent_id`
- INDEX: `active`
- INDEX: `sort_order`

**Foreign Keys:**
- `parent_id` → `categories.id` (ON DELETE SET NULL)

**Relationships:**
- Self-referencing: `parent` (belongsTo Category)
- Self-referencing: `children` (hasMany Category)
- Has Many: `products` (Product)

---

### Products (Продукти)

Основна таблица за продукти.

**Table Name:** `products`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT (PK) | NO | AUTO | Primary key |
| `category_id` | BIGINT (FK) | YES | NULL | Категория |
| `name` | VARCHAR(255) | NO | - | Име на продукта |
| `slug` | VARCHAR(255) | NO | - | SEO-friendly URL (unique) |
| `sku` | VARCHAR(255) | NO | - | Референция/артикулен номер (unique) |
| `description` | TEXT | YES | NULL | Описание на продукта |
| `price` | DECIMAL(10,2) | NO | 0.00 | Цена |
| `quantity` | INTEGER | NO | 0 | Налично количество |
| `meta_title` | VARCHAR(255) | YES | NULL | SEO meta title |
| `meta_description` | TEXT | YES | NULL | SEO meta description |
| `active` | BOOLEAN | NO | TRUE | Статус (активен/неактивен) |
| `created_at` | TIMESTAMP | YES | NULL | Дата на създаване |
| `updated_at` | TIMESTAMP | YES | NULL | Дата на последна промяна |
| `deleted_at` | TIMESTAMP | YES | NULL | Soft delete |

**Indexes:**
- PRIMARY KEY: `id`
- UNIQUE: `slug`
- UNIQUE: `sku`
- INDEX: `category_id`
- INDEX: `active`
- INDEX: `price`
- COMPOSITE INDEX: `active, category_id`

**Foreign Keys:**
- `category_id` → `categories.id` (ON DELETE SET NULL)

**Relationships:**
- Belongs To: `category` (Category)

---

## 🔗 Relationships Diagram

```
Categories (Self-referencing)
    ↓ parent_id
Categories
    ↓ category_id (nullable)
Products
```

### Detailed Relationships

**Category Model:**
```php
// Parent category (for sub-categories)
public function parent(): BelongsTo
    → belongsTo(Category::class, 'parent_id')

// Child categories
public function children(): HasMany
    → hasMany(Category::class, 'parent_id')

// Products in this category
public function products(): HasMany
    → hasMany(Product::class)
```

**Product Model:**
```php
// Category
public function category(): BelongsTo
    → belongsTo(Category::class)
```

---

## 📝 Usage Examples

### Йерархия на категории

```php
// Главна категория
$mainCategory = Category::create([
    'name' => 'Електроника',
    'slug' => 'elektronika',
    'parent_id' => null, // root category
]);

// Подкатегория
$subCategory = Category::create([
    'name' => 'Лаптопи',
    'slug' => 'laptopi',
    'parent_id' => $mainCategory->id,
]);

// Втора подкатегория
$subCategory2 = Category::create([
    'name' => 'Телефони',
    'slug' => 'telefoni',
    'parent_id' => $mainCategory->id,
]);
```

### Продукти

```php
// Създаване на продукт
$product = Product::create([
    'category_id' => $subCategory->id,
    'name' => 'Lenovo ThinkPad X1 Carbon',
    'slug' => 'lenovo-thinkpad-x1-carbon',
    'sku' => 'LAPTOP-001',
    'description' => 'Професионален бизнес лаптоп...',
    'price' => 2499.99,
    'quantity' => 5,
    'active' => true,
]);
```

### Queries с Relationships

```php
// Вземи продукт с категорията му (eager loading)
$product = Product::with('category')->find(1);
echo $product->category->name; // Без N+1 query

// Вземи категория с продуктите ѝ
$category = Category::with('products')->find(1);
$products = $category->products;

// Вземи категория с детайлите ѝ
$category = Category::with(['parent', 'children'])->find(1);
echo $category->parent->name; // Parent category
foreach ($category->children as $child) {
    echo $child->name; // Child categories
}

// Вземи само активните продукти в категория
$activeProducts = $category->activeProducts;

// Scope queries
$activeCategories = Category::active()->get();
$rootCategories = Category::root()->get();
$inStockProducts = Product::active()->inStock()->get();
```

---

## 🎯 Migration Order

За правилно изпълнение на migrations, спазвай следния ред:

1. `2025_10_21_000001_create_categories_table.php` - първо (самостоятелна)
2. `2025_10_21_000002_create_products_table.php` - второ (зависи от categories)

---

## ⚠️ Important Notes

### Soft Deletes
И двете таблици използват **soft deletes** (`deleted_at`):
- Записите не се изтриват физически
- Използвай `$model->forceDelete()` за permanent delete
- Използвай `withTrashed()` за показване на изтрити записи

### Foreign Key Constraints
- `categories.parent_id` → `ON DELETE SET NULL` (ако parent се изтрие, child става root)
- `products.category_id` → `ON DELETE SET NULL` (ако category се изтрие, продуктът остава без категория)

### SEO Fields
- `slug` полетата са unique и индексирани за бързи търсения
- `meta_title` и `meta_description` за SEO optimization

### Performance
- Composite index на `products(active, category_id)` за бързи filtered queries
- Всички foreign keys са индексирани

---

## 🔜 Future Enhancements

Планирани таблици (не са създадени още):

### Фаза 2:
- `attributes` - атрибути (цвят, размер)
- `attribute_values` - стойности на атрибути
- `features` - характеристики
- `feature_groups` - групи характеристики
- `product_variants` - варианти на продукти
- `brands` - марки
- `suppliers` - доставчици

### Фаза 3:
- `warehouses` - складове
- `product_warehouse` (pivot) - stock per склад

### Фаза 4:
- `orders` - поръчки
- `order_items` - продукти в поръчка
- `customers` - клиенти

---

*Актуализирай този файл при всяка промяна в database schema!*
