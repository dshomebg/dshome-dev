import { NextResponse } from "next/server";
import { db } from "@/src/db";
import { users } from "@/src/db/schema";
import { eq } from "drizzle-orm";
import bcrypt from "bcryptjs";

/**
 * API endpoint за създаване на първи admin потребител
 * ВАЖНО: Използвай само веднъж за първоначално setup!
 * След това трябва да се деактивира или премахне.
 */
export async function POST(request: Request) {
  try {
    const { email, password, name } = await request.json();

    // Validation
    if (!email || !password) {
      return NextResponse.json(
        { success: false, message: "Email и парола са задължителни" },
        { status: 400 }
      );
    }

    // Check if user already exists
    const [existingUser] = await db
      .select()
      .from(users)
      .where(eq(users.email, email))
      .limit(1);

    if (existingUser) {
      return NextResponse.json(
        { success: false, message: "Потребител с този email вече съществува" },
        { status: 400 }
      );
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);

    // Create admin user
    const [newUser] = await db
      .insert(users)
      .values({
        email,
        password: hashedPassword,
        name: name || "Admin",
        role: "admin",
        emailVerified: new Date(),
      })
      .$returningId();

    return NextResponse.json({
      success: true,
      message: "Admin потребител създаден успешно",
      userId: newUser.id,
    });
  } catch (error) {
    console.error("Error creating admin user:", error);
    return NextResponse.json(
      {
        success: false,
        message: "Грешка при създаване на admin потребител",
        error: error instanceof Error ? error.message : "Unknown error",
      },
      { status: 500 }
    );
  }
}
