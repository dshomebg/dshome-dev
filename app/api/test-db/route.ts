import { NextResponse } from 'next/server';
import { testConnection } from '@/src/db';

/**
 * API endpoint за тестване на database connection
 * GET /api/test-db
 */
export async function GET() {
  try {
    const isConnected = await testConnection();

    if (isConnected) {
      return NextResponse.json({
        success: true,
        message: 'Database connection successful!',
        database: process.env.DATABASE_NAME,
        host: process.env.DATABASE_HOST,
      });
    } else {
      return NextResponse.json({
        success: false,
        message: 'Database connection failed!',
      }, { status: 500 });
    }
  } catch (error) {
    return NextResponse.json({
      success: false,
      message: 'Error testing database connection',
      error: error instanceof Error ? error.message : 'Unknown error',
    }, { status: 500 });
  }
}
