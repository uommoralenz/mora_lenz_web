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

  // Checkboxes: always send the ticked sections as a list (empty means "no
  // sections"), so unticking everything is actually saved.
  const permissions = formData.getAll("permissions").map(String);
  formData.delete("permissions");

  const body = { ...Object.fromEntries(formData.entries()), permissions };

  let created: { mail_sent: boolean; temporary_password: string | null } | null = null;

  try {
    if (id) {
      await api.put(`/admins/${id}`, body);
    } else {
      created = await api.post("/admins", body);
    }
  } catch (error) {
    return toActionState(error);
  }

  revalidatePath("/admins");
  revalidatePath("/dashboard");

  if (!created) {
    return { ok: true, message: "Admin updated." };
  }

  return {
    ok: true,
    message: created.mail_sent
      ? "Admin created. A temporary password was emailed to them; they must change it at their first sign-in."
      : `Admin created, but the email could not be sent. Give them this temporary password yourself (shown once): ${created.temporary_password}`,
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

export async function resetAdminPasswordAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");

  if (!id) return { ok: false, message: "Missing admin." };

  try {
    const result = await api.post<{ mail_sent: boolean; temporary_password: string | null }>(
      `/admins/${id}/reset-password`
    );

    revalidatePath("/admins");

    return {
      ok: true,
      message: result.mail_sent
        ? "A new temporary password was emailed. They must change it at their next sign-in."
        : `The email could not be sent. Give them this temporary password yourself (shown once): ${result.temporary_password}`,
    };
  } catch (error) {
    return toActionState(error);
  }
}
