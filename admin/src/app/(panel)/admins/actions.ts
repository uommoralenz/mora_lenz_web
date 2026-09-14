"use server";

import { revalidatePath } from "next/cache";

import { api, setBool, toActionState } from "@/lib/api";
import type { ActionState } from "@/lib/types";

export async function saveAdminAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");

  setBool(formData, "is_super_admin");
  setBool(formData, "is_active");
  formData.delete("id");

  // On edit, an empty password field means "keep the current password".
  if (id && !String(formData.get("password") ?? "").trim()) {
    formData.delete("password");
  }

  const body = Object.fromEntries(formData.entries());

  try {
    if (id) {
      await api.put(`/admins/${id}`, body);
    } else {
      await api.post("/admins", body);
    }
  } catch (error) {
    return toActionState(error);
  }

  revalidatePath("/admins");
  revalidatePath("/dashboard");

  return {
    ok: true,
    message: id
      ? "Admin updated. If you changed the password, their other sessions were signed out."
      : "Admin created. Give them the username and password directly — there is no email invite.",
  };
}

export async function deleteAdminAction(formData: FormData) {
  const id = String(formData.get("id") ?? "");

  if (id) {
    await api.del(`/admins/${id}`);
    revalidatePath("/admins");
    revalidatePath("/dashboard");
  }
}
