"use server";

import { revalidatePath } from "next/cache";

import { api, toActionState } from "@/lib/api";
import type { ActionState } from "@/lib/types";

/** Both actions take an id straight from the list, so check it is one. */
function messageId(formData: FormData): string | null {
  const id = String(formData.get("id") ?? "");

  return /^\d+$/.test(id) && Number(id) > 0 ? id : null;
}

function refresh() {
  revalidatePath("/messages");
  revalidatePath("/dashboard");
}

export async function toggleReadAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = messageId(formData);

  if (!id) {
    return { ok: false, message: "That message is no longer in the list. Reload the page." };
  }

  const isRead = String(formData.get("is_read") ?? "") === "1";

  try {
    await api.put(`/messages/${id}`, { is_read: isRead });
  } catch (error) {
    return toActionState(error);
  }

  refresh();

  return { ok: true, message: isRead ? "Marked as read." : "Marked as unread." };
}

export async function deleteMessageAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = messageId(formData);

  if (!id) {
    return { ok: false, message: "That message is no longer in the list. Reload the page." };
  }

  try {
    await api.del(`/messages/${id}`);
  } catch (error) {
    return toActionState(error);
  }

  refresh();

  return { ok: true, message: "Message deleted." };
}
