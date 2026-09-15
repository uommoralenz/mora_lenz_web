"use client";

import { useActionState, useRef, useState } from "react";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { Thumb } from "@/components/ui";
import { EMPTY_ACTION_STATE, type GalleryItem } from "@/lib/types";

import { saveGalleryAction } from "./actions";

export default function GalleryForm({
  item,
  onDone,
}: {
  item?: GalleryItem;
  onDone?: () => void;
}) {
  const formRef = useRef<HTMLFormElement>(null);
  const [selectedImages, setSelectedImages] = useState<File[]>([]);
  const [state, formAction] = useActionState(
    async (prev: typeof EMPTY_ACTION_STATE, data: FormData) => {
      const result = await saveGalleryAction(prev, data);

      // Clear the form after a successful create so the next one starts empty.
      if (result.ok && !item) {
        formRef.current?.reset();
        setSelectedImages([]);
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
          required={!item}
        />
        <p className="hint">Clicking the album preview opens this Facebook album.</p>
        <FieldError errors={state.errors} name="facebook_album_url" />
      </div>

      <div>
        <label className="label" htmlFor={`images-${item?.id ?? "new"}`}>
          Photos
        </label>
        {item?.image_url ? (
          <div className="mb-2 flex items-center gap-3">
            <Thumb src={item.image_url} alt={item.title} className="h-20 w-32" />
            <p className="text-xs text-slate-500">
              {item.images.length} photo{item.images.length === 1 ? "" : "s"} currently in this album. New photos are added to it.
            </p>
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
          onChange={(event) => setSelectedImages(Array.from(event.currentTarget.files ?? []))}
        />
        <p className="hint">Select up to 30 photos. Max 8 MB each.</p>
        <FieldError errors={state.errors} name="images" />
      </div>

      {selectedImages.length > 0 ? (
        <div className="space-y-3 rounded border border-ink-700 p-3">
          <p className="text-sm font-medium text-slate-200">Photo descriptions</p>
          {selectedImages.map((file, index) => (
            <div key={`${file.name}-${index}`}>
              <label className="label" htmlFor={`photo-description-${index}`}>{file.name}</label>
              <textarea id={`photo-description-${index}`} name="image_descriptions[]" className="textarea" placeholder="Description shown on this slide" />
            </div>
          ))}
        </div>
      ) : null}

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
