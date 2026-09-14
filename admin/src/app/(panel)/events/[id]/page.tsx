import { notFound } from "next/navigation";

import { BackLink, Card, PageHeader } from "@/components/ui";
import { ApiError, api } from "@/lib/api";
import type { EventItem } from "@/lib/types";

import EventForm from "../event-form";

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

  return (
    <>
      <BackLink href="/events">Back to events</BackLink>
      <div className="mt-3">
        <PageHeader title="Edit event" description={event.title} />
      </div>
      <Card>
        <EventForm event={event} />
      </Card>
    </>
  );
}
