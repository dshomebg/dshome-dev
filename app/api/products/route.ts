import { NextResponse } from "next/server";
import { db } from "@/src/db";
import { products, productCategories } from "@/src/db/schema";
import { eq, like, or, desc } from "drizzle-orm";

/**
 * GET /api/products
 * Връща списък с продукти
 */
export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url);
    const search = searchParams.get("search");
    const categoryId = searchParams.get("categoryId");
    const limit = parseInt(searchParams.get("limit") || "50");
    const offset = parseInt(searchParams.get("offset") || "0");

    let query = db.select().from(products);

    // Search filter
    if (search) {
      query = query.where(
        or(
          like(products.name, `%${search}%`),
          like(products.sku, `%${search}%`)
        )
      ) as typeof query;
    }

    // Category filter
    if (categoryId) {
      const productIds = await db
        .select({ productId: productCategories.productId })
        .from(productCategories)
        .where(eq(productCategories.categoryId, parseInt(categoryId)));

      const ids = productIds.map((p) => p.productId);
      if (ids.length > 0) {
        query = query.where(
          eq(products.id, ids[0])
        ) as typeof query;
      }
    }

    // Pagination and sorting
    const allProducts = await query
      .orderBy(desc(products.createdAt))
      .limit(limit)
      .offset(offset);

    // Get total count
    const totalCount = await db.select().from(products);

    return NextResponse.json({
      success: true,
      data: allProducts,
      count: allProducts.length,
      total: totalCount.length,
    });
  } catch (error) {
    console.error("Error fetching products:", error);
    return NextResponse.json(
      {
        success: false,
        message: "Грешка при зареждане на продукти",
        error: error instanceof Error ? error.message : "Unknown error",
      },
      { status: 500 }
    );
  }
}

/**
 * POST /api/products
 * Създава нов продукт
 */
export async function POST(request: Request) {
  try {
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
      categoryIds, // Array of category IDs
    } = body;

    // Validation
    if (!sku || !name || !slug || !price) {
      return NextResponse.json(
        { success: false, message: "SKU, име, slug и цена са задължителни" },
        { status: 400 }
      );
    }

    // Check if SKU already exists
    const [existingSku] = await db
      .select()
      .from(products)
      .where(eq(products.sku, sku))
      .limit(1);

    if (existingSku) {
      return NextResponse.json(
        { success: false, message: "Продукт с този SKU вече съществува" },
        { status: 400 }
      );
    }

    // Check if slug already exists
    const [existingSlug] = await db
      .select()
      .from(products)
      .where(eq(products.slug, slug))
      .limit(1);

    if (existingSlug) {
      return NextResponse.json(
        { success: false, message: "Продукт с този slug вече съществува" },
        { status: 400 }
      );
    }

    // Create product
    const [newProduct] = await db
      .insert(products)
      .values({
        sku,
        name,
        slug,
        description: description || null,
        shortDescription: shortDescription || null,
        price: price.toString(),
        priceWithoutVat: priceWithoutVat ? priceWithoutVat.toString() : null,
        cost: cost ? cost.toString() : null,
        discount: discount ? discount.toString() : "0",
        discountType: discountType || "fixed",
        discountStart: discountStart ? new Date(discountStart) : null,
        discountEnd: discountEnd ? new Date(discountEnd) : null,
        stock: stock || 0,
        lowStockThreshold: lowStockThreshold || 5,
        images: images || [],
        width: width ? width.toString() : null,
        height: height ? height.toString() : null,
        depth: depth ? depth.toString() : null,
        weight: weight ? weight.toString() : null,
        deliveryTime: deliveryTime || null,
        isActive: isActive !== undefined ? isActive : true,
        isFeatured: isFeatured || false,
        position: position || 0,
        metaTitle: metaTitle || null,
        metaDescription: metaDescription || null,
      })
      .$returningId();

    // Add category relations
    if (categoryIds && categoryIds.length > 0) {
      await db.insert(productCategories).values(
        categoryIds.map((catId: number, index: number) => ({
          productId: newProduct.id,
          categoryId: catId,
          isPrimary: index === 0, // First category is primary
        }))
      );
    }

    // Fetch created product
    const [created] = await db
      .select()
      .from(products)
      .where(eq(products.id, newProduct.id))
      .limit(1);

    return NextResponse.json({
      success: true,
      message: "Продуктът е създаден успешно",
      data: created,
    });
  } catch (error) {
    console.error("Error creating product:", error);
    return NextResponse.json(
      {
        success: false,
        message: "Грешка при създаване на продукт",
        error: error instanceof Error ? error.message : "Unknown error",
      },
      { status: 500 }
    );
  }
}
