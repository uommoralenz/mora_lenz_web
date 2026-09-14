"use client";

import { useActionState, useRef } from "react";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { Thumb } from "@/components/ui";
import {
  EMPTY_ACTION_STATE,
  type TeamGroupItem,
  type TeamMemberItem,
  type TeamSubgroupItem,
} from "@/lib/types";

import { saveGroupAction, saveMemberAction, saveSubgroupAction } from "./actions";

/* ------------------------------------------------------------------ group */

export function GroupForm({ group }: { group?: TeamGroupItem }) {
  const formRef = useRef<HTMLFormElement>(null);
  const uid = group?.id ?? "new";

  const [state, formAction] = useActionState(
    async (prev: typeof EMPTY_ACTION_STATE, data: FormData) => {
      const result = await saveGroupAction(prev, data);
      if (result.ok && !group) formRef.current?.reset();
      return result;
    },
    EMPTY_ACTION_STATE
  );

  return (
    <form ref={formRef} action={formAction} className="space-y-4">
      {group ? <input type="hidden" name="id" value={group.id} /> : null}

      <FormMessage state={state} />

      <div>
        <label className="label" htmlFor={`g-name-${uid}`}>
          Group name
        </label>
        <input
          id={`g-name-${uid}`}
          name="name"
          className="input"
          defaultValue={group?.name ?? ""}
          placeholder="Executive Committee"
          maxLength={150}
          required
        />
        <FieldError errors={state.errors} name="name" />
      </div>

      <div>
        <label className="label" htmlFor={`g-desc-${uid}`}>
          Description <span className="font-normal text-slate-500">(optional)</span>
        </label>
        <input
          id={`g-desc-${uid}`}
          name="description"
          className="input"
          defaultValue={group?.description ?? ""}
          maxLength={300}
        />
        <p className="hint">Shown as a caption under the group heading.</p>
        <FieldError errors={state.errors} name="description" />
      </div>

      <label className="flex items-center gap-2 text-sm text-slate-300">
        <input
          type="checkbox"
          name="is_active"
          value="1"
          defaultChecked={group ? group.is_active : true}
        />
        Visible on the Team page
      </label>

      <SubmitButton>{group ? "Save group" : "Create group"}</SubmitButton>
    </form>
  );
}

/* --------------------------------------------------------------- subgroup */

export function SubgroupForm({
  groupId,
  groups,
  subgroup,
}: {
  groupId: number;
  groups: TeamGroupItem[];
  subgroup?: TeamSubgroupItem;
}) {
  const formRef = useRef<HTMLFormElement>(null);
  const uid = subgroup?.id ?? `new-${groupId}`;

  const [state, formAction] = useActionState(
    async (prev: typeof EMPTY_ACTION_STATE, data: FormData) => {
      const result = await saveSubgroupAction(prev, data);
      if (result.ok && !subgroup) formRef.current?.reset();
      return result;
    },
    EMPTY_ACTION_STATE
  );

  return (
    <form ref={formRef} action={formAction} className="space-y-4">
      {subgroup ? <input type="hidden" name="id" value={subgroup.id} /> : null}

      <FormMessage state={state} />

      <div>
        <label className="label" htmlFor={`s-name-${uid}`}>
          Subgroup name
        </label>
        <input
          id={`s-name-${uid}`}
          name="name"
          className="input"
          defaultValue={subgroup?.name ?? ""}
          placeholder="Photography Crew"
          maxLength={150}
          required
        />
        <FieldError errors={state.errors} name="name" />
      </div>

      <div>
        <label className="label" htmlFor={`s-group-${uid}`}>
          Inside group
        </label>
        <select
          id={`s-group-${uid}`}
          name="group_id"
          className="input"
          defaultValue={subgroup?.group_id ?? groupId}
        >
          {groups.map((g) => (
            <option key={g.id} value={g.id}>
              {g.name}
            </option>
          ))}
        </select>
        <p className="hint">Moving a subgroup takes its members with it.</p>
        <FieldError errors={state.errors} name="group_id" />
      </div>

      <div>
        <label className="label" htmlFor={`s-desc-${uid}`}>
          Description <span className="font-normal text-slate-500">(optional)</span>
        </label>
        <input
          id={`s-desc-${uid}`}
          name="description"
          className="input"
          defaultValue={subgroup?.description ?? ""}
          maxLength={300}
        />
        <FieldError errors={state.errors} name="description" />
      </div>

      <label className="flex items-center gap-2 text-sm text-slate-300">
        <input
          type="checkbox"
          name="is_active"
          value="1"
          defaultChecked={subgroup ? subgroup.is_active : true}
        />
        Visible on the Team page
      </label>

      <SubmitButton>{subgroup ? "Save subgroup" : "Create subgroup"}</SubmitButton>
    </form>
  );
}

/* ----------------------------------------------------------------- member */

