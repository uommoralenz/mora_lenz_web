import "server-only";

import { cookies } from "next/headers";

export const SESSION_COOKIE =
  process.env.SESSION_COOKIE || "moralenz_admin_session";

/**
 * The admin's API token lives in an httpOnly cookie that only this Next.js
 * server can read. It is never sent to the browser as JavaScript-readable
 * state, and the Laravel API URL is never exposed to the client either.
 */
export async function getToken(): Promise<string | null> {
  const store = await cookies();
  return store.get(SESSION_COOKIE)?.value ?? null;
}

export async function setToken(token: string, expiresAt?: string) {
  const store = await cookies();

  const expires = expiresAt ? new Date(expiresAt) : undefined;
  const valid = expires && !Number.isNaN(expires.getTime()) ? expires : undefined;

  store.set(SESSION_COOKIE, token, {
    httpOnly: true,
    // Allow http only when explicitly opted in for local development.
    secure: process.env.ALLOW_INSECURE_COOKIE !== "1",
    sameSite: "lax",
    path: "/",
    expires: valid,
  });
}

export async function clearToken() {
  const store = await cookies();
  store.delete(SESSION_COOKIE);
}
