import Disclosure from "@/components/disclosure";
import { ConfirmSubmit } from "@/components/form";
import { Badge, Card, EmptyState, PageHeader, SectionTitle, Thumb } from "@/components/ui";
import { api } from "@/lib/api";
import type { GalleryItem } from "@/lib/types";

import { deleteGalleryAction, reorderGalleriesAction } from "./actions";
import GalleryForm from "./gallery-form";

export const dynamic = "force-dynamic";

function swapPayload(items: GalleryItem[], index: number, delta: number) {
  const other = index + delta;

  if (other < 0 || other >= items.length) return null;

  return JSON.stringify([
    { id: items[index].id, sort_order: other },
    { id: items[other].id, sort_order: index },
  ]);
}

export default async function GalleryPage() {
  const { data: items } = await api.get<{ data: GalleryItem[] }>("/galleries");

  return (
    <>
      <PageHeader
        title="Featured gallery"
        description="The alternating image-and-text rows in the Featured Galleries section of the homepage."
      />

      <Card className="mb-6">
        <Disclosure label="Add a gallery" variant="primary">
          <GalleryForm />
        </Disclosure>
      </Card>

      {items.length === 0 ? (
        <EmptyState title="Nothing in the featured gallery yet." />
      ) : (
        <>
          <SectionTitle>{items.length} in order of appearance</SectionTitle>

          <ul className="space-y-3">
            {items.map((item, index) => {
              const up = swapPayload(items, index, -1);
              const down = swapPayload(items, index, 1);

              return (
                <li key={item.id} className="card p-4">
                  <div className="flex flex-wrap items-center gap-4">
                    <Thumb src={item.image_url} alt={item.title} />

                    <div className="min-w-0 flex-1">
                      <div className="flex flex-wrap items-center gap-2">
                        <p className="truncate font-medium text-slate-100">
                          {item.title}
                        </p>
                        {!item.is_active ? <Badge tone="danger">Hidden</Badge> : null}
                      </div>
                      {item.description ? (
                        <p className="mt-1 line-clamp-2 text-xs text-slate-500">
                          {item.description}
                        </p>
                      ) : null}
                    </div>

                    <div className="flex items-center gap-1">
                      <form action={reorderGalleriesAction}>
                        <input type="hidden" name="items" value={up ?? ""} />
                        <button
                          type="submit"
                          className="btn-ghost"
                          disabled={!up}
                          aria-label="Move up"
                        >
                          ↑
                        </button>
                      </form>

                      <form action={reorderGalleriesAction}>
                        <input type="hidden" name="items" value={down ?? ""} />
                        <button
                          type="submit"
                          className="btn-ghost"
                          disabled={!down}
                          aria-label="Move down"
                        >
                          ↓
                        </button>
                      </form>

                      <form action={deleteGalleryAction}>
                        <input type="hidden" name="id" value={item.id} />
                        <ConfirmSubmit
                          confirm={`Delete "${item.title}" and its image? This cannot be undone.`}
                        >
                          Delete
                        </ConfirmSubmit>
                      </form>
                    </div>
                  </div>

                  <div className="mt-4 border-t border-ink-700 pt-4">
                    <Disclosure label="Edit" variant="ghost">
                      <GalleryForm item={item} />
                    </Disclosure>
                  </div>
                </li>
              );
            })}
          </ul>
        </>
      )}
    </>
  );
}
