import { createGalleryImageAction } from "@/app/gallery/actions";
import { AdminShell } from "@/components/AdminShell";
import { GalleryImageForm } from "@/components/GalleryImageForm";
import { requireAdmin } from "@/lib/auth";

export const runtime = "nodejs";

export default async function NewGalleryImagePage() {
  const admin = await requireAdmin();

  return (
    <AdminShell admin={admin} title="New Gallery Image" description="Add an image to the public gallery carousel.">
      <GalleryImageForm action={createGalleryImageAction} submitLabel="Create Image" />
    </AdminShell>
  );
}
