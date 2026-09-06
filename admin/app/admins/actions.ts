"use server";

import bcrypt from "bcryptjs";
import crypto from "node:crypto";
import { execute, queryRows } from "@/lib/db";
import { requireSuperAdmin, revokeAdminSessions } from "@/lib/auth";
import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";

function adminPayload(formData: FormData) {
  const role = String(formData.get("role") ?? "admin");

  return {
    name: String(formData.get("name") ?? "").trim(),
    email: String(formData.get("email") ?? "").trim().toLowerCase(),
    password: String(formData.get("password") ?? ""),
    role: role === "super_admin" ? "super_admin" : "admin",
  };
}

export async function createAdminAction(formData: FormData) {
  await requireSuperAdmin();

  const payload = adminPayload(formData);

  if (!payload.name || payload.name.length > 255 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.email) || payload.email.length > 255 || payload.password.length < 10 || Buffer.byteLength(payload.password, "utf8") > 72) {
    throw new Error("A name, valid email, and password of at least 10 characters (at most 72 UTF-8 bytes) are required.");
  }

  const passwordHash = await bcrypt.hash(payload.password, 12);

  await execute(
    `
      INSERT INTO admin_users
        (id, name, email, password_hash, role, is_active, created_at, updated_at)
      VALUES
        (?, ?, ?, ?, ?, 1, NOW(), NOW())
    `,
    [crypto.randomUUID(), payload.name, payload.email, passwordHash, payload.role],
  );

  revalidatePath("/admins");
  revalidatePath("/dashboard");
  redirect("/admins");
}

export async function toggleAdminActiveAction(formData: FormData) {
  const currentAdmin = await requireSuperAdmin();
  const id = String(formData.get("id") ?? "");
  const isActive = String(formData.get("is_active") ?? "0") === "1";

  if (!id || id === currentAdmin.id) {
    redirect("/admins");
  }

  if (!isActive && await isLastActiveSuperAdmin(id)) {
    redirect("/admins?error=last-super-admin");
  }

  await execute("UPDATE admin_users SET is_active = ?, updated_at = NOW() WHERE id = ?", [isActive, id]);
  await revokeAdminSessions(id);

  revalidatePath("/admins");
  revalidatePath("/dashboard");
  redirect("/admins");
}

export async function deleteAdminAction(formData: FormData) {
  const currentAdmin = await requireSuperAdmin();
  const id = String(formData.get("id") ?? "");

  if (!id || id === currentAdmin.id) {
    redirect("/admins");
  }

  if (await isLastActiveSuperAdmin(id)) {
    redirect("/admins?error=last-super-admin");
  }

  await execute("DELETE FROM admin_users WHERE id = ?", [id]);

  revalidatePath("/admins");
  revalidatePath("/dashboard");
  redirect("/admins");
}

async function isLastActiveSuperAdmin(id: string) {
  const [target] = await queryRows<{ role: string; is_active: number }>(
    "SELECT role, is_active FROM admin_users WHERE id = ? LIMIT 1",
    [id],
  );

  if (!target || target.role !== "super_admin" || !target.is_active) {
    return false;
  }

  const [row] = await queryRows<{ count: number }>(
    "SELECT COUNT(*) AS count FROM admin_users WHERE role = 'super_admin' AND is_active = 1",
  );

  return (row?.count ?? 0) <= 1;
}
