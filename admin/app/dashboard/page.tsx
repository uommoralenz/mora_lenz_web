import Link from "next/link";
import { AdminShell } from "@/components/AdminShell";
import { getDashboardStats, getRecentMessages } from "@/lib/data";
import { formatDate, shortText } from "@/lib/format";
import { requireAdmin } from "@/lib/auth";

export const runtime = "nodejs";

export default async function DashboardPage() {
  const admin = await requireAdmin();
  const [stats, recentMessages] = await Promise.all([getDashboardStats(), getRecentMessages()]);

  return (
    <AdminShell
      admin={admin}
      title="Dashboard"
      description="Overview of the public website content."
    >
      <div className="stats-grid">
        <article className="stat-card">
          <span>Total events</span>
          <strong>{stats.eventsCount}</strong>
        </article>
        <article className="stat-card">
          <span>Upcoming events</span>
          <strong>{stats.upcomingEventsCount}</strong>
        </article>
        <article className="stat-card">
          <span>Members</span>
          <strong>{stats.membersCount}</strong>
        </article>
        <article className="stat-card">
          <span>Panels & pillars</span>
          <strong>{stats.panelsPillarsCount}</strong>
        </article>
        <article className="stat-card">
          <span>Gallery images</span>
          <strong>{stats.galleryImagesCount}</strong>
        </article>
        <article className="stat-card">
          <span>Unread messages</span>
          <strong>{stats.unreadMessagesCount}</strong>
        </article>
        <article className="stat-card">
          <span>Active admins</span>
          <strong>{stats.adminsCount}</strong>
        </article>
      </div>

      <section className="panel">
        <div className="panel-header">
          <h2>Recent Messages</h2>
          <Link href="/messages" className="text-link">
            View all
          </Link>
        </div>

        <div className="list">
          {recentMessages.length > 0 ? (
            recentMessages.map((message) => (
              <article className="list-item" key={message.id}>
                <div>
                  <strong>{message.subject}</strong>
                  <p>{message.name} &lt;{message.email}&gt;</p>
                  <p>{shortText(message.message)}</p>
                </div>
                <time>{formatDate(message.created_at)}</time>
              </article>
            ))
          ) : (
            <p className="empty-state">No messages yet.</p>
          )}
        </div>
      </section>
    </AdminShell>
  );
}
