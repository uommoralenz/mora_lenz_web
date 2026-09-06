"use server";

import crypto from "node:crypto";
import { execute } from "@/lib/db";
import { requireAdmin } from "@/lib/auth";
import { uploadOptionalImage } from "@/lib/uploads";
import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";

function memberPayload(formData: FormData) {
  const cardSize = String(formData.get("card_size") ?? "sm");

  return {
    firstName: String(formData.get("first_name") ?? "").trim(),
    lastName: String(formData.get("last_name") ?? "").trim() || null,
    pillarOrPanel: String(formData.get("pillar_or_panel") ?? "").trim() || null,
    position: String(formData.get("position") ?? "").trim(),
    bio: String(formData.get("bio") ?? "").trim() || null,
    photoUrl: String(formData.get("photo_url") ?? "").trim() || null,
    cardSize: cardSize === "md" || cardSize === "lg" ? cardSize : "sm",
    sortOrder: Number(formData.get("sort_order") ?? 0),
  };
}

function assertValidMember(payload: ReturnType<typeof memberPayload>) {
  if (!payload.firstName || !payload.position) {
    throw new Error("First name and position are required.");
  }
}

export async function createMemberAction(formData: FormData) {
  await requireAdmin();

  const payload = memberPayload(formData);
  assertValidMember(payload);
  const uploadedPhotoUrl = await uploadOptionalImage(formData.get("photo_file"), "members");

  await execute(
    `
      INSERT INTO members
        (id, first_name, last_name, pillar_or_panel, position, bio, photo_url, card_size, sort_order, created_at, updated_at)
      VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    `,
    [
      crypto.randomUUID(),
      payload.firstName,
      payload.lastName,
      payload.pillarOrPanel,
      payload.position,
      payload.bio,
      uploadedPhotoUrl ?? payload.photoUrl,
      payload.cardSize,
      payload.sortOrder,
    ],
  );

  revalidatePath("/members");
  revalidatePath("/dashboard");
  redirect("/members");
}

export async function updateMemberAction(formData: FormData) {
  await requireAdmin();

  const id = String(formData.get("id") ?? "");
  const payload = memberPayload(formData);
  assertValidMember(payload);
  const uploadedPhotoUrl = await uploadOptionalImage(formData.get("photo_file"), "members");

  await execute(
    `
      UPDATE members
      SET first_name = ?, last_name = ?, pillar_or_panel = ?, position = ?, bio = ?, photo_url = ?, card_size = ?, sort_order = ?, updated_at = NOW()
      WHERE id = ?
    `,
    [
      payload.firstName,
      payload.lastName,
      payload.pillarOrPanel,
      payload.position,
      payload.bio,
      uploadedPhotoUrl ?? payload.photoUrl,
      payload.cardSize,
      payload.sortOrder,
      id,
    ],
  );

  revalidatePath("/members");
  revalidatePath("/dashboard");
  redirect("/members");
}

export async function deleteMemberAction(formData: FormData) {
  await requireAdmin();

  const id = String(formData.get("id") ?? "");

  if (id) {
    await execute("DELETE FROM members WHERE id = ?", [id]);
  }

  revalidatePath("/members");
  revalidatePath("/dashboard");
  redirect("/members");
}
