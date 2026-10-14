"use client";

import { useRef, useState, useTransition } from "react";

import { Thumb } from "@/components/ui";
import type { EventPhoto } from "@/lib/types";

import {
  deleteEventPhotoAction,
  saveEventPhotoCaptionAction,
  uploadEventPhotosAction,
} from "./actions";

/**
 * Uploads go through a server action, whose body is capped (next.config.mjs),
 * so a big selection is sent in batches that each stay under the cap.
 */
const BATCH_BYTES = 9 * 1024 * 1024;
const BATCH_FILES = 20;
const MAX_FILE_BYTES = 8 * 1024 * 1024;

function batches(files: File[]): File[][] {
  const out: File[][] = [];
  let current: File[] = [];
  let size = 0;

  for (const file of files) {
    if (current.length > 0 && (size + file.size > BATCH_BYTES || current.length >= BATCH_FILES)) {
      out.push(current);
      current = [];
      size = 0;
    }
    current.push(file);
    size += file.size;
  }

  if (current.length > 0) out.push(current);

  return out;
}

export default function PhotoAlbum({
  eventId,
  initial,
}: {
  eventId: number;
  initial: EventPhoto[];
}) {
  const inputRef = useRef<HTMLInputElement>(null);
  const [photos, setPhotos] = useState(initial);
  const [progress, setProgress] = useState("");
  const [message, setMessage] = useState<{ ok: boolean; text: string } | null>(null);
  const [uploading, setUploading] = useState(false);

  async function upload() {
    const chosen = Array.from(inputRef.current?.files ?? []);

    if (chosen.length === 0) {
      setMessage({ ok: false, text: "Choose at least one photo." });
      return;
    }

    const tooBig = chosen.filter((file) => file.size > MAX_FILE_BYTES);
    const files = chosen.filter((file) => file.size <= MAX_FILE_BYTES);
    const groups = batches(files);

    setUploading(true);
    setMessage(null);

    let added = 0;
    let error = "";

    for (let i = 0; i < groups.length; i++) {
      setProgress(`Uploading batch ${i + 1} of ${groups.length}…`);

      const form = new FormData();
      form.set("event_id", String(eventId));
      groups[i].forEach((file) => form.append("images[]", file));

      try {
        const result = await uploadEventPhotosAction(form);

        if (!result.ok) {
          error = result.message;
          break;
        }

        added += groups[i].length;
        if (result.photos) setPhotos(result.photos);
      } catch {
        error = "The upload failed. Please try again.";
        break;
      }
    }

    setUploading(false);
    setProgress("");
    if (inputRef.current) inputRef.current.value = "";

    const skipped = tooBig.length
      ? ` ${tooBig.length} file${tooBig.length === 1 ? " was" : "s were"} over 8 MB and skipped.`
      : "";

    if (error) {
      setMessage({ ok: false, text: `${added ? `${added} added, then: ` : ""}${error}${skipped}` });
    } else {
      setMessage({ ok: added > 0, text: `${added} photo${added === 1 ? "" : "s"} added.${skipped}` });
    }
  }

  return (
    <div className="space-y-4">
      <div className="space-y-2">
        <input
          ref={inputRef}
          type="file"
          accept="image/*"
          multiple
          disabled={uploading}
          className="input file:mr-3 file:rounded file:border-0 file:bg-ink-700 file:px-3 file:py-1 file:text-slate-200"
        />
        <div className="flex flex-wrap items-center gap-3">
          <button type="button" className="btn-primary" onClick={upload} disabled={uploading}>
            {uploading ? "Uploading…" : "Add photos"}
          </button>
          {progress ? <span className="text-xs text-slate-400">{progress}</span> : null}
        </div>
        <p className="hint">
          Select as many photos as you like; they are uploaded in batches. JPG, PNG or WebP,
          max 8 MB each. For the full set, link the Facebook album above.
        </p>
        {message ? (
          <p className={message.ok ? "text-sm text-emerald-400" : "error-text"}>{message.text}</p>
        ) : null}
      </div>

      {photos.length === 0 ? (
        <p className="text-sm text-slate-500">No photos in this album yet.</p>
      ) : (
        <>
          <p className="label">
            {photos.length} photo{photos.length === 1 ? "" : "s"}
          </p>
          <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
            {photos.map((photo) => (
              <PhotoTile
                key={photo.id}
                eventId={eventId}
                photo={photo}
                onRemoved={() => setPhotos((list) => list.filter((p) => p.id !== photo.id))}
              />
            ))}
          </div>
        </>
      )}
    </div>
  );
}

function PhotoTile({
  eventId,
  photo,
  onRemoved,
}: {
  eventId: number;
  photo: EventPhoto;
  onRemoved: () => void;
}) {
  const [caption, setCaption] = useState(photo.caption ?? "");
  const [saved, setSaved] = useState(photo.caption ?? "");
  const [error, setError] = useState("");
  const [pending, startTransition] = useTransition();

  const saveCaption = () => {
    if (caption.trim() === saved.trim()) return;

    startTransition(async () => {
      const result = await saveEventPhotoCaptionAction(eventId, photo.id, caption);
      if (result.ok) {
        setSaved(caption);
        setError("");
      } else {
        setError(result.message);
      }
    });
  };

  const remove = () => {
    if (!window.confirm("Remove this photo from the album? This cannot be undone.")) return;

    startTransition(async () => {
      const result = await deleteEventPhotoAction(eventId, photo.id);
      if (result.ok) onRemoved();
      else setError(result.message);
    });
  };

  return (
    <div className={`space-y-1 ${pending ? "opacity-60" : ""}`}>
      <Thumb src={photo.image_url} alt={caption} className="h-24 w-full" />
      <input
        className="input px-2 py-1 text-xs"
        placeholder="Caption (optional)"
        maxLength={300}
        value={caption}
        onChange={(e) => setCaption(e.target.value)}
        onBlur={saveCaption}
        onKeyDown={(e) => {
          if (e.key === "Enter") {
            e.preventDefault();
            saveCaption();
          }
        }}
      />
      <button
        type="button"
        className="btn-danger w-full px-2 py-1 text-xs"
        onClick={remove}
        disabled={pending}
      >
        Remove
      </button>
      {error ? <p className="error-text text-xs">{error}</p> : null}
    </div>
  );
}
