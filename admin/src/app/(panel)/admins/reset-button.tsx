"use client";

import { useActionState } from "react";

import { EMPTY_ACTION_STATE } from "@/lib/types";

import { resetAdminPasswordAction } from "./actions";

/** Emails the admin a new temporary password and forces a change at next sign-in. */
export default function ResetPasswordButton({
  id,
  name,
  hasEmail,
}: {
  id: number;
  name: string;
  hasEmail: boolean;
}) {
  const [state, formAction, pending] = useActionState(
    resetAdminPasswordAction,
    EMPTY_ACTION_STATE
  );

  return (
    <form action={formAction} className="space-y-2">
      <input type="hidden" name="id" value={id} />
      <button
        type="submit"
        className="btn-secondary"
        disabled={pending || !hasEmail}
        title={hasEmail ? undefined : "Save an email address for this admin first."}
        onClick={(event) => {
          if (
            !window.confirm(
              `Reset the password for ${name}? A new temporary password is emailed to them, and they are signed out everywhere.`
            )
          ) {
            event.preventDefault();
          }
        }}
      >
        {pending ? "Resetting…" : "Reset password"}
      </button>

      {state.message ? (
        <p
          role={state.ok ? "status" : "alert"}
          className={`max-w-xs text-xs ${state.ok ? "text-emerald-300" : "text-rose-300"}`}
        >
          {state.message}
        </p>
      ) : null}
    </form>
  );
}
