import Link from "next/link";
import { deletePanelPillarAction } from "@/app/panels-pillars/actions";
import { AdminShell } from "@/components/AdminShell";
import { requireAdmin } from "@/lib/auth";
import { getPanelPillars } from "@/lib/data";
import { shortText } from "@/lib/format";

export const runtime = "nodejs";

export default async function PanelsPillarsPage() {
  const admin = await requireAdmin();
  const panelPillars = await getPanelPillars();

  return (
    <AdminShell
      admin={admin}
      title="Panels & Pillars"
      description="Manage the public panel and pillar cards with member counts."
      action={
        <Link href="/panels-pillars/new" className="button button-primary">
          New Item
        </Link>
      }
    >
      <section className="panel">
        <div className="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Icon</th>
                <th>Members</th>
                <th>Sort</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {panelPillars.length > 0 ? (
                panelPillars.map((item) => (
                  <tr key={item.id}>
                    <td>
                      <div className="table-identity text-only">
                        <div>
                          <strong>{item.name}</strong>
                          <p>{shortText(item.description, 90)}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span className={`status-pill ${item.type}`}>{item.type}</span>
                    </td>
                    <td>{item.icon}</td>
                    <td>{item.member_count}</td>
                    <td>{item.sort_order}</td>
                    <td>
                      <div className="row-actions">
                        <Link href={`/panels-pillars/${item.id}/edit`} className="button button-muted">
                          Edit
                        </Link>
                        <form action={deletePanelPillarAction}>
                          <input type="hidden" name="id" value={item.id} />
                          <button type="submit" className="button button-danger">
                            Delete
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={6} className="empty-table">
                    No panels or pillars yet.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </section>
    </AdminShell>
  );
}
