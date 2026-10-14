"use client";

import { useActionState } from "react";

import { SubmitButton } from "@/components/form";
import { EMPTY_ACTION_STATE, type MigrationStatus } from "@/lib/types";

import { runMigrationsAction } from "./actions";

/**
 * Shown only when the database is behind the code.
 *
 * This happens after `laravel/` is uploaded without the migrations being
 * applied, and it is deceptive: every page still reads fine, because a column
 * the database does not have reads as empty, while every save that touches one
 * fails with a 500. So the warning is on the dashboard, where it is seen
 * before someone spends an afternoon wondering why nothing saves.
 */
export default function DatabaseUpdates({ status }: { status: MigrationStatus }) {
  const [state, run] = useActionState(runMigrationsAction, EMPTY_ACTION_STATE);

  // Once they have been applied, the card has nothing left to say — unless
  // this render is the one right after running them, which is worth confirming.
  if (status.ok && !state.message) return null;

  return (
    <div
      className={`card mt-6 p-4 ${
        status.ok ? "border-emerald-900/70" : "border-amber-900/70 bg-amber-950/20"
      }`}
    >
      <h2 className="text-sm font-semibold text-slate-200">Database updates</h2>

      {state.message ? (
        <p
          role={state.ok ? "status" : "alert"}
          className={`mt-2 text-sm ${state.ok ? "text-emerald-300" : "text-rose-300"}`}
        >
          {state.message}
        </p>
      ) : null}

      {!status.ok ? (
        <>
          <p className="mt-2 text-sm text-slate-300">{status.message}</p>

          <p className="mt-2 text-xs text-slate-500">
            The site was updated but its database was not. Pages still load, because
            a missing column reads as empty — but saving an event, a page or a team
            member can fail with a server error until this is run. It is safe: it only
            adds what is missing and never touches content you have added.
          </p>

          {status.pending.length > 0 ? (
            <ul className="mt-3 space-y-1 font-mono text-xs text-slate-400">
              {status.pending.map((name) => (
                <li key={name}>{name}</li>
              ))}
            </ul>
          ) : null}

          <form action={run} className="mt-4">
            <SubmitButton pendingLabel="Updating the database…">
              Run database updates
            </SubmitButton>
          </form>
        </>
      ) : null}
    </div>
  );
}
