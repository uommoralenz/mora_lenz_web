"use client";

import { useActionState, useRef, useState } from "react";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { Thumb, toLocalInput } from "@/components/ui";
import { EMPTY_ACTION_STATE, type EventItem } from "@/lib/types";

import BlockEditor from "./block-editor";
import { previewEventAction, saveEventAction } from "./actions";

export default function EventForm({ event }: { event?: EventItem }) {
  const [state, formAction] = useActionState(saveEventAction, EMPTY_ACTION_STATE);
  const formRef = useRef<HTMLFormElement>(null);
  const [previewing, setPreviewing] = useState(false);
  const [previewError, setPreviewError] = useState("");

  async function openPreview() {
    if (!formRef.current) return;

    // Open the tab now, inside the click, or the browser blocks it as a popup.
    const tab = window.open("", "_blank");
    setPreviewing(true);
    setPreviewError("");

    const data = new FormData(formRef.current);
    if (event) data.set("event_id", String(event.id));
    data.delete("image");

    try {
      const result = await previewEventAction(data);

      if (result.url && tab) {
        tab.location.href = result.url;
      } else {
        tab?.close();
        setPreviewError(result.message || "Could not create the preview.");
      }
    } catch {
      tab?.close();
      setPreviewError("Could not create the preview. Please try again.");
    } finally {
      setPreviewing(false);
    }
  }

  return (
    <form ref={formRef} action={formAction} className="space-y-5">
      {event ? <input type="hidden" name="id" value={event.id} /> : null}

      <FormMessage state={state} />

      <div>
        <label className="label" htmlFor="title">
          Title
        </label>
        <input
          id="title"
          name="title"
          className="input"
          defaultValue={event?.title ?? ""}
          maxLength={200}
          required
        />
        <FieldError errors={state.errors} name="title" />
      </div>

      <div>
        <label className="label" htmlFor="slug">
          URL slug
        </label>
        <input
          id="slug"
          name="slug"
          className="input"
          defaultValue={event?.slug ?? ""}
          placeholder="leave blank to generate from the title"
          maxLength={200}
        />
        <p className="hint">
          The address of the page: /events/<span className="text-slate-400">your-slug</span>.
          Changing it breaks any link already shared.
        </p>
        <FieldError errors={state.errors} name="slug" />
      </div>

      <div>
        <label className="label" htmlFor="description">
          Intro text
        </label>
        <textarea
          id="description"
          name="description"
          className="textarea min-h-40"
          defaultValue={event?.description ?? ""}
        />
        <p className="hint">
          Shown first on the page and in search results. Leave a blank line between paragraphs.
        </p>
        <FieldError errors={state.errors} name="description" />
      </div>

      <div>
        <span className="label">Page content</span>
        <p className="hint mb-3 mt-0">
          Build the rest of the page like a blog post. Buttons can link to WhatsApp,
          Facebook, Google Forms, Drive or any website.
        </p>
        <BlockEditor initial={event?.content ?? []} />
        <FieldError errors={state.errors} name="content" />
      </div>

      <div className="grid gap-4 sm:grid-cols-2">
        <div>
          <label className="label" htmlFor="event_date">
            Starts
          </label>
          <input
            id="event_date"
            name="event_date"
            type="datetime-local"
            className="input"
            defaultValue={toLocalInput(event?.event_date)}
            required
          />
          <FieldError errors={state.errors} name="event_date" />
        </div>

        <div>
          <label className="label" htmlFor="end_date">
            Ends <span className="font-normal text-slate-500">(optional)</span>
          </label>
          <input
            id="end_date"
            name="end_date"
            type="datetime-local"
            className="input"
            defaultValue={toLocalInput(event?.end_date)}
          />
          <p className="hint">When set, the countdown targets this instead.</p>
          <FieldError errors={state.errors} name="end_date" />
        </div>
      </div>

      <div>
        <label className="label" htmlFor="location">
          Location
        </label>
        <input
          id="location"
          name="location"
          className="input"
          defaultValue={event?.location ?? ""}
          maxLength={200}
        />
        <FieldError errors={state.errors} name="location" />
      </div>

      <div>
        <label className="label" htmlFor="image">
          Cover image
        </label>
        {event?.image_url ? (
          <div className="mb-2 flex items-center gap-3">
            <Thumb src={event.image_url} alt={event.title} className="h-20 w-28" />
            <p className="text-xs text-slate-500">
              Current image. Choose a new file to replace it.
            </p>
          </div>
        ) : null}
        <input
          id="image"
          name="image"
          type="file"
          accept="image/*"
          className="input file:mr-3 file:rounded file:border-0 file:bg-ink-700 file:px-3 file:py-1 file:text-slate-200"
        />
        <p className="hint">JPG, PNG or WebP. Landscape works best. Max 8 MB.</p>
        <FieldError errors={state.errors} name="image" />
      </div>

      <fieldset className="space-y-3 rounded-lg border border-ink-700 p-4">
        <legend className="px-1 text-xs uppercase tracking-wide text-slate-500">
          Display
        </legend>

        <label className="flex items-start gap-3 text-sm">
          <input
            type="checkbox"
            name="is_active"
            value="1"
            defaultChecked={event ? event.is_active : true}
            className="mt-0.5"
          />
          <span>
            <span className="font-medium text-slate-200">Visible on the site</span>
            <span className="block text-xs text-slate-500">
              Untick to hide it without deleting it.
            </span>
          </span>
        </label>

        <label className="flex items-start gap-3 text-sm">
          <input
            type="checkbox"
            name="is_featured"
            value="1"
            defaultChecked={event?.is_featured ?? false}
            className="mt-0.5"
          />
          <span>
            <span className="font-medium text-slate-200">Featured on the homepage</span>
            <span className="block text-xs text-slate-500">
              Only one event can be featured — ticking this unticks the others.
            </span>
          </span>
        </label>

        <label className="flex items-start gap-3 text-sm">
          <input
            type="checkbox"
            name="countdown_enabled"
            value="1"
            defaultChecked={event?.countdown_enabled ?? false}
            className="mt-0.5"
          />
          <span>
            <span className="font-medium text-slate-200">Show a countdown timer</span>
            <span className="block text-xs text-slate-500">
              Hidden automatically once the date has passed.
            </span>
          </span>
        </label>
      </fieldset>

      <div className="flex flex-wrap items-center gap-3">
        <SubmitButton>{event ? "Save changes" : "Create event"}</SubmitButton>
        <button
          type="button"
          className="btn-secondary"
          onClick={openPreview}
          disabled={previewing}
        >
          {previewing ? "Preparing preview…" : "Preview page"}
        </button>
        <span className="text-xs text-slate-500">
          Preview shows your unsaved changes (the cover image is the saved one).
        </span>
      </div>
      {previewError ? <p className="error-text">{previewError}</p> : null}
    </form>
  );
}
