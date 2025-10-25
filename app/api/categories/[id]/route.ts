import { NextResponse } from "next/server";
import { db } from "@/src/db";
import { categories } from "@/src/db/schema";
import { eq } from "drizzle-orm";

/**
 * GET /api/categories/[id]
 * Връща една категория по ID
 */
export async function GET(
  request: Request,
  { params }: { params: { id: string } }
) {
  try {
    const id = parseInt(params.id);

    if (isNaN(id)) {
      return NextResponse.json(
        { success: false, message: "Невалидно ID" },
        { status: 400 }
      );
    }

    const [category] = await db
      .select()
      .from(categories)
      .where(eq(categories.id, id))
      .limit(1);

    if (!category) {
      return NextResponse.json(
        { success: false, message: "Категорията не е намерена" },
        { status: 404 }
      );
    }

    return NextResponse.json({
      success: true,
      data: category,
    });
  } catch (error) {
    console.error("Error fetching category:", error);
    return NextResponse.json(
      {
        success: false,
        message: "Грешка при зареждане на категория",
        error: error instanceof Error ? error.message : "Unknown error",
      },
      { status: 500 }
    );
  }
}

/**
 * PUT /api/categories/[id]
 * Обновява категория
 */
export async function PUT(
  request: Request,
  { params }: { params: { id: string } }
) {
  try {
    const id = parseInt(params.id);

    if (isNaN(id)) {
      return NextResponse.json(
        { success: false, message: "Невалидно ID" },
        { status: 400 }
      );
    }

    const body = await request.json();
    const { name, slug, description, parentId, image, isActive, position, metaTitle, metaDescription } = body;

    // Check if category exists
    const [existing] = await db
      .select()
      .from(categories)
      .where(eq(categories.id, id))
      .limit(1);

    if (!existing) {
      return NextResponse.json(
        { success: false, message: "Категорията не е намерена" },
        { status: 404 }
      );
    }

    // Check if slug is unique (excluding current category)
    if (slug && slug !== existing.slug) {
      const [slugExists] = await db
        .select()
        .from(categories)
        .where(eq(categories.slug, slug))
        .limit(1);

      if (slugExists) {
        return NextResponse.json(
          { success: false, message: "Категория с този slug вече съществува" },
          { status: 400 }
        );
      }
    }

    // Prevent circular parent reference
    if (parentId === id) {
      return NextResponse.json(
        { success: false, message: "Категорията не може да бъде родител на себе си" },
        { status: 400 }
      );
    }

    // Update category
    await db
      .update(categories)
      .set({
        name: name || existing.name,
        slug: slug || existing.slug,
        description: description !== undefined ? description : existing.description,
        parentId: parentId !== undefined ? parentId : existing.parentId,
        image: image !== undefined ? image : existing.image,
        isActive: isActive !== undefined ? isActive : existing.isActive,
        position: position !== undefined ? position : existing.position,
        metaTitle: metaTitle !== undefined ? metaTitle : existing.metaTitle,
        metaDescription: metaDescription !== undefined ? metaDescription : existing.metaDescription,
      })
      .where(eq(categories.id, id));

    // Fetch updated category
    const [updated] = await db
      .select()
      .from(categories)
      .where(eq(categories.id, id))
      .limit(1);

    return NextResponse.json({
      success: true,
      message: "Категорията е обновена успешно",
      data: updated,
    });
  } catch (error) {
    console.error("Error updating category:", error);
    return NextResponse.json(
      {
        success: false,
        message: "Грешка при обновяване на категория",
        error: error instanceof Error ? error.message : "Unknown error",
      },
      { status: 500 }
    );
  }
}

/**
 * DELETE /api/categories/[id]
 * Изтрива категория
 */
export async function DELETE(
  request: Request,
  { params }: { params: { id: string } }
) {
  try {
    const id = parseInt(params.id);

    if (isNaN(id)) {
      return NextResponse.json(
        { success: false, message: "Невалидно ID" },
        { status: 400 }
      );
    }

    // Check if category exists
    const [existing] = await db
      .select()
      .from(categories)
      .where(eq(categories.id, id))
      .limit(1);

    if (!existing) {
      return NextResponse.json(
        { success: false, message: "Категорията не е намерена" },
        { status: 404 }
      );
    }

    // Check if category has subcategories
    const subcategories = await db
      .select()
      .from(categories)
      .where(eq(categories.parentId, id))
      .limit(1);

    if (subcategories.length > 0) {
      return NextResponse.json(
        {
          success: false,
          message: "Не можете да изтриете категория с подкатегории",
        },
        { status: 400 }
      );
    }

    // TODO: Check if category has products (when products table is created)

    // Delete category
    await db.delete(categories).where(eq(categories.id, id));

    return NextResponse.json({
      success: true,
      message: "Категорията е изтрита успешно",
    });
  } catch (error) {
    console.error("Error deleting category:", error);
    return NextResponse.json(
      {
        success: false,
        message: "Грешка при изтриване на категория",
        error: error instanceof Error ? error.message : "Unknown error",
      },
      { status: 500 }
    );
  }
}
