"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";

import { api, asUpdate, pruneEmptyFile, setBool, toActionState } from "@/lib/api";
import type { ActionState, TeamTermItem } from "@/lib/types";

function refresh() {
  revalidatePath("/team");
  revalidatePath("/dashboard");
}

// --------------------------------------------------------------------- terms

/** Create or update one year's team. A new one opens straight after saving. */
export async function saveTermAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");

  setBool(formData, "is_active");
  setBool(formData, "is_current");
  formData.delete("id");

  // "" means "start empty"; Laravel wants that as no value at all.
  if (!String(formData.get("copy_from") ?? "").trim()) {
    formData.delete("copy_from");
  }

  const body = Object.fromEntries(formData.entries());
  let created: TeamTermItem | null = null;

  try {
    if (id) {
      await api.put(`/team/terms/${id}`, body);
    } else {
      created = (await api.post<{ data: TeamTermItem }>("/team/terms", body)).data;
    }
  } catch (error) {
    return toActionState(error);
  }

  refresh();

  if (created) redirect(`/team?term=${created.id}`);

  return { ok: true, message: "Team year updated." };
}

export async function deleteTermAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");

  if (!/^\d+$/.test(id)) {
    return { ok: false, message: "Select a valid team year to delete." };
  }

  try {
    await api.del(`/team/terms/${id}`);
  } catch (error) {
    const state = toActionState(error);
    return { ...state, message: state.errors?.term ?? state.message };
  }

  refresh();
  redirect("/team");
}

// -------------------------------------------------------------------- groups

export async function saveGroupAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");

  setBool(formData, "is_active");
  formData.delete("id");

  const body = Object.fromEntries(formData.entries());

  try {
    if (id) {
      await api.put(`/team/groups/${id}`, body);
    } else {
      await api.post("/team/groups", body);
    }
  } catch (error) {
    return toActionState(error);
  }

  refresh();

  return { ok: true, message: id ? "Group updated." : "Group created." };
}

export async function deleteGroupAction(formData: FormData) {
  const id = String(formData.get("id") ?? "");

  if (id) {
    await api.del(`/team/groups/${id}`);
    refresh();
  }
}

export async function reorderGroupsAction(formData: FormData) {
  const raw = String(formData.get("items") ?? "");

  if (!raw) return;

  await api.post("/team/groups/reorder", { items: JSON.parse(raw) });
  refresh();
}

// ----------------------------------------------------------------- subgroups

export async function saveSubgroupAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");

  setBool(formData, "is_active");
  formData.delete("id");

  const body = Object.fromEntries(formData.entries());

  try {
    if (id) {
      await api.put(`/team/subgroups/${id}`, body);
    } else {
      await api.post("/team/subgroups", body);
    }
  } catch (error) {
    return toActionState(error);
  }

  refresh();

  return { ok: true, message: id ? "Subgroup updated." : "Subgroup created." };
}

export async function deleteSubgroupAction(formData: FormData) {
  const id = String(formData.get("id") ?? "");

  if (id) {
    await api.del(`/team/subgroups/${id}`);
    refresh();
  }
}

export async function reorderSubgroupsAction(formData: FormData) {
  const raw = String(formData.get("items") ?? "");

  if (!raw) return;

  await api.post("/team/subgroups/reorder", { items: JSON.parse(raw) });
  refresh();
}

// ------------------------------------------------------------------- members

export async function saveMemberAction(
  _prev: ActionState,
  formData: FormData
): Promise<ActionState> {
  const id = String(formData.get("id") ?? "");

  setBool(formData, "is_active");
  setBool(formData, "remove_image");
  pruneEmptyFile(formData);
  formData.delete("id");

  // "" means "directly in the group"; Laravel wants that as no value at all.
  if (!String(formData.get("subgroup_id") ?? "").trim()) {
    formData.set("subgroup_id", "");
  }

  try {
    if (id) {
      await api.post(`/team/members/${id}`, asUpdate(formData));
    } else {
      await api.post("/team/members", formData);
    }
  } catch (error) {
    return toActionState(error);
  }

  refresh();

  return { ok: true, message: id ? "Member updated." : "Member added." };
}

export async function deleteMemberAction(formData: FormData) {
  const id = String(formData.get("id") ?? "");

  if (id) {
    await api.del(`/team/members/${id}`);
    refresh();
  }
}

export async function reorderMembersAction(formData: FormData) {
  const raw = String(formData.get("items") ?? "");

  if (!raw) return;

  await api.post("/team/members/reorder", { items: JSON.parse(raw) });
  refresh();
}
