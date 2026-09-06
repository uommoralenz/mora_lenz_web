import Link from "next/link";
import { deleteAdminAction, toggleAdminActiveAction } from "@/app/admins/actions";
import { AdminShell } from "@/components/AdminShell";
import { getAdminUsers } from "@/lib/data";
import { formatDate } from "@/lib/format";
import { requireSuperAdmin } from "@/lib/auth";

export const runtime = "nodejs";

type AdminsPageProps = {
  searchParams: Promise<{
    error?: string;
  }>;
};

export default async function AdminsPage({ searchParams }: AdminsPageProps) {
  const [admin, admins, params] = await Promise.all([requireSuperAdmin(), getAdminUsers(), searchParams]);

  return (
    <AdminShell
      admin={admin}
      title="Admins"
      description="Only super admins can create and manage admin accounts."
      action={
        <Link href="/admins/new" className="button button-primary">
          New Admin
        </Link>
      }
    >
      {params.error === "last-super-admin" ? (
        <div className="alert alert-error">You must keep at least one active super admin.</div>
      ) : null}

      <section className="panel">
        <div className="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last login</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {admins.map((item) => (
                <tr key={item.id}>
                  <td><strong>{item.name}</strong></td>
                  <td>{item.email}</td>
                  <td><span className="status-pill">{item.role.replace("_", " ")}</span></td>
                  <td>
                    <span className={item.is_active ? "status-pill unread" : "status-pill read"}>
                      {item.is_active ? "active" : "disabled"}
                    </span>
                  </td>
                  <td>{formatDate(item.last_login_at)}</td>
                  <td>
                    {item.id === admin.id ? (
                      <span className="muted-text">Current account</span>
                    ) : (
                      <div className="row-actions">
                        <form action={toggleAdminActiveAction}>
                          <input type="hidden" name="id" value={item.id} />
                          <input type="hidden" name="is_active" value={item.is_active ? "0" : "1"} />
                          <button type="submit" className="button button-muted">
                            {item.is_active ? "Disable" : "Enable"}
                          </button>
                        </form>
                        <form action={deleteAdminAction}>
                          <input type="hidden" name="id" value={item.id} />
                          <button type="submit" className="button button-danger">
                            Delete
                          </button>
                        </form>
                      </div>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>
    </AdminShell>
  );
}
