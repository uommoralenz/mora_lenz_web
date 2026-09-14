import Link from "next/link";

import { ConfirmSubmit } from "@/components/form";
import {
  Badge,
  EmptyState,
  PageHeader,
  Thumb,
  formatDateTime,
} from "@/components/ui";
import { api } from "@/lib/api";
import type { EventItem } from "@/lib/types";

import { deleteEventAction, reorderEventsAction } from "./actions";

export const dynamic = "force-dynamic";

/** Swap this row's sort_order with its neighbour's, then persist both. */
function swapPayload(events: EventItem[], index: number, delta: number) {
  const other = index + delta;

  if (other < 0 || other >= events.length) return null;

  return JSON.stringify([
    { id: events[index].id, sort_order: other },
    { id: events[other].id, sort_order: index },
  ]);
}

export default async function EventsPage() {
  const { data: events } = await api.get<{ data: EventItem[] }>("/events");

  return (
    <>
      <PageHeader
        title="Events"
        description="These appear on /events. The one marked Featured fills the homepage hero."
        action={
          <Link href="/events/new" className="btn-primary">
            New event
          </Link>
        }
      />

      {events.length === 0 ? (
        <EmptyState
          title="No events yet."
          hint="Create one and it will show up on the public site straight away."
        />
      ) : (
        <ul className="space-y-3">
          {events.map((event, index) => {
            const up = swapPayload(events, index, -1);
            const down = swapPayload(events, index, 1);

            return (
              <li key={event.id} className="card flex flex-wrap items-center gap-4 p-4">
                <Thumb src={event.image_url} alt={event.title} />

                <div className="min-w-0 flex-1">
                  <div className="flex flex-wrap items-center gap-2">
                    <p className="truncate font-medium text-slate-100">{event.title}</p>
                    {event.is_featured ? <Badge tone="info">Featured</Badge> : null}
                    {event.countdown_enabled ? <Badge>Countdown</Badge> : null}
                    {!event.is_active ? <Badge tone="danger">Hidden</Badge> : null}
                  </div>
                  <p className="mt-1 truncate text-xs text-slate-500">
                    {formatDateTime(event.event_date)}
                    {event.location ? ` · ${event.location}` : ""}
                  </p>
                  <p className="truncate text-xs text-slate-600">/events/{event.slug}</p>
                </div>

                <div className="flex items-center gap-1">
                  <form action={reorderEventsAction}>
                    <input type="hidden" name="items" value={up ?? ""} />
                    <button
                      type="submit"
                      className="btn-ghost"
                      disabled={!up}
                      aria-label="Move up"
                      title="Move up"
                    >
                      ↑
                    </button>
                  </form>

                  <form action={reorderEventsAction}>
                    <input type="hidden" name="items" value={down ?? ""} />
                    <button
                      type="submit"
                      className="btn-ghost"
                      disabled={!down}
                      aria-label="Move down"
                      title="Move down"
                    >
                      ↓
                    </button>
                  </form>

                  <Link href={`/events/${event.id}`} className="btn-secondary">
                    Edit
                  </Link>

                  <form action={deleteEventAction}>
                    <input type="hidden" name="id" value={event.id} />
                    <ConfirmSubmit
                      confirm={`Delete "${event.title}"? This also removes its image and cannot be undone.`}
                    >
                      Delete
                    </ConfirmSubmit>
                  </form>
                </div>
              </li>
            );
          })}
        </ul>
      )}
    </>
  );
}
