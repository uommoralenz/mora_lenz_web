import "server-only";
import bcrypt from "bcryptjs";
import crypto from "node:crypto";
import { cookies } from "next/headers";
import { execute, queryRows } from "@/lib/db";
import { redirect } from "next/navigation";
import type { AdminSession, AdminUserRecord } from "@/lib/types";

const COOKIE_NAME = "moralenz_admin_session";
const SESSION_MAX_AGE = 60 * 60 * 8;

function tokenHash(token: string) {
  const secret = process.env.SESSION_SECRET;
  if (!secret || secret.length < 32 || /replace|change-before|generate-a-long/i.test(secret)) {
    throw new Error("SESSION_SECRET must be a random secret of at least 32 characters.");
  }
  return crypto.createHmac("sha256", secret).update(token).digest("hex");
}

function passwordVersion(hash: string) {
  return crypto.createHash("sha256").update(hash).digest("hex");
}

function sessionAdmin(admin: AdminUserRecord): AdminSession {
  return { id: admin.id, name: admin.name, email: admin.email, role: admin.role };
}

export async function verifyAdminLogin(email: string, password: string) {
  if (!email || email.length > 255 || !password || Buffer.byteLength(password, "utf8") > 72) return null;
  const [admin] = await queryRows<AdminUserRecord>(
    "SELECT * FROM admin_users WHERE email = ? LIMIT 1", [email.toLowerCase()],
  );
  if (!admin || !admin.is_active) return null;
  const hash = admin.password_hash.replace(/^\$2y\$/, "$2b$");
  if (!await bcrypt.compare(password, hash)) return null;
  await execute("UPDATE admin_users SET last_login_at = NOW(), updated_at = NOW() WHERE id = ?", [admin.id]);
  return sessionAdmin(admin);
}

export async function createSession(admin: AdminSession) {
  const [current] = await queryRows<AdminUserRecord>("SELECT * FROM admin_users WHERE id = ? AND is_active = 1", [admin.id]);
  if (!current) throw new Error("Admin account is not active.");
  const token = crypto.randomBytes(32).toString("base64url");
  const hash = tokenHash(token);
  await destroySession();
  await execute("DELETE FROM admin_sessions WHERE expires_at <= ?", [Date.now()]);
  await execute(
    "INSERT INTO admin_sessions (token_hash, admin_id, password_version, expires_at) VALUES (?, ?, ?, ?)",
    [hash, admin.id, passwordVersion(current.password_hash), Date.now() + SESSION_MAX_AGE * 1000],
  );
  (await cookies()).set(COOKIE_NAME, token, {
    httpOnly: true, maxAge: SESSION_MAX_AGE, path: "/", sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
  });
}

export async function destroySession() {
  const cookieStore = await cookies();
  const token = cookieStore.get(COOKIE_NAME)?.value;
  if (token && /^[A-Za-z0-9_-]{43}$/.test(token)) {
    await execute("DELETE FROM admin_sessions WHERE token_hash = ?", [tokenHash(token)]);
  }
  cookieStore.delete(COOKIE_NAME);
}

export async function revokeAdminSessions(adminId: string) {
  await execute("DELETE FROM admin_sessions WHERE admin_id = ?", [adminId]);
}

export async function getCurrentAdmin() {
  const token = (await cookies()).get(COOKIE_NAME)?.value;
  if (!token || !/^[A-Za-z0-9_-]{43}$/.test(token)) return null;
  const [admin] = await queryRows<AdminUserRecord & { password_version: string }>(
    `SELECT a.*, s.password_version FROM admin_sessions s
     JOIN admin_users a ON a.id = s.admin_id
     WHERE s.token_hash = ? AND s.expires_at > ? AND a.is_active = 1 LIMIT 1`,
    [tokenHash(token), Date.now()],
  );
  if (!admin || admin.password_version !== passwordVersion(admin.password_hash)) return null;
  return sessionAdmin(admin);
}

export async function requireAdmin() {
  const admin = await getCurrentAdmin();
  if (!admin) redirect("/login");
  return admin;
}

export async function requireSuperAdmin() {
  const admin = await requireAdmin();
  if (admin.role !== "super_admin") redirect("/dashboard");
  return admin;
}
