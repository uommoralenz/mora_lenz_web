import { ImageField } from "@/components/ImageField";
import type { EventRecord } from "@/lib/types";
import { formatDateTimeLocal, formatImageUrlsForTextarea } from "@/lib/format";
import Link from "next/link";

type EventFormProps = {
  action: (formData: FormData) => Promise<void>;
  event?: EventRecord;
  submitLabel: string;
};

export function EventForm({ action, event, submitLabel }: EventFormProps) {
  return (
    <form action={action} className="form-panel" encType="multipart/form-data">
      {event ? <input type="hidden" name="id" value={event.id} /> : null}

      <div className="form-grid">
        <label>
          <span>Title</span>
          <input name="title" defaultValue={event?.title ?? ""} required maxLength={255} />
        </label>

        <label>
          <span>Status</span>
          <select name="status" defaultValue={event?.status ?? "upcoming"}>
            <option value="upcoming">Upcoming</option>
            <option value="past">Past</option>
          </select>
        </label>

        <label>
          <span>Date and time</span>
          <input
            type="datetime-local"
            name="event_date"
            defaultValue={event ? formatDateTimeLocal(event.event_date) : ""}
            required
          />
        </label>

        <label>
          <span>Sort order</span>
          <input type="number" name="sort_order" defaultValue={event?.sort_order ?? 0} min="0" />
        </label>

        <label>
          <span>Location</span>
          <input name="location" defaultValue={event?.location ?? ""} maxLength={255} />
        </label>
      </div>

      <label>
        <span>Description</span>
        <textarea name="description" defaultValue={event?.description ?? ""} rows={5} required />
      </label>

      <ImageField name="image_urls" label="Image URLs" type="events" multiple defaultValue={event ? formatImageUrlsForTextarea(event.image_urls) : ""} />

      <div className="form-actions">
        <Link href="/events" className="button button-muted">
          Cancel
        </Link>
        <button type="submit" className="button button-primary">
          {submitLabel}
        </button>
      </div>
    </form>
  );
}

