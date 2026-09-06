"use server";

import { createSession, destroySession, verifyAdminLogin } from "@/lib/auth";
import { redirect } from "next/navigation";
import { consumeRateLimit } from "@/lib/rate-limit";

export async function loginAction(formData: FormData) {
  const email = String(formData.get("email") ?? "").trim().toLowerCase();
  const password = String(formData.get("password") ?? "");

  if (!await consumeRateLimit("login-global", "all", 100, 60) ||
      !await consumeRateLimit("login-account", email.slice(0, 255), 5, 900)) {
    redirect("/login?error=rate-limit");
  }
  const admin = await verifyAdminLogin(email, password);

  if (!admin) {
    redirect("/login?error=invalid");
  }

  await createSession(admin);
  redirect("/dashboard");
}

export async function logoutAction() {
  await destroySession();
  redirect("/login");
}
