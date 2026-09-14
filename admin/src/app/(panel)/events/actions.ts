"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";

import { api, asUpdate, pruneEmptyFile, setBool, toActionState } from "@/lib/api";
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
