"use server";

import { revalidatePath } from "next/cache";

import { api, asUpdate, pruneEmptyFile, setBool, toActionState } from "@/lib/api";
import type { ActionState } from "@/lib/types";

export async function saveAboutAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  setBool(formData, "remove_image");
  pruneEmptyFile(formData);

  try {
    await api.post("/pages/about", asUpdate(formData));
  } catch (error) {
    return toActionState(error);
  }

  // Without this the form would reset to the values from page load.
  revalidatePath("/about");

  return { ok: true, message: "About page saved. It is live on the site now." };
}
