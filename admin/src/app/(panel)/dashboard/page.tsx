import Link from "next/link";

import { Card, PageHeader } from "@/components/ui";
import { api, requireAdmin } from "@/lib/api";
import type { MigrationStatus, Permission, Stats } from "@/lib/types";

import DatabaseUpdates from "./database-updates";

export const dynamic = "force-dynamic";

function Stat({
  label,
  value,
  href,
  tone,
  show = true,
}: {
  label: string;
  value: number;
  href: string;
  tone?: "alert";
  show?: boolean;
}) {
  if (!show) return null;

  return (
    <Link
      href={href}
      className="card p-4 transition-colors hover:border-ink-500 hover:bg-ink-800"
    >
      <p className="text-xs uppercase tracking-wide text-slate-500">{label}</p>
      <p
        className={`mt-2 text-3xl font-semibold ${
          tone === "alert" && value > 0 ? "text-amber-400" : "text-slate-100"
        }`}
      >
        {value}
      </p>
    </Link>
  );
}

export default async function DashboardPage() {
  const admin = await requireAdmin();

  const can = (p: Permission) => admin.is_super_admin || admin.permissions.includes(p);

  const { data, featured_event } = await api.get<{
    data: Stats;
    featured_event: string | null;
  }>("/stats");

  // Only a super admin can see or apply these, and a server too old to know
  // the endpoint must not take the dashboard down with it.
  let migrations: MigrationStatus | null = null;

  if (admin.is_super_admin) {
    try {
      const result = await api.get<{ data: MigrationStatus }>("/system/status");
      migrations = result.data;
    } catch {
      migrations = null;
    }
  }

  return (
    <>
      <PageHeader
        title={`Hello, ${admin.name.split(" ")[0]}`}
        description="Everything on the public site is managed from here. Changes go live immediately."
      />

      <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
        <Stat show={can("events")} label="Upcoming events" value={data.events_upcoming} href="/events" />
        <Stat show={can("events")} label="All events" value={data.events} href="/events" />
        <Stat show={can("events")} label="Past events" value={data.events_past ?? 0} href="/events" />
        <Stat show={can("events")} label="Event photos" value={data.event_photos ?? 0} href="/events" />
        <Stat show={can("services")} label="Packages" value={data.service_packages} href="/services" />
        <Stat show={can("team")} label="Team members" value={data.team_members} href="/team" />
        <Stat show={can("team")} label="Team groups" value={data.team_groups} href="/team" />
        <Stat show={can("team")} label="Team years" value={data.team_terms ?? 0} href="/team" />
        <Stat
          show={can("messages")}
          label="Unread messages"
          value={data.messages_unread}
          href="/messages"
          tone="alert"
        />
        {admin.is_super_admin ? (
          <Stat label="Admin accounts" value={data.admins} href="/admins" />
        ) : null}
      </div>

      {migrations ? <DatabaseUpdates status={migrations} /> : null}

      {!admin.is_super_admin && admin.permissions.length === 0 ? (
        <Card>
          <p className="text-sm text-slate-400">
            Your account has not been given access to any section yet. Ask a super admin
            to grant it.
          </p>
        </Card>
      ) : null}

      {can("events") ? (
      <Card className="mt-6">
        <h2 className="text-sm font-semibold text-slate-200">Homepage hero event</h2>
        {featured_event ? (
          <p className="mt-2 text-sm text-slate-400">
            Currently showing{" "}
            <span className="font-medium text-slate-200">{featured_event}</span> in the
            featured slot on the homepage.
          </p>
        ) : (
          <p className="mt-2 text-sm text-slate-400">
            No event is flagged as featured, so the homepage is falling back to the
            soonest upcoming event. Tick <em>Featured</em> on an event to pin one.
          </p>
        )}
        <Link href="/events" className="btn-secondary mt-4">
          Manage events
        </Link>
      </Card>
      ) : null}
    </>
  );
}
