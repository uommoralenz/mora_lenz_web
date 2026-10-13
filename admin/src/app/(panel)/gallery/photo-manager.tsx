"use client";

import { useActionState } from "react";

import { ConfirmSubmit, FormMessage } from "@/components/form";
import { Thumb } from "@/components/ui";
import { EMPTY_ACTION_STATE, type GalleryPhoto } from "@/lib/types";

import { deleteGalleryPhotoAction } from "./actions";

function PhotoTile({ galleryId, photo }: { galleryId: number; photo: GalleryPhoto }) {
  const [state, action] = useActionState(deleteGalleryPhotoAction, EMPTY_ACTION_STATE);

  return (
    <form action={action} className="space-y-1">
      <input type="hidden" name="gallery_id" value={galleryId} />
      <input type="hidden" name="photo_id" value={photo.id} />
      <Thumb src={photo.image_url} alt="" className="h-20 w-full" />
      <ConfirmSubmit confirm="Remove this photo from the album? This cannot be undone." className="btn-danger w-full px-2 py-1 text-xs">
        Remove
      </ConfirmSubmit>
      <FormMessage state={state} />
    </form>
  );
}

export default function PhotoManager({ galleryId, photos }: { galleryId: number; photos: GalleryPhoto[] }) {
  if (photos.length === 0) return null;

  return (
    <div className="mb-4">
      <p className="label">Current photos</p>
      <div className="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-5">
        {photos.map((photo) => (
          <PhotoTile key={photo.id} galleryId={galleryId} photo={photo} />
        ))}
      </div>
    </div>
  );
}
