import { updateEventAction } from "@/app/events/actions";
import { AdminShell } from "@/components/AdminShell";
import { EventForm } from "@/components/EventForm";
import { getEvent } from "@/lib/data";
import { requireAdmin } from "@/lib/auth";
import { notFound } from "next/navigation";

export const runtime = "nodejs";

type EditEventPageProps = {
  params: Promise<{
    id: string;
  }>;
};

export default async function EditEventPage({ params }: EditEventPageProps) {
  const admin = await requireAdmin();
  const { id } = await params;
  const event = await getEvent(id);

  if (!event) {
    notFound();
  }

  return (
    <AdminShell admin={admin} title="Edit Event" description="Update event details shown on the PHP website.">
      <EventForm action={updateEventAction} event={event} submitLabel="Save Event" />
    </AdminShell>
  );
}