export function MemberForm({
  groupId,
  subgroupId,
  groups,
  member,
}: {
  groupId: number;
  subgroupId?: number | null;
  groups: TeamGroupItem[];
  member?: TeamMemberItem;
}) {
  const formRef = useRef<HTMLFormElement>(null);
  const uid = member?.id ?? `new-${groupId}-${subgroupId ?? "direct"}`;

  const [state, formAction] = useActionState(
    async (prev: typeof EMPTY_ACTION_STATE, data: FormData) => {
      const result = await saveMemberAction(prev, data);
      if (result.ok && !member) formRef.current?.reset();
      return result;
    },
    EMPTY_ACTION_STATE
  );

  // A flat list of every placement, so a member can be moved anywhere.
  const placements: { value: string; label: string }[] = [];

  for (const g of groups) {
    placements.push({ value: `${g.id}:`, label: `${g.name} — directly in group` });

    for (const s of g.subgroups) {
      placements.push({ value: `${g.id}:${s.id}`, label: `${g.name} → ${s.name}` });
    }
  }

  const currentPlacement = member
    ? `${member.group_id}:${member.subgroup_id ?? ""}`
    : `${groupId}:${subgroupId ?? ""}`;

  return (
    <form ref={formRef} action={formAction} className="space-y-4">
      {member ? <input type="hidden" name="id" value={member.id} /> : null}

      <FormMessage state={state} />

      <div className="grid gap-4 sm:grid-cols-2">
        <div>
          <label className="label" htmlFor={`m-name-${uid}`}>
            Name
          </label>
          <input
            id={`m-name-${uid}`}
            name="name"
            className="input"
            defaultValue={member?.name ?? ""}
            maxLength={150}
            required
          />
          <FieldError errors={state.errors} name="name" />
        </div>

        <div>
          <label className="label" htmlFor={`m-role-${uid}`}>
            Role
          </label>
          <input
            id={`m-role-${uid}`}
            name="profession"
            className="input"
            defaultValue={member?.profession ?? ""}
            placeholder="Lead Photographer"
            maxLength={150}
            required
          />
          <FieldError errors={state.errors} name="profession" />
        </div>
      </div>

      <div>
        <label className="label" htmlFor={`m-place-${uid}`}>
          Placement
        </label>
        <PlacementSelect
          id={`m-place-${uid}`}
          placements={placements}
          defaultValue={currentPlacement}
        />
        <FieldError errors={state.errors} name="group_id" />
        <FieldError errors={state.errors} name="subgroup_id" />
      </div>

      <div>
        <label className="label" htmlFor={`m-desc-${uid}`}>
          Short bio <span className="font-normal text-slate-500">(optional)</span>
        </label>
        <textarea
          id={`m-desc-${uid}`}
          name="description"
          className="textarea min-h-20"
          defaultValue={member?.description ?? ""}
          maxLength={1000}
        />
        <FieldError errors={state.errors} name="description" />
      </div>

      <div>
        <label className="label" htmlFor={`m-image-${uid}`}>
          Photo
        </label>
        {member?.image_url ? (
          <div className="mb-2 flex items-center gap-3">
            <Thumb
              src={member.image_url}
              alt={member.name}
              className="h-16 w-16 rounded-full"
            />
            <label className="flex items-center gap-2 text-xs text-slate-400">
              <input type="checkbox" name="remove_image" value="1" />
              Remove this photo
            </label>
          </div>
        ) : null}
        <input
          id={`m-image-${uid}`}
          name="image"
          type="file"
          accept="image/*"
          className="input file:mr-3 file:rounded file:border-0 file:bg-ink-700 file:px-3 file:py-1 file:text-slate-200"
        />
        <p className="hint">
          Shown as a circle, so a square photo crops best. Without one, the member&rsquo;s
          initial is used.
        </p>
        <FieldError errors={state.errors} name="image" />
      </div>

      <label className="flex items-center gap-2 text-sm text-slate-300">
        <input
          type="checkbox"
          name="is_active"
          value="1"
          defaultChecked={member ? member.is_active : true}
        />
        Visible on the Team page
      </label>

      <SubmitButton>{member ? "Save member" : "Add member"}</SubmitButton>
    </form>
  );
}

/**
 * One select for "which group / which subgroup", split back into the two
 * fields Laravel expects on submit.
 */
function PlacementSelect({
  id,
  placements,
  defaultValue,
}: {
  id: string;
  placements: { value: string; label: string }[];
  defaultValue: string;
}) {
  const groupRef = useRef<HTMLInputElement>(null);
  const subgroupRef = useRef<HTMLInputElement>(null);

  const [initialGroup, initialSubgroup] = defaultValue.split(":");

  const apply = (value: string) => {
    const [group, subgroup] = value.split(":");
    if (groupRef.current) groupRef.current.value = group;
    if (subgroupRef.current) subgroupRef.current.value = subgroup ?? "";
  };

  return (
    <>
      <select
        id={id}
        className="input"
        defaultValue={defaultValue}
        onChange={(event) => apply(event.target.value)}
      >
        {placements.map((placement) => (
          <option key={placement.value} value={placement.value}>
            {placement.label}
          </option>
        ))}
      </select>
      <input ref={groupRef} type="hidden" name="group_id" defaultValue={initialGroup} />
      <input
        ref={subgroupRef}
        type="hidden"
        name="subgroup_id"
        defaultValue={initialSubgroup ?? ""}
      />
    </>
  );
}
