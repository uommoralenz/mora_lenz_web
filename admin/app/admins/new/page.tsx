import Link from "next/link";
import { createAdminAction } from "@/app/admins/actions";
import { AdminShell } from "@/components/AdminShell";
import { requireSuperAdmin } from "@/lib/auth";

export const runtime = "nodejs";

export default async function NewAdminPage() {
  const admin = await requireSuperAdmin();

  return (
    <AdminShell admin={admin} title="New Admin" description="Create a database-backed admin account with a hashed password.">
      <form action={createAdminAction} className="form-panel">
        <div className="form-grid">
          <label>
            <span>Name</span>
            <input name="name" required />
          </label>

          <label>
            <span>Email</span>
            <input type="email" name="email" required />
          </label>

          <label>
            <span>Role</span>
            <select name="role" defaultValue="admin">
              <option value="admin">Admin</option>
              <option value="super_admin">Super admin</option>
            </select>
          </label>

          <label>
            <span>Password</span>
            <input type="password" name="password" minLength={10} required />
          </label>
        </div>

        <p className="hint">
          Passwords are hashed before saving. They are never stored in `.env`.
        </p>

        <div className="form-actions">
          <Link href="/admins" className="button button-muted">
            Cancel
          </Link>
          <button type="submit" className="button button-primary">
            Create Admin
          </button>
        </div>
      </form>
    </AdminShell>
  );
}
