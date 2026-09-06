import { updateGalleryImageAction } from "@/app/gallery/actions";
import { AdminShell } from "@/components/AdminShell";
import { GalleryImageForm } from "@/components/GalleryImageForm";
import { requireAdmin } from "@/lib/auth";
import { getGalleryImage } from "@/lib/data";
import { notFound } from "next/navigation";

export const runtime = "nodejs";

type EditGalleryImagePageProps = {
  params: Promise<{
    id: string;
  }>;
};

export default async function EditGalleryImagePage({ params }: EditGalleryImagePageProps) {
  const admin = await requireAdmin();
  const { id } = await params;
  const image = await getGalleryImage(id);

  if (!image) {
    notFound();
  }

  return (
    <AdminShell admin={admin} title="Edit Gallery Image" description="Update the public gallery image.">
      <GalleryImageForm action={updateGalleryImageAction} image={image} submitLabel="Save Image" />
    </AdminShell>
  );
}
