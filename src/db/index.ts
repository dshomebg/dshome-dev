import { drizzle } from 'drizzle-orm/mysql2';
import mysql from 'mysql2/promise';
import * as schema from './schema';

// Създаване на connection pool към MariaDB
const poolConnection = mysql.createPool({
  host: process.env.DATABASE_HOST || '127.0.0.1',
  port: Number(process.env.DATABASE_PORT) || 3306,
  user: process.env.DATABASE_USER || 'dshome-dev',
  password: process.env.DATABASE_PASSWORD || '',
  database: process.env.DATABASE_NAME || 'dshome-dev',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
});

// Drizzle ORM instance
export const db = drizzle(poolConnection, { schema, mode: 'default' });

// Export schema за използване в други файлове
export { schema };

// Helper function за проверка на връзката
export async function testConnection() {
  try {
    const connection = await poolConnection.getConnection();
    console.log('✅ Database connection successful!');
    connection.release();
    return true;
  } catch (error) {
    console.error('❌ Database connection failed:', error);
    return false;
  }
}