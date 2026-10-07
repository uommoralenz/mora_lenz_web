"use client";

import { useActionState, useRef, useState } from "react";
import { useRouter } from "next/navigation";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { Thumb } from "@/components/ui";
import { shrinkImage } from "@/lib/shrink-image";
import { EMPTY_ACTION_STATE, type GalleryItem } from "@/lib/types";

import { removeGalleryPhotoAction, saveGalleryAction, uploadGalleryPhotoAction } from "./actions";

const MAX_PHOTOS = 5;
const MAX_BYTES = 2 * 1024 * 1024;

export default function GalleryForm({
  item,
  onDone,
}: {
  item?: GalleryItem;
  onDone?: () => void;
}) {
  const formRef = useRef<HTMLFormElement>(null);
  const [photoCount, setPhotoCount] = useState(0);
  const [progress, setProgress] = useState("");
  const router = useRouter();
  const [removing, setRemoving] = useState<number | null>(null);
  const [removeError, setRemoveError] = useState("");
  const existing = item?.photos ?? [];
  const room = MAX_PHOTOS - existing.length;

  async function removePhoto(imageId: number) {
    if (!item || !window.confirm("Remove this photo from the album?")) return;

    setRemoving(imageId);
    setRemoveError("");

    const result = await removeGalleryPhotoAction(item.id, imageId);

    setRemoving(null);

    if (result.ok) router.refresh();
    else setRemoveError(result.message || "The photo could not be removed.");
  }
  const [state, formAction] = useActionState(
    async (prev: typeof EMPTY_ACTION_STATE, data: FormData) => {
      // Shrink and upload photos one at a time, so no single request is large
      // enough to hit the host's body-size limit, then save with their URLs.
      const files = data
        .getAll("images[]")
        .filter((value): value is File => value instanceof File && value.size > 0);

      data.delete("images[]");

      if (files.length > room) {
        return {
          ok: false,
          message:
            room > 0
              ? `An album holds up to ${MAX_PHOTOS} photos. You can add ${room} more.`
              : `This album already has ${MAX_PHOTOS} photos. Remove one before adding another.`,
        };
      }

      try {
        for (let i = 0; i < files.length; i++) {
          setProgress(`Uploading photo ${i + 1} of ${files.length}…`);

          const body = new FormData();
          body.set("image", await shrinkImage(files[i], MAX_BYTES - 64 * 1024));

          const uploaded = await uploadGalleryPhotoAction(body);

          if (!uploaded.url) {
            return {
              ok: false,
              message: `${files[i].name}: ${uploaded.message || "upload failed."}`,
            };
          }

          data.append("image_urls[]", uploaded.url);
        }
      } catch {
        return {
          ok: false,
          message: "A photo could not be uploaded. Check your connection and try again.",
        };
      } finally {
        setProgress("");
      }

      const result = await saveGalleryAction(prev, data);

      // Clear the form after a successful create so the next one starts empty.
      if (result.ok && !item) {
        formRef.current?.reset();
        setPhotoCount(0);
        onDone?.();
      }

      return result;
    },
    EMPTY_ACTION_STATE
  );

  return (
    <form ref={formRef} action={formAction} className="space-y-4">
      {item ? <input type="hidden" name="id" value={item.id} /> : null}

      <FormMessage state={state} />

      <div>
        <label className="label" htmlFor={`title-${item?.id ?? "new"}`}>
          Title
        </label>
        <input
          id={`title-${item?.id ?? "new"}`}
          name="title"
          className="input"
          defaultValue={item?.title ?? ""}
          maxLength={200}
          required
        />
        <FieldError errors={state.errors} name="title" />
      </div>

      <div>
        <label className="label" htmlFor={`description-${item?.id ?? "new"}`}>
          Description
        </label>
        <textarea
          id={`description-${item?.id ?? "new"}`}
          name="description"
          className="textarea"
          defaultValue={item?.description ?? ""}
        />
        <FieldError errors={state.errors} name="description" />
      </div>

      <div>
        <label className="label" htmlFor={`facebook-${item?.id ?? "new"}`}>
          Facebook album link
        </label>
        <input
          id={`facebook-${item?.id ?? "new"}`}
          name="facebook_album_url"
          type="url"
          className="input"
          defaultValue={item?.facebook_album_url ?? ""}
          placeholder="https://www.facebook.com/media/set/..."
        />
        <p className="hint">Optional. When provided, clicking the album preview opens this Facebook album.</p>
        <FieldError errors={state.errors} name="facebook_album_url" />
      </div>

      <div>
        <label className="label" htmlFor={`images-${item?.id ?? "new"}`}>
          Photos
        </label>
        {existing.length > 0 ? (
          <div className="mb-3">
            <ul className="flex flex-wrap gap-3">
              {existing.map((photo) => (
                <li key={photo.id} className="relative">
                  <Thumb src={photo.url} alt={item?.title ?? ""} className="h-20 w-28" />
                  {existing.length > 1 ? (
                    <button
                      type="button"
                      onClick={() => removePhoto(photo.id)}
                      disabled={removing === photo.id}
                      className="absolute -right-2 -top-2 grid h-6 w-6 place-items-center rounded-full border border-rose-900 bg-rose-950 text-xs text-rose-300 hover:bg-rose-800 disabled:opacity-50"
                      aria-label="Remove this photo"
                      title="Remove this photo"
                    >
                      {removing === photo.id ? "…" : "✕"}
                    </button>
                  ) : null}
                </li>
              ))}
            </ul>
            <p className="mt-2 text-xs text-slate-500">
              {existing.length} of {MAX_PHOTOS} photos in this album.
              {existing.length > 1 ? " Use ✕ to remove one." : ""}
            </p>
            {removeError ? <p className="error-text">{removeError}</p> : null}
          </div>
        ) : null}
        <input
          id={`images-${item?.id ?? "new"}`}
          name="images[]"
          type="file"
          accept="image/*"
          multiple
          className="input file:mr-3 file:rounded file:border-0 file:bg-ink-700 file:px-3 file:py-1 file:text-slate-200"
          required={!item}
          disabled={!!item && room <= 0}
          onChange={(event) => setPhotoCount(event.currentTarget.files?.length ?? 0)}
        />
        <p className="hint">
          {item
            ? room > 0
              ? `You can add ${room} more photo${room === 1 ? "" : "s"} (up to ${MAX_PHOTOS} per album).`
              : `This album is full (${MAX_PHOTOS} photos). Remove one to add another.`
            : `Select 1–${MAX_PHOTOS} photos, up to 2 MB each. Larger photos are shrunk automatically.`}
        </p>
        {progress ? <p className="mt-1 text-xs text-sky-400">{progress}</p> : null}
        {photoCount > room ? (
          <p className="mt-1 text-xs text-rose-400">Please select no more than {Math.max(room, 0)} photo{room === 1 ? "" : "s"}.</p>
        ) : null}
        <FieldError errors={state.errors} name="images" />
      </div>

      <label className="flex items-center gap-2 text-sm text-slate-300">
        <input
          type="checkbox"
          name="is_active"
          value="1"
          defaultChecked={item ? item.is_active : true}
        />
        Published on the gallery page
      </label>

      <label className="flex items-center gap-2 text-sm text-slate-300">
        <input type="checkbox" name="show_on_homepage" value="1" defaultChecked={item?.show_on_homepage ?? false} />
        Show on homepage (first three published entries in gallery order)
      </label>

      <SubmitButton>{item ? "Save changes" : "Add to gallery"}</SubmitButton>
    </form>
  );
}
