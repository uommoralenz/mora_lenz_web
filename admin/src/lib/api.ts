import "server-only";


import { redirect } from "next/navigation";

import { getToken } from "./session";
import type { ActionState, AdminUser } from "./types";

const BASE = (process.env.LARAVEL_API_URL || "").replace(/\/+$/, "");

/**
 * The university server runs nginx with no rewrite rule, so only paths that
 * physically exist reach PHP. When LARAVEL_API_COMPAT=1, every call is sent to
 * the one real file at /api/admin/index.php with the intended route carried in
 * a `__path` query parameter, which that shim converts back before Laravel
 * routes it.
 *
 * Unset this env var (and delete the shim folder) once nginx has:
 *     location / { try_files $uri $uri/ /index.php?$query_string; }
 */
const COMPAT = process.env.LARAVEL_API_COMPAT === "1";

/** Turns an API path like "/messages?page=2" into the URL to actually fetch. */
function buildUrl(path: string): string {
  if (!COMPAT) return `${BASE}${path}`;

  const [pathname, search = ""] = path.split("?");
  const params = new URLSearchParams(search);

  // __path goes first so it is easy to spot in server logs.
  const query = new URLSearchParams({ __path: pathname });
  params.forEach((value, key) => query.append(key, value));

  return `${BASE}/?${query.toString()}`;
}

/** Thrown for any non-2xx response from Laravel. */
export class ApiError extends Error {
  status: number;
  errors: Record<string, string>;

  constructor(status: number, message: string, errors: Record<string, string> = {}) {
    super(message);
    this.name = "ApiError";
    this.status = status;
    this.errors = errors;
  }
}

/** Laravel returns { message, errors: { field: [msg, ...] } } for 422s. */
function flattenErrors(body: unknown): Record<string, string> {
  const out: Record<string, string> = {};
  const errors = (body as { errors?: Record<string, string[]> })?.errors;

  if (errors && typeof errors === "object") {
    for (const [field, messages] of Object.entries(errors)) {
      if (Array.isArray(messages) && messages.length > 0) {
        out[field] = String(messages[0]);
      }
    }
  }

  return out;
}

/** True for the special error `redirect()` throws, which must never be caught. */
function isRedirectError(error: unknown): boolean {
  const digest = (error as { digest?: unknown })?.digest;
  return typeof digest === "string" && digest.startsWith("NEXT_REDIRECT");
}

interface RequestOptions {
  /** Verify a newly issued token before saving the browser session. */
  token?: string;
  method?: string;
  body?: FormData | Record<string, unknown>;
  /** Set false for endpoints that do not need a token (only login). */
  auth?: boolean;
  /** Seconds to cache. Defaults to no caching — the panel must show live data. */
  revalidate?: number;
  /**
   * By default an expired or revoked session sends the user to /login. Set this
   * to true to get an ApiError instead — used by currentAdmin(), which needs to
   * answer "are you signed in?" rather than act on the answer.
   */
  soft?: boolean;
}

async function request<T>(path: string, options: RequestOptions = {}): Promise<T> {
  if (!BASE) {
    throw new ApiError(
      500,
      "LARAVEL_API_URL is not set. Add it in the Vercel project's environment variables."
    );
  }

  const { method = "GET", body, auth = true, revalidate, soft = false } = options;

  const headers: Record<string, string> = { Accept: "application/json" };
  let payload: BodyInit | undefined;

  if (body instanceof FormData) {
    // Let fetch set the multipart boundary itself — never set Content-Type here.
    payload = body;
  } else if (body) {
    headers["Content-Type"] = "application/json";
    payload = JSON.stringify(body);
  }

  if (auth) {
    const token = options.token ?? await getToken();

    if (!token) {
      // No cookie at all: go straight to the login page rather than rendering
      // a page that is about to fail anyway.
      if (!soft) redirect("/login");

      throw new ApiError(401, "Your session has expired. Please sign in again.");
    }

    // Sent both ways on purpose: some hosts (this one included) run nginx in
    // front of PHP-FPM without forwarding the Authorization header, which
    // would otherwise make every request after login look unauthenticated.
    // X-Admin-Token carries the same value through a header that always
    // reaches PHP, so AdminAuth::resolve() falls back to it automatically.
    headers.Authorization = `Bearer ${token}`;
    headers["X-Admin-Token"] = token;
  }

  let response: Response;

  try {
    response = await fetch(buildUrl(path), {
      method,
      headers,
      body: payload,
      cache: auth || revalidate === undefined ? "no-store" : undefined,
      next: auth || revalidate === undefined ? undefined : { revalidate },
    });
  } catch {
    throw new ApiError(
      503,
      "Could not reach the Mora Lenz server. It may be offline — try again in a moment."
    );
  }

  const text = await response.text();
  let parsed: unknown = null;

  if (text) {
    try {
      parsed = JSON.parse(text);
    } catch {
      // A non-JSON body means PHP produced an HTML error page.
      if (!response.ok) {
        throw new ApiError(
          response.status,
          `The server returned an unexpected response (HTTP ${response.status}).`
        );
      }
    }
  }

  if (!response.ok) {
    if (response.status >= 500) {
      throw new ApiError(response.status, "The server could not complete this request. Please try again or contact the administrator.");
    }
    // Laravel revoked or expired this token (or the admin was deactivated).
    // Server Components may redirect but cannot modify cookies. Logout clears
    // the cookie, and a later successful login overwrites a stale token.
    if (response.status === 401 && !soft) {
      redirect("/login");
    }

    const message =
      (parsed as { message?: string })?.message ||
      `Request failed with status ${response.status}.`;

    throw new ApiError(response.status, message, flattenErrors(parsed));
  }

  return parsed as T;
}

