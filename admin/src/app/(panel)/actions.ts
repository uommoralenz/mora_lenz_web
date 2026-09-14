"use server";

import { redirect } from "next/navigation";

import { api } from "@/lib/api";
import { clearToken } from "@/lib/session";

export async function logoutAction() {
  // Tell Laravel to revoke the token, then drop the cookie either way — a
  // failed round trip must never leave the user stuck signed in.
  try {
    await api.post("/auth/logout");
  } catch {
    // Ignored on purpose.
  }

  await clearToken();
  redirect("/login");
}
