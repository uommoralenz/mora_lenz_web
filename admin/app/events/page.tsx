import Link from "next/link";
import { deleteEventAction } from "@/app/events/actions";
import { AdminShell } from "@/components/AdminShell";
import { getEvents } from "@/lib/data";
import { coverImage, formatDateOnly, shortText } from "@/lib/format";
import { requireAdmin } from "@/lib/auth";

export const runtime = "nodejs";

export default async function EventsPage() {
  const admin = await requireAdmin();
  const events = await getEvents();

  return (
    <AdminShell
      admin={admin}
      title="Events"
      description="Create and update upcoming or past event listings."
      action={
        <Link href="/events/new" className="button button-primary">
          New Event
        </Link>
      }
    >
      <section className="panel">
        <div className="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Event</th>
                <th>Status</th>
                <th>Date</th>
                <th>Sort</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {events.map((event) => (
                <tr key={event.id}>
                  <td>
                    <div className="table-identity">
                      {coverImage(event) ? <img src={coverImage(event)} alt="" /> : <span className="thumb-empty" />}
                      <div>
                        <strong>{event.title}</strong>
                        <p>{shortText(event.description, 80)}</p>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span className={`status-pill ${event.status}`}>{event.status}</span>
                  </td>
                  <td>{formatDateOnly(event.event_date)}</td>
                  <td>{event.sort_order}</td>
                  <td>
                    <div className="row-actions">
                      <Link href={`/events/${event.id}/edit`} className="button button-muted">
                        Edit
                      </Link>
                      <form action={deleteEventAction}>
                        <input type="hidden" name="id" value={event.id} />
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
