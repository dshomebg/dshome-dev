import { NextResponse } from "next/server";
import { db } from "@/src/db";
import { categories } from "@/src/db/schema";
import { eq, isNull } from "drizzle-orm";

/**
 * GET /api/categories
 * Връща списък с всички категории
 */
export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url);
    const parentId = searchParams.get("parentId");

    let query;
    if (parentId === "null" || parentId === null) {
      // Връщаме само root категории (без parent)
      query = db
        .select()
        .from(categories)
        .where(isNull(categories.parentId))
        .orderBy(categories.position, categories.name);
    } else if (parentId) {
      // Връщаме subcategories за конкретен parent
      query = db
        .select()
        .from(categories)
        .where(eq(categories.parentId, parseInt(parentId)))
        .orderBy(categories.position, categories.name);
    } else {
      // Връщаме всички категории
      query = db
        .select()
        .from(categories)
        .orderBy(categories.position, categories.name);
    }

    const allCategories = await query;

    return NextResponse.json({
      success: true,
      data: allCategories,
      count: allCategories.length,
    });
  } catch (error) {
    console.error("Error fetching categories:", error);
    return NextResponse.json(
      {
        success: false,
        message: "Грешка при зареждане на категории",
        error: error instanceof Error ? error.message : "Unknown error",
      },
      { status: 500 }
    );
  }
}

/**
 * POST /api/categories
 * Създава нова категория
 */
export async function POST(request: Request) {
  try {
    const body = await request.json();
    const { name, slug, description, parentId, image, isActive, position, metaTitle, metaDescription } = body;

    // Validation
    if (!name || !slug) {
      return NextResponse.json(
        { success: false, message: "Име и slug са задължителни" },
        { status: 400 }
      );
    }

    // Check if slug already exists
    const [existing] = await db
      .select()
      .from(categories)
      .where(eq(categories.slug, slug))
      .limit(1);

    if (existing) {
      return NextResponse.json(
        { success: false, message: "Категория с този slug вече съществува" },
        { status: 400 }
      );
    }

    // Create category
    const [newCategory] = await db
      .insert(categories)
      .values({
        name,
        slug,
        description: description || null,
        parentId: parentId || null,
        image: image || null,
        isActive: isActive !== undefined ? isActive : true,
        position: position || 0,
        metaTitle: metaTitle || null,
        metaDescription: metaDescription || null,
      })
      .$returningId();

    // Fetch created category
    const [created] = await db
      .select()
      .from(categories)
      .where(eq(categories.id, newCategory.id))
      .limit(1);

    return NextResponse.json({
      success: true,
      message: "Категорията е създадена успешно",
      data: created,
    });
  } catch (error) {
    console.error("Error creating category:", error);
    return NextResponse.json(
      {
        success: false,
        message: "Грешка при създаване на категория",
        error: error instanceof Error ? error.message : "Unknown error",
      },
      { status: 500 }
    );
  }
}
