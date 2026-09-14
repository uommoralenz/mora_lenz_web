import Disclosure from "@/components/disclosure";
import { ConfirmSubmit } from "@/components/form";
import { Badge, Card, EmptyState, PageHeader, SectionTitle, Thumb } from "@/components/ui";
import { api } from "@/lib/api";
import type { ServiceImageItem, ServicePackageItem, ServiceType } from "@/lib/types";

import {
  deletePackageAction,
  deleteServiceImageAction,
  reorderPackagesAction,
  reorderServiceImagesAction,
} from "./actions";
import ImageForm from "./image-form";
import PackageForm from "./package-form";

export const dynamic = "force-dynamic";

const SERVICES: { type: ServiceType; label: string }[] = [
  { type: "photography", label: "Photography" },
  { type: "videography", label: "Videography" },
];

function swapPayload<T extends { id: number }>(items: T[], index: number, delta: number) {
  const other = index + delta;

  if (other < 0 || other >= items.length) return null;

  return JSON.stringify([
    { id: items[index].id, sort_order: other },
    { id: items[other].id, sort_order: index },
  ]);
}

function money(value: number) {
  return `LKR ${new Intl.NumberFormat("en-LK").format(value)}`;
}

export default async function ServicesPage() {
  const [{ data: packages }, { data: images }] = await Promise.all([
    api.get<{ data: ServicePackageItem[] }>("/service-packages"),
    api.get<{ data: ServiceImageItem[] }>("/service-images"),
  ]);

  return (
    <>
      <PageHeader
        title="Services"
        description="Packages and carousel images for /services/photography and /services/videography."
      />

      <div className="space-y-10">
        {SERVICES.map(({ type, label }) => {
          const typePackages = packages.filter((p) => p.service_type === type);
          const typeImages = images.filter((i) => i.service_type === type);

          return (
            <section key={type}>
              <h2 className="mb-4 text-lg font-semibold text-slate-100">{label}</h2>

              {/* ---------------------------------------------- carousel */}
              <Card className="mb-4">
                <SectionTitle>Carousel images ({typeImages.length})</SectionTitle>

                {typeImages.length > 0 ? (
                  <ul className="mb-4 flex flex-wrap gap-3">
                    {typeImages.map((image, index) => {
                      const up = swapPayload(typeImages, index, -1);
                      const down = swapPayload(typeImages, index, 1);

                      return (
                        <li key={image.id} className="w-32">
                          <Thumb
                            src={image.image_url}
                            alt={image.caption ?? `${label} image ${index + 1}`}
                            className="h-24 w-32"
                          />
                          <div className="mt-1 flex items-center justify-between gap-1">
                            <div className="flex">
                              <form action={reorderServiceImagesAction}>
                                <input type="hidden" name="items" value={up ?? ""} />
                                <button
                                  type="submit"
                                  className="btn-ghost"
                                  disabled={!up}
                                  aria-label="Move earlier"
                                >
                                  ←
                                </button>
                              </form>
                              <form action={reorderServiceImagesAction}>
                                <input type="hidden" name="items" value={down ?? ""} />
                                <button
                                  type="submit"
                                  className="btn-ghost"
                                  disabled={!down}
                                  aria-label="Move later"
                                >
                                  →
                                </button>
                              </form>
                            </div>

                            <form action={deleteServiceImageAction}>
                              <input type="hidden" name="id" value={image.id} />
                              <ConfirmSubmit
                                className="btn-ghost text-rose-400 hover:text-rose-300"
                                confirm="Delete this carousel image?"
                              >
                                ✕
                              </ConfirmSubmit>
                            </form>
                          </div>
                        </li>
                      );
                    })}
                  </ul>
                ) : (
                  <p className="mb-4 text-sm text-slate-500">
                    No carousel images — the page falls back to a plain icon.
                  </p>
                )}

                <Disclosure label="Upload images">
                  <ImageForm serviceType={type} />
                </Disclosure>
              </Card>

              {/* ---------------------------------------------- packages */}
              <Card>
                <SectionTitle>Packages ({typePackages.length})</SectionTitle>

                {typePackages.length === 0 ? (
                  <EmptyState title={`No ${label.toLowerCase()} packages yet.`} />
                ) : (
                  <ul className="mb-4 space-y-3">
                    {typePackages.map((pkg, index) => {
                      const up = swapPayload(typePackages, index, -1);
                      const down = swapPayload(typePackages, index, 1);

                      return (
                        <li key={pkg.id} className="rounded-lg border border-ink-700 p-4">
                          <div className="flex flex-wrap items-center gap-4">
                            <Thumb src={pkg.image_url} alt={pkg.name} />

                            <div className="min-w-0 flex-1">
                              <div className="flex flex-wrap items-center gap-2">
                                <p className="truncate font-medium text-slate-100">
                                  {pkg.name}
                                </p>
                                {pkg.offered_price !== null ? (
                                  <Badge tone="success">Offer</Badge>
                                ) : null}
                                {!pkg.is_active ? (
                                  <Badge tone="danger">Hidden</Badge>
                                ) : null}
                              </div>
                              <p className="mt-1 text-xs text-slate-500">
                                {pkg.offered_price !== null ? (
                                  <>
                                    {money(pkg.offered_price)}{" "}
                                    <span className="line-through">
                                      {money(pkg.price)}
                                    </span>
                                  </>
                                ) : (
                                  money(pkg.price)
                                )}
                                {" · "}
                                {pkg.description.length} bullet
                                {pkg.description.length === 1 ? "" : "s"}
                              </p>
                            </div>

                            <div className="flex items-center gap-1">
                              <form action={reorderPackagesAction}>
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
                              <form action={reorderPackagesAction}>
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
                              <form action={deletePackageAction}>
                                <input type="hidden" name="id" value={pkg.id} />
                                <ConfirmSubmit
                                  confirm={`Delete the "${pkg.name}" package?`}
                                >
                                  Delete
                                </ConfirmSubmit>
                              </form>
                            </div>
                          </div>

                          <div className="mt-4 border-t border-ink-700 pt-4">
                            <Disclosure label="Edit" variant="ghost">
                              <PackageForm serviceType={type} pkg={pkg} />
                            </Disclosure>
                          </div>
                        </li>
                      );
                    })}
                  </ul>
                )}

                <Disclosure label={`Add ${label.toLowerCase()} package`} variant="primary">
                  <PackageForm serviceType={type} />
                </Disclosure>
              </Card>
            </section>
          );
        })}
      </div>
    </>
  );
}
