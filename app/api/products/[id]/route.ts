import { NextResponse } from "next/server";
import { db } from "@/src/db";
import { products, productCategories } from "@/src/db/schema";
import { eq } from "drizzle-orm";

/**
 * GET /api/products/[id]
 * Връща един продукт по ID
 */
export async function GET(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id: idStr } = await params;
    const id = parseInt(idStr);

    if (isNaN(id)) {
      return NextResponse.json(
        { success: false, message: "Невалидно ID" },
        { status: 400 }
      );
    }

    const [product] = await db
      .select()
      .from(products)
      .where(eq(products.id, id))
      .limit(1);

    if (!product) {
      return NextResponse.json(
        { success: false, message: "Продуктът не е намерен" },
        { status: 404 }
      );
    }

    // Get product categories
    const categories = await db
      .select()
      .from(productCategories)
      .where(eq(productCategories.productId, id));

    return NextResponse.json({
      success: true,
      data: {
        ...product,
        categoryIds: categories.map((c) => c.categoryId),
      },
    });
  } catch (error) {
    console.error("Error fetching product:", error);
    return NextResponse.json(
      {
        success: false,
        message: "Грешка при зареждане на продукт",
        error: error instanceof Error ? error.message : "Unknown error",
      },
      { status: 500 }
    );
  }
}

/**
 * PUT /api/products/[id]
 * Обновява продукт
 */
export async function PUT(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id: idStr } = await params;
    const id = parseInt(idStr);

    if (isNaN(id)) {
      return NextResponse.json(
        { success: false, message: "Невалидно ID" },
        { status: 400 }
      );
    }

    const body = await request.json();
    const {
      sku,
      name,
      slug,
      description,
      shortDescription,
      price,
      priceWithoutVat,
      cost,
      discount,
      discountType,
      discountStart,
      discountEnd,
      stock,
      lowStockThreshold,
      images,
      width,
      height,
      depth,
      weight,
      deliveryTime,
      isActive,
      isFeatured,
      position,
      metaTitle,
      metaDescription,
      categoryIds,
    } = body;

    // Check if product exists
    const [existing] = await db
      .select()
      .from(products)
      .where(eq(products.id, id))
      .limit(1);

    if (!existing) {
      return NextResponse.json(
        { success: false, message: "Продуктът не е намерен" },
        { status: 404 }
      );
    }

    // Check if SKU is unique (excluding current product)
    if (sku && sku !== existing.sku) {
      const [skuExists] = await db
        .select()
        .from(products)
        .where(eq(products.sku, sku))
        .limit(1);

      if (skuExists) {
        return NextResponse.json(
          { success: false, message: "Продукт с този SKU вече съществува" },
          { status: 400 }
        );
      }
    }

    // Check if slug is unique (excluding current product)
    if (slug && slug !== existing.slug) {
      const [slugExists] = await db
        .select()
        .from(products)
        .where(eq(products.slug, slug))
        .limit(1);

      if (slugExists) {
        return NextResponse.json(
          { success: false, message: "Продукт с този slug вече съществува" },
          { status: 400 }
        );
      }
    }

    // Update product
    await db
      .update(products)
      .set({
        sku: sku || existing.sku,
        name: name || existing.name,
        slug: slug || existing.slug,
        description: description !== undefined ? description : existing.description,
        shortDescription: shortDescription !== undefined ? shortDescription : existing.shortDescription,
        price: price ? price.toString() : existing.price,
        priceWithoutVat: priceWithoutVat !== undefined ? (priceWithoutVat ? priceWithoutVat.toString() : null) : existing.priceWithoutVat,
        cost: cost !== undefined ? (cost ? cost.toString() : null) : existing.cost,
        discount: discount !== undefined ? discount.toString() : existing.discount,
        discountType: discountType || existing.discountType,
        discountStart: discountStart !== undefined ? (discountStart ? new Date(discountStart) : null) : existing.discountStart,
        discountEnd: discountEnd !== undefined ? (discountEnd ? new Date(discountEnd) : null) : existing.discountEnd,
        stock: stock !== undefined ? stock : existing.stock,
        lowStockThreshold: lowStockThreshold !== undefined ? lowStockThreshold : existing.lowStockThreshold,
        images: images !== undefined ? images : existing.images,
        width: width !== undefined ? (width ? width.toString() : null) : existing.width,
        height: height !== undefined ? (height ? height.toString() : null) : existing.height,
        depth: depth !== undefined ? (depth ? depth.toString() : null) : existing.depth,
        weight: weight !== undefined ? (weight ? weight.toString() : null) : existing.weight,
        deliveryTime: deliveryTime !== undefined ? deliveryTime : existing.deliveryTime,
        isActive: isActive !== undefined ? isActive : existing.isActive,
        isFeatured: isFeatured !== undefined ? isFeatured : existing.isFeatured,
        position: position !== undefined ? position : existing.position,
        metaTitle: metaTitle !== undefined ? metaTitle : existing.metaTitle,
        metaDescription: metaDescription !== undefined ? metaDescription : existing.metaDescription,
      })
      .where(eq(products.id, id));

    // Update category relations
    if (categoryIds !== undefined) {
      // Delete existing relations
      await db.delete(productCategories).where(eq(productCategories.productId, id));

      // Add new relations
      if (categoryIds.length > 0) {
        await db.insert(productCategories).values(
          categoryIds.map((catId: number, index: number) => ({
            productId: id,
            categoryId: catId,
            isPrimary: index === 0,
          }))
        );
      }
    }

    // Fetch updated product
    const [updated] = await db
      .select()
      .from(products)
      .where(eq(products.id, id))
      .limit(1);

    return NextResponse.json({
      success: true,
      message: "Продуктът е обновен успешно",
      data: updated,
    });
  } catch (error) {
    console.error("Error updating product:", error);
    return NextResponse.json(
      {
        success: false,
        message: "Грешка при обновяване на продукт",
        error: error instanceof Error ? error.message : "Unknown error",
      },
      { status: 500 }
    );
  }
}

/**
 * DELETE /api/products/[id]
 * Изтрива продукт
 */
export async function DELETE(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id: idStr } = await params;
    const id = parseInt(idStr);

    if (isNaN(id)) {
      return NextResponse.json(
        { success: false, message: "Невалидно ID" },
        { status: 400 }
      );
    }

    // Check if product exists
    const [existing] = await db
      .select()
      .from(products)
      .where(eq(products.id, id))
      .limit(1);

    if (!existing) {
      return NextResponse.json(
        { success: false, message: "Продуктът не е намерен" },
        { status: 404 }
      );
    }

    // Delete category relations first
    await db.delete(productCategories).where(eq(productCategories.productId, id));

    // Delete product
    await db.delete(products).where(eq(products.id, id));

    return NextResponse.json({
      success: true,
      message: "Продуктът е изтрит успешно",
    });
  } catch (error) {
    console.error("Error deleting product:", error);
    return NextResponse.json(
      {
        success: false,
        message: "Грешка при изтриване на продукт",
        error: error instanceof Error ? error.message : "Unknown error",
      },
      { status: 500 }
    );
  }
}
