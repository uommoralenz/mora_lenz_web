import { ImageField } from "@/components/ImageField";
import type { GalleryImageRecord } from "@/lib/types";
import Link from "next/link";

type GalleryImageFormProps = {
  action: (formData: FormData) => Promise<void>;
  image?: GalleryImageRecord;
  submitLabel: string;
};

function isActive(image?: GalleryImageRecord) {
  return !image || image.is_active === true || image.is_active === 1;
}

export function GalleryImageForm({ action, image, submitLabel }: GalleryImageFormProps) {
  return (
    <form action={action} className="form-panel" encType="multipart/form-data">
      {image ? <input type="hidden" name="id" value={image.id} /> : null}

      <div className="form-grid">
        <label>
          <span>Title</span>
          <input name="title" defaultValue={image?.title ?? ""} required maxLength={255} />
        </label>

        <label>
          <span>Category</span>
          <input name="category" defaultValue={image?.category ?? ""} maxLength={255} />
        </label>

        <label>
          <span>Sort order</span>
          <input type="number" name="sort_order" defaultValue={image?.sort_order ?? 0} min="0" />
        </label>

        <label className="checkbox-label">
          <input type="checkbox" name="is_active" value="1" defaultChecked={isActive(image)} />
          <span>Visible on public gallery</span>
        </label>
      </div>

      <ImageField name="image_url" label="Image URL" type="gallery" defaultValue={image?.image_url ?? ""} maxLength={1000} />

      <div className="form-actions">
        <Link href="/gallery" className="button button-muted">
          Cancel
        </Link>
        <button type="submit" className="button button-primary">
          {submitLabel}
        </button>
      </div>
    </form>
  );
}

