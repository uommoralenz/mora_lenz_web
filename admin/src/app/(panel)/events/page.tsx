import Link from "next/link";

import {
  Badge,
  EmptyState,
  PageHeader,
  SectionTitle,
  Thumb,
  formatDateTime,
} from "@/components/ui";
import { api } from "@/lib/api";
import type { EventItem } from "@/lib/types";

import { reorderEventsAction } from "./actions";
import DeleteEventForm from "./delete-event-form";

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

/** One list of events. Past events are listed by date, so they have no arrows. */
function EventList({ events, reorder }: { events: EventItem[]; reorder: boolean }) {
  return (
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
                  {event.photo_count ? (
                  <Badge>
                    {event.photo_count} photo{event.photo_count === 1 ? "" : "s"}
                  </Badge>
                ) : null}
                {event.facebook_album_urls?.length ? <Badge>Facebook</Badge> : null}
                {!event.is_active ? <Badge tone="danger">Hidden</Badge> : null}
              </div>
              <p className="mt-1 truncate text-xs text-slate-500">
                {formatDateTime(event.event_date)}
                {event.location ? ` · ${event.location}` : ""}
              </p>
              <p className="truncate text-xs text-slate-600">/events/{event.slug}</p>
            </div>

            <div className="flex items-center gap-1">
              {reorder ? (
                <>
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

                </>
              ) : null}

              <Link href={`/events/${event.id}`} className="btn-secondary">
                Edit
              </Link>

              <DeleteEventForm id={event.id} title={event.title} />
            </div>
          </li>
        );
      })}
    </ul>
  );
}

export default async function EventsPage() {
  const { data: events } = await api.get<{ data: EventItem[] }>("/events");

  const upcoming = events.filter((e) => !e.is_past);
  // Newest first, the same order as on the site.
  const past = events
    .filter((e) => e.is_past)
    .sort((a, b) => b.event_date.localeCompare(a.event_date));

  return (
    <>
      <PageHeader
        title="Events"
        description="These appear on /events. Once an event is over it moves to Past Events with its photo album and Facebook links. The one marked Featured fills the homepage hero."
        action={
          <div className="flex flex-wrap gap-2">
            <Link href="/events/new?past=1" className="btn-secondary">
              Add past event
            </Link>
            <Link href="/events/new" className="btn-primary">
              New event
            </Link>
          </div>
        }
      />

      {events.length === 0 ? (
        <EmptyState
          title="No events yet."
          hint="Create one and it will show up on the public site straight away."
        />
      ) : (
        <div className="space-y-8">
          <section>
            <SectionTitle>Upcoming ({upcoming.length})</SectionTitle>
            {upcoming.length === 0 ? (
              <p className="text-sm text-slate-500">No upcoming events.</p>
            ) : (
              <EventList events={upcoming} reorder />
            )}
          </section>

          <section>
            <SectionTitle>Past events ({past.length})</SectionTitle>
            {past.length === 0 ? (
              <p className="text-sm text-slate-500">
                No past events yet. Use <strong>Add past event</strong> to add one with its photos.
              </p>
            ) : (
              <EventList events={past} reorder={false} />
            )}
          </section>
        </div>
      )}
    </>
  );
}
