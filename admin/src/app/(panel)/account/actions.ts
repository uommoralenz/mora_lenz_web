"use server";

import { api, toActionState } from "@/lib/api";
import { setToken } from "@/lib/session";
import type { ActionState } from "@/lib/types";

export async function changePasswordAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const password = String(formData.get("password") ?? "");
  const confirmation = String(formData.get("password_confirmation") ?? "");

  if (password !== confirmation) {
    return { ok: false, message: "The two new passwords do not match." };
  }

  try {
    const result = await api.post<{
      message: string;
      token: string;
      expires_at: string;
    }>("/auth/password", {
      current_password: String(formData.get("current_password") ?? ""),
      password,
      password_confirmation: confirmation,
    });

    // Laravel revoked every token including this one, so store the new token
    // it issued — otherwise the next click would bounce back to the login page.
    await setToken(result.token, result.expires_at);

    return { ok: true, message: result.message };
  } catch (error) {
    return toActionState(error);
  }
}
