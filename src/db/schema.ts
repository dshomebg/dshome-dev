import { mysqlTable, varchar, int, timestamp, text } from 'drizzle-orm/mysql-core';

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