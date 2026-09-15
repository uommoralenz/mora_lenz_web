"use server";

import { revalidatePath } from "next/cache";

import { api, asUpdate, pruneEmptyFile, setBool, toActionState } from "@/lib/api";
import type { ActionState } from "@/lib/types";

export async function saveGalleryAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");

  setBool(formData, "is_active");

  // New albums do not need to send a false homepage flag: Laravel defaults it
  // to false. This also avoids hosts that mishandle an unchecked multipart
  // checkbox value. Updates must still explicitly send false when unchecked.
  if (id) {
    setBool(formData, "show_on_homepage");
  } else if (formData.get("show_on_homepage")) {
    formData.set("show_on_homepage", "1");
  } else {
    formData.delete("show_on_homepage");
  }
  pruneEmptyFile(formData);
  formData.delete("id");

  try {
    if (id) {
      await api.post(`/galleries/${id}`, asUpdate(formData));
    } else {
      await api.post("/galleries", formData);
    }
  } catch (error) {
    return toActionState(error);
  }

  revalidatePath("/gallery");
  revalidatePath("/dashboard");

  return { ok: true, message: id ? "Gallery updated." : "Gallery added." };
}

export async function deleteGalleryAction(_prev: ActionState, formData: FormData): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");

  if (!/^\d+$/.test(id) || Number(id) < 1) {
    return { ok: false, message: "Select a valid gallery entry to delete." };
  }

  try {
    await api.del(`/galleries/${id}`);
  } catch (error) {
    return toActionState(error);
  }

  revalidatePath("/gallery");
  revalidatePath("/dashboard");
  return { ok: true, message: "Gallery entry deleted." };
}

export async function reorderGalleriesAction(formData: FormData) {
  const raw = String(formData.get("items") ?? "");

  if (!raw) return;

  await api.post("/galleries/reorder", { items: JSON.parse(raw) });
  revalidatePath("/gallery");
}
