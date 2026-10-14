"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";

import { ApiError, api, asUpdate, pruneEmptyFile, setBool, toActionState } from "@/lib/api";
import type { ActionState, EventPhoto } from "@/lib/types";

const BOOLS = ["countdown_enabled", "is_featured", "is_active"];

function normalise(form: FormData) {
  BOOLS.forEach((field) => setBool(form, field));
  pruneEmptyFile(form);

  // One Facebook album link per line in the form; the API takes a list. Sent
  // as "" when there are none, so clearing the box clears the links.
  if (form.has("facebook_album_links")) {
    const links = facebookLinks(form);
    form.delete("facebook_album_links");
    if (links.length === 0) form.set("facebook_album_urls", "");
    links.forEach((link) => form.append("facebook_album_urls[]", link));
  }

  // An empty end date must not be sent as "", which fails the `date` rule.
  if (!String(form.get("end_date") ?? "").trim()) {
    form.delete("end_date");
  }

  return form;
}

function facebookLinks(form: FormData): string[] {
  return String(form.get("facebook_album_links") ?? "")
    .split(/[\r\n]+/)
    .map((link) => link.trim())
    .filter(Boolean);
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
    page_options: text("page_options"),
    location: text("location"),
    countdown_enabled: formData.get("countdown_enabled") ? true : false,
    facebook_album_urls: facebookLinks(formData),
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

// --------------------------------------------------------------- photo album

function refreshEvent(eventId: string) {
  revalidatePath("/events");
  revalidatePath(`/events/${eventId}`);
  revalidatePath("/dashboard");
}

/**
 * Add one batch of photos to an event's album. The editor splits a large
 * selection into batches small enough for the server action body limit.
 */
export async function uploadEventPhotosAction(
  formData: FormData
): Promise<{ ok: boolean; message: string; photos?: EventPhoto[] }> {
  const eventId = String(formData.get("event_id") ?? "");

  if (!/^\d+$/.test(eventId)) {
    return { ok: false, message: "Save the event before adding photos." };
  }

  const form = new FormData();
  for (const file of formData.getAll("images[]")) {
    if (file instanceof File && file.size > 0) form.append("images[]", file);
  }

  if (!form.has("images[]")) {
    return { ok: false, message: "Choose at least one photo." };
  }

  try {
    const result = await api.post<{ data: EventPhoto[] }>(`/events/${eventId}/photos`, form);
    refreshEvent(eventId);
    return { ok: true, message: "Photos added.", photos: result.data };
  } catch (error) {
    const state = toActionState(error);
    const first = state.errors ? Object.values(state.errors)[0] : "";
    return { ok: false, message: first || state.message };
  }
}

export async function saveEventPhotoCaptionAction(
  eventId: number,
  photoId: number,
  caption: string
): Promise<ActionState> {
  try {
    await api.put(`/events/${eventId}/photos/${photoId}`, { caption });
  } catch (error) {
    return toActionState(error);
  }

  refreshEvent(String(eventId));
  return { ok: true, message: "Caption saved." };
}

export async function deleteEventPhotoAction(eventId: number, photoId: number): Promise<ActionState> {
  try {
    await api.del(`/events/${eventId}/photos/${photoId}`);
  } catch (error) {
    return toActionState(error);
  }

  refreshEvent(String(eventId));
  return { ok: true, message: "Photo removed." };
}
