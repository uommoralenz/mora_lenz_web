"use server";

import { redirect } from "next/navigation";

import { api, toActionState } from "@/lib/api";
import { setToken } from "@/lib/session";
import type { ActionState } from "@/lib/types";

export async function loginAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const username = String(formData.get("username") ?? "").trim();
  const password = String(formData.get("password") ?? "");

  if (!username || !password) {
    return { ok: false, message: "Enter your username and password." };
  }

  try {
    const result = await api.login(username, password);
    await setToken(result.token, result.expires_at);
  } catch (error) {
    return toActionState(error);
  }

  // redirect() throws internally, so it must sit outside the try block or the
  // catch above would swallow it and the login would silently do nothing.
  redirect("/dashboard");
}
