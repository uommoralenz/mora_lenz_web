"use server";

import { revalidatePath } from "next/cache";

import { api } from "@/lib/api";

export async function toggleReadAction(formData: FormData) {
  const id = String(formData.get("id") ?? "");
  const isRead = String(formData.get("is_read") ?? "") === "1";

  if (id) {
    await api.put(`/messages/${id}`, { is_read: isRead });
    revalidatePath("/messages");
    revalidatePath("/dashboard");
  }
}

export async function deleteMessageAction(formData: FormData) {
  const id = String(formData.get("id") ?? "");

  if (id) {
    await api.del(`/messages/${id}`);
    revalidatePath("/messages");
    revalidatePath("/dashboard");
  }
}
