"use server";

import crypto from "node:crypto";
import { execute } from "@/lib/db";
import { requireAdmin } from "@/lib/auth";
import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";

const allowedIcons = new Set([
  "users",
  "camera",
  "video",
  "dollar-sign",
  "user-check",
  "message-circle",
  "file-text",
  "pen-tool",
  "mic",
  "award",
  "calendar",
  "book-open",
]);

function toNonNegativeInteger(value: FormDataEntryValue | null) {
  const number = Number(value ?? 0);

  return Number.isFinite(number) && number > 0 ? Math.trunc(number) : 0;
}

function panelPillarPayload(formData: FormData) {
  const type = String(formData.get("type") ?? "panel");
  const icon = String(formData.get("icon") ?? "users");

  return {
    type: type === "pillar" ? "pillar" : "panel",
    icon: allowedIcons.has(icon) ? icon : "users",
    name: String(formData.get("name") ?? "").trim(),
    description: String(formData.get("description") ?? "").trim() || null,
    memberCount: toNonNegativeInteger(formData.get("member_count")),
    sortOrder: toNonNegativeInteger(formData.get("sort_order")),
  };
}

function assertValidPanelPillar(payload: ReturnType<typeof panelPillarPayload>) {
  if (!payload.name) {
    throw new Error("Name is required.");
  }
}

export async function createPanelPillarAction(formData: FormData) {
  await requireAdmin();

  const payload = panelPillarPayload(formData);
  assertValidPanelPillar(payload);

  await execute(
    `
      INSERT INTO panels_pillars
        (id, type, icon, name, description, member_count, sort_order, created_at, updated_at)
      VALUES
        (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    `,
    [
      crypto.randomUUID(),
      payload.type,
      payload.icon,
      payload.name,
      payload.description,
      payload.memberCount,
      payload.sortOrder,
    ],
  );

  revalidatePath("/panels-pillars");
  revalidatePath("/dashboard");
  redirect("/panels-pillars");
}

export async function updatePanelPillarAction(formData: FormData) {
  await requireAdmin();

  const id = String(formData.get("id") ?? "");
  const payload = panelPillarPayload(formData);
  assertValidPanelPillar(payload);

  await execute(
    `
      UPDATE panels_pillars
      SET type = ?, icon = ?, name = ?, description = ?, member_count = ?, sort_order = ?, updated_at = NOW()
      WHERE id = ?
    `,
    [
      payload.type,
      payload.icon,
      payload.name,
      payload.description,
      payload.memberCount,
      payload.sortOrder,
      id,
    ],
  );

  revalidatePath("/panels-pillars");
  revalidatePath("/dashboard");
  redirect("/panels-pillars");
}

export async function deletePanelPillarAction(formData: FormData) {
  await requireAdmin();

  const id = String(formData.get("id") ?? "");

  if (id) {
    await execute("DELETE FROM panels_pillars WHERE id = ?", [id]);
  }

  revalidatePath("/panels-pillars");
  revalidatePath("/dashboard");
  redirect("/panels-pillars");
}
