import Link from "next/link";
import { deleteGalleryImageAction } from "@/app/gallery/actions";
import { AdminShell } from "@/components/AdminShell";
import { requireAdmin } from "@/lib/auth";
import { getGalleryImages } from "@/lib/data";

export const runtime = "nodejs";

function activeLabel(value: boolean | number) {
  return value === true || value === 1 ? "active" : "inactive";
}

export default async function GalleryPage() {
  const admin = await requireAdmin();
  const images = await getGalleryImages();

  return (
    <AdminShell
      admin={admin}
      title="Gallery"
      description="Manage the public gallery carousel."
      action={
        <Link href="/gallery/new" className="button button-primary">
          New Image
        </Link>
      }
    >
      <section className="panel">
        <div className="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Image</th>
                <th>Category</th>
                <th>Status</th>
                <th>Sort</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {images.length > 0 ? (
                images.map((image) => {
                  const status = activeLabel(image.is_active);

                  return (
                    <tr key={image.id}>
                      <td>
                        <div className="table-identity">
                          <img src={image.image_url} alt="" />
                          <div>
                            <strong>{image.title}</strong>
                            <p>{image.image_url}</p>
                          </div>
                        </div>
                      </td>
                      <td>{image.category ?? "-"}</td>
                      <td>
                        <span className={`status-pill ${status}`}>{status}</span>
                      </td>
                      <td>{image.sort_order}</td>
                      <td>
                        <div className="row-actions">
                          <Link href={`/gallery/${image.id}/edit`} className="button button-muted">
                            Edit
                          </Link>
                          <form action={deleteGalleryImageAction}>
                            <input type="hidden" name="id" value={image.id} />
                            <button type="submit" className="button button-danger">
                              Delete
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  );
                })
              ) : (
                <tr>
                  <td colSpan={5} className="empty-table">
                    No gallery images yet.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </section>
    </AdminShell>
  );
}
