import { mysqlTable, varchar, int, timestamp, text, boolean, decimal, json } from 'drizzle-orm/mysql-core';

/**
 * Users table - за NextAuth.js authentication
 */
export const users = mysqlTable('users', {
  id: int('id').primaryKey().autoincrement(),
  email: varchar('email', { length: 255 }).notNull().unique(),
  name: varchar('name', { length: 255 }),
  password: varchar('password', { length: 255 }), // hashed password for credentials auth
  emailVerified: timestamp('email_verified'),
  image: text('image'),
  role: varchar('role', { length: 50 }).default('user'), // user, admin
  createdAt: timestamp('created_at').defaultNow(),
  updatedAt: timestamp('updated_at').defaultNow().onUpdateNow(),
});

/**
 * Accounts table - за OAuth providers (Google, Facebook, etc.)
 * Опционално - ще се използва когато добавим OAuth
 */
export const accounts = mysqlTable('accounts', {
  id: int('id').primaryKey().autoincrement(),
  userId: int('user_id').notNull(),
  type: varchar('type', { length: 255 }).notNull(),
  provider: varchar('provider', { length: 255 }).notNull(),
  providerAccountId: varchar('provider_account_id', { length: 255 }).notNull(),
  refresh_token: text('refresh_token'),
  access_token: text('access_token'),
  expires_at: int('expires_at'),
  token_type: varchar('token_type', { length: 255 }),
  scope: varchar('scope', { length: 255 }),
  id_token: text('id_token'),
  session_state: varchar('session_state', { length: 255 }),
});

/**
 * Sessions table - за session-based authentication
 * Опционално - NextAuth може да използва JWT вместо DB sessions
 */
export const sessions = mysqlTable('sessions', {
  id: int('id').primaryKey().autoincrement(),
  sessionToken: varchar('session_token', { length: 255 }).notNull().unique(),
  userId: int('user_id').notNull(),
  expires: timestamp('expires').notNull(),
});

/**
 * Verification tokens table - за email verification
 * Опционално - за password reset и email verification
 */
export const verificationTokens = mysqlTable('verification_tokens', {
  identifier: varchar('identifier', { length: 255 }).notNull(),
  token: varchar('token', { length: 255 }).notNull().unique(),
  expires: timestamp('expires').notNull(),
});

/**
 * Categories table - за категории на продукти
 * Поддържа йерархична структура (parent-child)
 */
export const categories = mysqlTable('categories', {
  id: int('id').primaryKey().autoincrement(),
  name: varchar('name', { length: 255 }).notNull(),
  slug: varchar('slug', { length: 255 }).notNull().unique(),
  description: text('description'),
  parentId: int('parent_id'), // NULL за root категории
  image: varchar('image', { length: 500 }),
  isActive: boolean('is_active').default(true),
  position: int('position').default(0), // за sorting

  // SEO полета
  metaTitle: varchar('meta_title', { length: 255 }),
  metaDescription: text('meta_description'),

  createdAt: timestamp('created_at').defaultNow(),
  updatedAt: timestamp('updated_at').defaultNow().onUpdateNow(),
});

/**
 * Products table - основна таблица за продукти
 */
export const products = mysqlTable('products', {
  id: int('id').primaryKey().autoincrement(),

  // Основна информация
  sku: varchar('sku', { length: 100 }).notNull().unique(), // Референтен номер
  name: varchar('name', { length: 500 }).notNull(),
  slug: varchar('slug', { length: 500 }).notNull().unique(),
  description: text('description'),
  shortDescription: text('short_description'),

  // Цени (в лева)
  price: decimal('price', { precision: 10, scale: 2 }).notNull(), // Цена с ДДС
  priceWithoutVat: decimal('price_without_vat', { precision: 10, scale: 2 }), // Цена без ДДС
  cost: decimal('cost', { precision: 10, scale: 2 }), // Себестойност

  // Промоция/Намаление
  discount: decimal('discount', { precision: 10, scale: 2 }).default('0'), // Стойност на намалението
  discountType: varchar('discount_type', { length: 20 }).default('fixed'), // fixed или percent
  discountStart: timestamp('discount_start'),
  discountEnd: timestamp('discount_end'),

  // Наличност
  stock: int('stock').default(0), // Общо количество
  lowStockThreshold: int('low_stock_threshold').default(5), // Праг за ниска наличност

  // Изображения (JSON array of image URLs)
  images: json('images').$type<string[]>().default([]),

  // Размери и тегло
  width: decimal('width', { precision: 10, scale: 2 }), // см
  height: decimal('height', { precision: 10, scale: 2 }), // см
  depth: decimal('depth', { precision: 10, scale: 2 }), // см
  weight: decimal('weight', { precision: 10, scale: 2 }), // кг

  // Доставка
  deliveryTime: varchar('delivery_time', { length: 100 }), // "1-2 работни дни"

  // Статус и видимост
  isActive: boolean('is_active').default(true),
  isFeatured: boolean('is_featured').default(false), // Промо/featured продукт
  position: int('position').default(0),

  // SEO
  metaTitle: varchar('meta_title', { length: 255 }),
  metaDescription: text('meta_description'),

  createdAt: timestamp('created_at').defaultNow(),
  updatedAt: timestamp('updated_at').defaultNow().onUpdateNow(),
});

/**
 * Product Categories - Many-to-many връзка между продукти и категории
 */
export const productCategories = mysqlTable('product_categories', {
  id: int('id').primaryKey().autoincrement(),
  productId: int('product_id').notNull(),
  categoryId: int('category_id').notNull(),
  isPrimary: boolean('is_primary').default(false), // Основна категория
  createdAt: timestamp('created_at').defaultNow(),
});