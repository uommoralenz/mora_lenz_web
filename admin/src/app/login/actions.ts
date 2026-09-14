"use server";

import { redirect } from "next/navigation";

import { api, ApiError, toActionState } from "@/lib/api";
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
    if (!result?.token || !result.admin?.id) {
      return { ok: false, message: "The login server returned an invalid session response. Contact the site administrator." };
    }
    const expiry = Date.parse(result.expires_at);
    if (!Number.isFinite(expiry) || expiry <= Date.now()) {
      return { ok: false, message: "The login server issued an already expired or invalid session. Ask the hosting provider to check the server clock and session expiry configuration." };
    }
    try {
      const verified = await api.verifySession(result.token);
      if (verified?.admin?.id !== result.admin.id) {
        return { ok: false, message: "The login server could not verify your new session. Contact the site administrator." };
      }
    } catch (error) {
      if (error instanceof ApiError && [401, 403].includes(error.status)) {
        return { ok: false, message: "Your password was accepted, but the login server rejected the new session. Ask the site administrator to check API token handling on the Laravel server." };
      }
      throw error;
    }
    await setToken(result.token, result.expires_at);
  } catch (error) {
    return toActionState(error);
  }

  // redirect() throws internally, so it must sit outside the try block or the
  // catch above would swallow it and the login would silently do nothing.
  redirect("/dashboard");
}
