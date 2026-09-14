import Link from "next/link";

import { Card, PageHeader } from "@/components/ui";
import { api, requireAdmin } from "@/lib/api";
import type { Stats } from "@/lib/types";

export const dynamic = "force-dynamic";

function Stat({
  label,
  value,
  href,
  tone,
}: {
  label: string;
  value: number;
  href: string;
  tone?: "alert";
}) {
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

  const { data, featured_event } = await api.get<{
    data: Stats;
    featured_event: string | null;
  }>("/stats");

  return (
    <>
      <PageHeader
        title={`Hello, ${admin.name.split(" ")[0]}`}
        description="Everything on the public site is managed from here. Changes go live immediately."
      />

      <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
        <Stat label="Upcoming events" value={data.events_upcoming} href="/events" />
        <Stat label="All events" value={data.events} href="/events" />
        <Stat label="Galleries" value={data.galleries} href="/gallery" />
        <Stat label="Packages" value={data.service_packages} href="/services" />
        <Stat label="Team members" value={data.team_members} href="/team" />
        <Stat label="Team groups" value={data.team_groups} href="/team" />
        <Stat
          label="Unread messages"
          value={data.messages_unread}
          href="/messages"
          tone="alert"
        />
        {admin.is_super_admin ? (
          <Stat label="Admin accounts" value={data.admins} href="/admins" />
        ) : null}
      </div>

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
    </>
  );
}
