"use server";

import crypto from "node:crypto";
import { execute } from "@/lib/db";
import { imageUrlsFromInput, toMysqlDateTime } from "@/lib/format";
import { requireAdmin } from "@/lib/auth";
import { uploadImages } from "@/lib/uploads";
import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";

function eventPayload(formData: FormData) {
  return {
    title: String(formData.get("title") ?? "").trim(),
    description: String(formData.get("description") ?? "").trim(),
    eventDate: toMysqlDateTime(formData.get("event_date")),
    location: String(formData.get("location") ?? "").trim() || null,
    imageUrls: imageUrlsFromInput(formData.get("image_urls")),
    status: String(formData.get("status") ?? "upcoming") === "past" ? "past" : "upcoming",
    sortOrder: Number(formData.get("sort_order") ?? 0),
  };
}

function assertValidEvent(payload: ReturnType<typeof eventPayload>) {
  if (!payload.title || !payload.description || !payload.eventDate) {
    throw new Error("Title, description, and event date are required.");
  }
}

export async function createEventAction(formData: FormData) {
  await requireAdmin();

  const payload = eventPayload(formData);
  assertValidEvent(payload);
  const uploadedUrls = await uploadImages(formData.getAll("image_files"), "events");
  const imageUrls = [...payload.imageUrls, ...uploadedUrls];

  await execute(
    `
      INSERT INTO events
        (id, title, description, event_date, location, image_urls, status, sort_order, created_at, updated_at)
      VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    `,
    [
      crypto.randomUUID(),
      payload.title,
      payload.description,
      payload.eventDate,
      payload.location,
      JSON.stringify(imageUrls),
      payload.status,
      payload.sortOrder,
    ],
  );

  revalidatePath("/events");
  revalidatePath("/dashboard");
  redirect("/events");
}

export async function updateEventAction(formData: FormData) {
  await requireAdmin();

  const id = String(formData.get("id") ?? "");
  const payload = eventPayload(formData);
  assertValidEvent(payload);
  const uploadedUrls = await uploadImages(formData.getAll("image_files"), "events");
  const imageUrls = [...payload.imageUrls, ...uploadedUrls];

  await execute(
    `
      UPDATE events
      SET title = ?, description = ?, event_date = ?, location = ?, image_urls = ?, status = ?, sort_order = ?, updated_at = NOW()
      WHERE id = ?
    `,
    [
      payload.title,
      payload.description,
      payload.eventDate,
      payload.location,
      JSON.stringify(imageUrls),
      payload.status,
      payload.sortOrder,
      id,
    ],
  );

  revalidatePath("/events");
  revalidatePath("/dashboard");
  redirect("/events");
}

export async function deleteEventAction(formData: FormData) {
  await requireAdmin();

  const id = String(formData.get("id") ?? "");

  if (id) {
    await execute("DELETE FROM events WHERE id = ?", [id]);
  }

  revalidatePath("/events");
  revalidatePath("/dashboard");
  redirect("/events");
}