export const api = {
  verifySession: (token: string) =>
    request<{ admin: AdminUser }>("/auth/me", { token, soft: true }),
  get: <T>(path: string, revalidate?: number) => request<T>(path, { revalidate }),
  post: <T>(path: string, body?: RequestOptions["body"]) =>
    request<T>(path, { method: "POST", body }),
  put: <T>(path: string, body?: RequestOptions["body"]) =>
    request<T>(path, { method: "PUT", body }),
  del: <T>(path: string, body?: RequestOptions["body"]) => {
    // The compatibility host rejects DELETE at nginx. Laravel accepts a POST
    // with _method=DELETE, just as it accepts our multipart PUT overrides.
    if (!COMPAT) return request<T>(path, { method: "DELETE", body });

    const form = new FormData();
    if (body instanceof FormData) {
      body.forEach((value, key) => form.append(key, value));
    } else {
      return request<T>(path, { method: "POST", body: { ...body, _method: "DELETE" } });
    }
    form.set("_method", "DELETE");
    return request<T>(path, { method: "POST", body: form });
  },
  /** Login is the one call made without a token. */
  login: (username: string, password: string) =>
    request<{ token: string; expires_at: string; admin: AdminUser }>("/auth/login", {
      method: "POST",
      body: { username, password },
      auth: false,
    }),
};

/**
 * Multipart updates: PHP does not populate $_FILES for PUT requests, so file
 * uploads are sent as POST with Laravel's _method override.
 */
export function asUpdate(form: FormData): FormData {
  form.set("_method", "PUT");
  return form;
}

/** The signed-in admin, or null if the token is missing/expired/revoked. */
export async function currentAdmin(): Promise<AdminUser | null> {
  const token = await getToken();

  if (!token) return null;

  try {
    // soft: this is the one call that must be able to answer "no" instead of
    // redirecting, because /login itself uses it to decide what to render.
    const { admin } = await request<{ admin: AdminUser }>("/auth/me", { soft: true });
    return admin;
  } catch (error) {
    if (error instanceof ApiError && (error.status === 401 || error.status === 403)) {
      return null;
    }

    // A server outage should not look like a logout, so re-throw and let the
    // error boundary explain what happened.
    throw error;
  }
}

/** Use at the top of any protected page. Redirects to /login when signed out. */
export async function requireAdmin(): Promise<AdminUser> {
  const admin = await currentAdmin();

  if (!admin) {
    const reason = await getToken() ? "session-rejected" : "session-missing";
    redirect(`/login?reason=${reason}`);
  }

  return admin;
}

export async function requireSuperAdmin(): Promise<AdminUser> {
  const admin = await requireAdmin();

  if (!admin.is_super_admin) {
    redirect("/dashboard");
  }

  return admin;
}

/** Turns any thrown error into the { ok, message, errors } shape forms expect. */
export function toActionState(error: unknown): ActionState {
  // redirect() signals itself by throwing. Swallowing it here would turn an
  // expired session into a meaningless "something went wrong" message.
  if (isRedirectError(error)) {
    throw error;
  }

  if (error instanceof ApiError) {
    return {
      ok: false,
      message: error.message,
      errors: Object.keys(error.errors).length > 0 ? error.errors : undefined,
    };
  }

  return {
    ok: false,
    message: "Something went wrong. Please try again.",
  };
}

/** Booleans must reach Laravel as "1"/"0", not "on"/absent. */
export function setBool(form: FormData, field: string) {
  form.set(field, form.get(field) ? "1" : "0");
}

/** Drops an empty file input so Laravel's `nullable|image` rule is satisfied. */
export function pruneEmptyFile(form: FormData, field = "image") {
  const value = form.get(field);

  if (value instanceof File && value.size === 0) {
    form.delete(field);
  }
}
