"use server";

import crypto from "node:crypto";
import { execute } from "@/lib/db";
import { requireAdmin } from "@/lib/auth";
import { uploadOptionalImage } from "@/lib/uploads";
import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";

function toNonNegativeInteger(value: FormDataEntryValue | null) {
  const number = Number(value ?? 0);

  return Number.isFinite(number) && number > 0 ? Math.trunc(number) : 0;
}

function galleryImagePayload(formData: FormData) {
  return {
    title: String(formData.get("title") ?? "").trim(),
    category: String(formData.get("category") ?? "").trim() || null,
    imageUrl: String(formData.get("image_url") ?? "").trim() || null,
    isActive: formData.get("is_active") === "1",
    sortOrder: toNonNegativeInteger(formData.get("sort_order")),
  };
}

function assertValidGalleryImage(payload: ReturnType<typeof galleryImagePayload>, imageUrl: string | null) {
  if (!payload.title || !imageUrl) {
    throw new Error("Title and image are required.");
  }
}

export async function createGalleryImageAction(formData: FormData) {
  await requireAdmin();

  const payload = galleryImagePayload(formData);
  const uploadedImageUrl = await uploadOptionalImage(formData.get("image_file"), "gallery");
  const imageUrl = uploadedImageUrl ?? payload.imageUrl;
  assertValidGalleryImage(payload, imageUrl);

  await execute(
    `
      INSERT INTO gallery_images
        (id, title, category, image_url, is_active, sort_order, created_at, updated_at)
      VALUES
        (?, ?, ?, ?, ?, ?, NOW(), NOW())
    `,
    [
      crypto.randomUUID(),
      payload.title,
      payload.category,
      imageUrl,
      payload.isActive ? 1 : 0,
      payload.sortOrder,
    ],
  );

  revalidatePath("/gallery");
  revalidatePath("/dashboard");
  redirect("/gallery");
}

export async function updateGalleryImageAction(formData: FormData) {
  await requireAdmin();

  const id = String(formData.get("id") ?? "");
  const payload = galleryImagePayload(formData);
  const uploadedImageUrl = await uploadOptionalImage(formData.get("image_file"), "gallery");
  const imageUrl = uploadedImageUrl ?? payload.imageUrl;
  assertValidGalleryImage(payload, imageUrl);

  await execute(
    `
      UPDATE gallery_images
      SET title = ?, category = ?, image_url = ?, is_active = ?, sort_order = ?, updated_at = NOW()
      WHERE id = ?
    `,
    [
      payload.title,
      payload.category,
      imageUrl,
      payload.isActive ? 1 : 0,
      payload.sortOrder,
      id,
    ],
  );

  revalidatePath("/gallery");
  revalidatePath("/dashboard");
  redirect("/gallery");
}

export async function deleteGalleryImageAction(formData: FormData) {
  await requireAdmin();

  const id = String(formData.get("id") ?? "");

  if (id) {
    await execute("DELETE FROM gallery_images WHERE id = ?", [id]);
  }

  revalidatePath("/gallery");
  revalidatePath("/dashboard");
  redirect("/gallery");
}
