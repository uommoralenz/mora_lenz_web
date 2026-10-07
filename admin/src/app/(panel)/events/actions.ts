"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";

import { ApiError, api, asUpdate, pruneEmptyFile, setBool, toActionState } from "@/lib/api";
import type { ActionState } from "@/lib/types";

const BOOLS = ["countdown_enabled", "is_featured", "is_active"];

function normalise(form: FormData) {
  BOOLS.forEach((field) => setBool(form, field));
  pruneEmptyFile(form);

  // An empty end date must not be sent as "", which fails the `date` rule.
  if (!String(form.get("end_date") ?? "").trim()) {
    form.delete("end_date");
  }

  return form;
}

export async function saveEventAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");
  const form = normalise(formData);
  form.delete("id");

  try {
    if (id) {
      await api.post(`/events/${id}`, asUpdate(form));
    } else {
      await api.post("/events", form);
    }
  } catch (error) {
    return toActionState(error);
  }

  revalidatePath("/events");
  revalidatePath("/dashboard");

  // React resets the form once the action finishes, restoring each input to its
  // defaultValue. Without revalidating THIS page too, those defaults would
  // still hold the values from page load and the form would appear to undo the
  // save that actually succeeded.
  if (id) revalidatePath(`/events/${id}`);

  if (!id) redirect("/events");

  return { ok: true, message: "Event saved." };
}

export async function deleteEventAction(_prev: ActionState, formData: FormData): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");

  if (!/^\d+$/.test(id) || Number(id) < 1) {
    return { ok: false, message: "Select a valid event to delete." };
  }
  try {
    await api.del(`/events/${id}`);
  } catch (error) {
    return toActionState(error);
  }
  revalidatePath("/events");
  revalidatePath("/dashboard");
  return { ok: true, message: "Event deleted." };
}

/** Move one event up or down by swapping sort_order with its neighbour. */
export async function reorderEventsAction(formData: FormData) {
  const raw = String(formData.get("items") ?? "");

  if (!raw) return;

  await api.post("/events/reorder", { items: JSON.parse(raw) });
  revalidatePath("/events");
}

/** Upload one image for a page block and hand back its public URL. */
export async function uploadEventImageAction(
  formData: FormData
): Promise<{ url?: string; message?: string }> {
  const file = formData.get("image");

  if (!(file instanceof File) || file.size === 0) {
    return { message: "Choose an image first." };
  }

  const form = new FormData();
  form.set("type", "events");
  form.set("image", file);

  try {
    const result = await api.post<{ url: string }>("/uploads", form);
    return { url: result.url };
  } catch (error) {
    if (error instanceof ApiError) {
      return { message: error.errors.image || error.message };
    }
    throw error;
  }
}

/** Send the unsaved form to Laravel and get back a link to the draft page. */
export async function previewEventAction(
  formData: FormData
): Promise<{ url?: string; message?: string }> {
  const text = (name: string) => String(formData.get(name) ?? "").trim();
  const body: Record<string, unknown> = {
    title: text("title"),
    description: text("description"),
    content: text("content"),
    location: text("location"),
    countdown_enabled: formData.get("countdown_enabled") ? true : false,
  };

  for (const field of ["event_date", "end_date", "event_id"]) {
    if (text(field)) body[field] = text(field);
  }

  try {
    const result = await api.post<{ url: string }>("/events/preview", body);
    return { url: result.url };
  } catch (error) {
    if (error instanceof ApiError) {
      const first = Object.values(error.errors)[0];
      return { message: first || error.message };
    }
    throw error;
  }
}
