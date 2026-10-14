"use client";

import { useActionState } from "react";

import { ConfirmSubmit, FieldError, FormMessage, SubmitButton } from "@/components/form";
import { EMPTY_ACTION_STATE, type TeamTermItem } from "@/lib/types";

import { deleteTermAction, saveTermAction } from "./actions";

/** "2026/27" for a team that starts in 2026. */
function defaultName(year: number) {
  return `${year}/${String(year + 1).slice(-2)}`;
}

export function TermForm({
  term,
  terms,
}: {
  term?: TeamTermItem;
  terms: TeamTermItem[];
}) {
  const [state, formAction] = useActionState(saveTermAction, EMPTY_ACTION_STATE);
  const uid = term?.id ?? "new";

  // A new term usually follows the newest one.
  const nextYear = term?.year ?? (terms[0] ? terms[0].year + 1 : new Date().getFullYear());

  return (
    <form action={formAction} className="space-y-4">
      {term ? <input type="hidden" name="id" value={term.id} /> : null}

      <FormMessage state={state} />

      <div className="grid gap-4 sm:grid-cols-2">
        <div>
          <label className="label" htmlFor={`t-name-${uid}`}>
            Name
          </label>
          <input
            id={`t-name-${uid}`}
            name="name"
            className="input"
            defaultValue={term?.name ?? defaultName(nextYear)}
            placeholder="2026/27"
            maxLength={100}
            required
          />
          <p className="hint">Shown as the heading of that year&rsquo;s team page.</p>
          <FieldError errors={state.errors} name="name" />
        </div>

        <div>
          <label className="label" htmlFor={`t-year-${uid}`}>
            Starting year
          </label>
          <input
            id={`t-year-${uid}`}
            name="year"
            type="number"
            min={1990}
            max={2100}
            className="input"
            defaultValue={nextYear}
            required
          />
          <p className="hint">Teams are listed newest first by this year.</p>
          <FieldError errors={state.errors} name="year" />
        </div>
      </div>

      <div className="grid gap-4 sm:grid-cols-2">
        <div>
          <label className="label" htmlFor={`t-slug-${uid}`}>
            URL slug
          </label>
          <input
            id={`t-slug-${uid}`}
            name="slug"
            className="input"
            defaultValue={term?.slug ?? ""}
            placeholder="leave blank to build it from the name"
            maxLength={100}
          />
          <p className="hint">
            The page address: /team/<span className="text-slate-400">2026-27</span>.
          </p>
          <FieldError errors={state.errors} name="slug" />
        </div>

        {!term ? (
          <div>
            <label className="label" htmlFor={`t-copy-${uid}`}>
              Start from
            </label>
            <select
              id={`t-copy-${uid}`}
              name="copy_from"
              className="input"
              defaultValue={terms[0]?.id ?? ""}
            >
              <option value="">An empty team</option>
              {terms.map((t) => (
                <option key={t.id} value={t.id}>
                  {t.name}&rsquo;s groups and subgroups
                </option>
              ))}
            </select>
            <p className="hint">Copies the structure only. Members are added fresh each year.</p>
            <FieldError errors={state.errors} name="copy_from" />
          </div>
        ) : null}
      </div>

      <div>
        <label className="label" htmlFor={`t-desc-${uid}`}>
          Description <span className="font-normal text-slate-500">(optional)</span>
        </label>
        <input
          id={`t-desc-${uid}`}
          name="description"
          className="input"
          defaultValue={term?.description ?? ""}
          maxLength={300}
        />
        <p className="hint">Shown under the heading of the team page.</p>
        <FieldError errors={state.errors} name="description" />
      </div>

      <label className="flex items-start gap-3 text-sm">
        <input
          type="checkbox"
          name="is_current"
          value="1"
          defaultChecked={term ? term.is_current : true}
          className="mt-0.5"
        />
        <span>
          <span className="font-medium text-slate-200">This is the current team</span>
          <span className="block text-xs text-slate-500">
            Shown at /team. Only one year can be current; ticking this moves the others to
            past teams.
          </span>
        </span>
      </label>

      <label className="flex items-center gap-2 text-sm text-slate-300">
        <input
          type="checkbox"
          name="is_active"
          value="1"
          defaultChecked={term ? term.is_active : true}
        />
        Visible on the site
      </label>

      <SubmitButton>{term ? "Save team year" : "Create team year"}</SubmitButton>
    </form>
  );
}

export function DeleteTermForm({ term }: { term: TeamTermItem }) {
  const [state, action] = useActionState(deleteTermAction, EMPTY_ACTION_STATE);

  return (
    <form action={action} className="space-y-2">
      <input type="hidden" name="id" value={term.id} />
      <ConfirmSubmit
        confirm={`Delete the ${term.name} team? Every group, subgroup and member in it is deleted too. This cannot be undone.`}
      >
        Delete this team year
      </ConfirmSubmit>
      <FormMessage state={state} />
    </form>
  );
}
