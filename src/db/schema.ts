import { mysqlTable, varchar, int, timestamp } from 'drizzle-orm/mysql-core';

/**
 * Users table - за тестване на Drizzle ORM
 * По-късно ще се използва за NextAuth и Payload CMS
 */
export const users = mysqlTable('users', {
  id: int('id').primaryKey().autoincrement(),
  email: varchar('email', { length: 255 }).notNull().unique(),
  name: varchar('name', { length: 255 }),
  createdAt: timestamp('created_at').defaultNow(),
  updatedAt: timestamp('updated_at').defaultNow().onUpdateNow(),
});