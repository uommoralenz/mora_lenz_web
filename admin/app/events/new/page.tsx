import { createEventAction } from "@/app/events/actions";
import { AdminShell } from "@/components/AdminShell";
import { EventForm } from "@/components/EventForm";
import { requireAdmin } from "@/lib/auth";

export const runtime = "nodejs";

export default async function NewEventPage() {
  const admin = await requireAdmin();

  return (
    <AdminShell admin={admin} title="New Event" description="Add a new event to the public website.">
      <EventForm action={createEventAction} submitLabel="Create Event" />
    </AdminShell>
  );
}
