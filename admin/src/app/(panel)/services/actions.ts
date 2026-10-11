"use server";

import { revalidatePath } from "next/cache";

import { api, asUpdate, pruneEmptyFile, setBool, toActionState } from "@/lib/api";
import type { ActionState } from "@/lib/types";

// ------------------------------------------------------------------ packages

export async function savePackageAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");

  setBool(formData, "is_active");
  setBool(formData, "has_special_discount");
  pruneEmptyFile(formData);
  formData.delete("id");

  // An unchecked discount always means there is no special price. When it is
  // checked, leave a blank value for Laravel to reject as required.
  if (formData.get("has_special_discount") !== "1") {
    formData.delete("offered_price");
  }

  try {
    if (id) {
      await api.post(`/service-packages/${id}`, asUpdate(formData));
    } else {
      await api.post("/service-packages", formData);
    }
  } catch (error) {
    return toActionState(error);
  }

  revalidatePath("/services");
  revalidatePath("/dashboard");

  return { ok: true, message: id ? "Package updated." : "Package added." };
}

export async function deletePackageAction(formData: FormData) {
  const id = String(formData.get("id") ?? "");

  if (id) {
    await api.del(`/service-packages/${id}`);
    revalidatePath("/services");
    revalidatePath("/dashboard");
  }
}

export async function reorderPackagesAction(formData: FormData) {
  const raw = String(formData.get("items") ?? "");

  if (!raw) return;

  await api.post("/service-packages/reorder", { items: JSON.parse(raw) });
  revalidatePath("/services");
}

// -------------------------------------------------------------------- images

export async function addServiceImagesAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  // The [] suffix makes PHP build the uploaded files as the array Laravel's
  // `images` / `images.*` validation rules expect.
  const files = formData.getAll("images[]").filter((f) => f instanceof File && f.size > 0);

  if (files.length === 0) {
    return { ok: false, message: "Choose at least one image." };
  }

  try {
    await api.post("/service-images", formData);
  } catch (error) {
    return toActionState(error);
  }

  revalidatePath("/services");

  return {
    ok: true,
    message: `${files.length} image${files.length === 1 ? "" : "s"} added.`,
  };
}

export async function deleteServiceImageAction(formData: FormData) {
  const id = String(formData.get("id") ?? "");

  if (id) {
    await api.del(`/service-images/${id}`);
    revalidatePath("/services");
  }
}

export async function reorderServiceImagesAction(formData: FormData) {
  const raw = String(formData.get("items") ?? "");

  if (!raw) return;

  await api.post("/service-images/reorder", { items: JSON.parse(raw) });
  revalidatePath("/services");
}
