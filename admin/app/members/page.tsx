import Link from "next/link";
import { deleteMemberAction } from "@/app/members/actions";
import { AdminShell } from "@/components/AdminShell";
import { getMembers } from "@/lib/data";
import { displayName, shortText } from "@/lib/format";
import { requireAdmin } from "@/lib/auth";

export const runtime = "nodejs";

export default async function MembersPage() {
  const admin = await requireAdmin();
  const members = await getMembers();

  return (
    <AdminShell
      admin={admin}
      title="Members"
      description="Manage advisors, committee members, panels, and pillars."
      action={
        <Link href="/members/new" className="button button-primary">
          New Member
        </Link>
      }
    >
      <section className="panel">
        <div className="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Member</th>
                <th>Position</th>
                <th>Panel</th>
                <th>Card</th>
                <th>Sort</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {members.map((member) => (
                <tr key={member.id}>
                  <td>
                    <div className="table-identity">
                      {member.photo_url ? <img src={member.photo_url} alt="" /> : <span className="thumb-empty" />}
                      <div>
                        <strong>{displayName(member)}</strong>
                        <p>{shortText(member.bio, 70)}</p>
                      </div>
                    </div>
                  </td>
                  <td>{member.position}</td>
                  <td>{member.pillar_or_panel ?? "-"}</td>
                  <td>{member.card_size}</td>
                  <td>{member.sort_order}</td>
                  <td>
                    <div className="row-actions">
                      <Link href={`/members/${member.id}/edit`} className="button button-muted">
                        Edit
                      </Link>
                      <form action={deleteMemberAction}>
                        <input type="hidden" name="id" value={member.id} />
                        <button type="submit" className="button button-danger">
                          Delete
                        </button>
                      </form>
                    </div>
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
