"use server";

import { execute } from "@/lib/db";
import { requireAdmin } from "@/lib/auth";
import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";

export async function markMessageReadAction(formData: FormData) {
  await requireAdmin();

  const id = Number(formData.get("id") ?? 0);

  if (id > 0) {
    await execute("UPDATE contact_messages SET read_at = NOW(), updated_at = NOW() WHERE id = ?", [id]);
  }

  revalidatePath("/messages");
  revalidatePath("/dashboard");
  redirect("/messages");
}

export async function deleteMessageAction(formData: FormData) {
  await requireAdmin();

  const id = Number(formData.get("id") ?? 0);

  if (id > 0) {
    await execute("DELETE FROM contact_messages WHERE id = ?", [id]);
  }

  revalidatePath("/messages");
  revalidatePath("/dashboard");
  redirect("/messages");
}
