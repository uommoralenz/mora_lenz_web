"use server";

import { revalidatePath } from "next/cache";

import { api, toActionState } from "@/lib/api";
import type { ActionState } from "@/lib/types";

/**
 * Apply any database updates that shipped with the last upload of the Laravel
 * side. Super admin only — the API enforces that too.
 */
export async function runMigrationsAction(
  _prev: ActionState,
  _formData: FormData
): Promise<ActionState> {
  let result: { data: { ok: boolean; applied: string[] } };

  try {
    result = await api.post<{ data: { ok: boolean; applied: string[] } }>(
      "/system/migrate"
    );
  } catch (error) {
    return toActionState(error);
  }

  const { ok, applied } = result.data;

  // Every page reads data shaped by the schema that just changed.
  revalidatePath("/", "layout");

  if (!ok) {
    return {
      ok: false,
      message:
        "Some updates could not be applied. Check storage/logs/laravel.log on the server.",
    };
  }

  return {
    ok: true,
    message:
      applied.length === 0
        ? "The database was already up to date."
        : `Applied ${applied.length} database update${applied.length === 1 ? "" : "s"}. Saving should work now.`,
  };
}
