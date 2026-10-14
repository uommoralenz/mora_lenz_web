import { notFound } from "next/navigation";

import { BackLink, Card, PageHeader } from "@/components/ui";
import { ApiError, api } from "@/lib/api";
import type { EventItem, EventPhoto } from "@/lib/types";

import EventForm from "../event-form";
import PhotoAlbum from "../photo-album";

export const dynamic = "force-dynamic";

export default async function EditEventPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;

  let event: EventItem;

  try {
    const result = await api.get<{ data: EventItem }>(`/events/${id}`);
    event = result.data;
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) {
      notFound();
    }
    throw error;
  }

  const { data: photos } = await api.get<{ data: EventPhoto[] }>(`/events/${event.id}/photos`);

  return (
    <>
      <BackLink href="/events">Back to events</BackLink>
      <div className="mt-3">
        <PageHeader title="Edit event" description={event.title} />
      </div>
      <Card>
        <EventForm event={event} />
      </Card>

      <Card className="mt-6">
        <h2 id="photos" className="scroll-mt-6 text-sm font-semibold text-slate-200">Photo album</h2>
        <p className="mb-4 mt-1 text-xs text-slate-500">
          {event.is_past
            ? "This event is over, so it is listed under Past Events with these photos."
            : "Shown on the event page now, and under Past Events once the event is over."}
        </p>
        <PhotoAlbum eventId={event.id} initial={photos} />
      </Card>
    </>
  );
}
