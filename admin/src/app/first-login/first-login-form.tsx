"use client";

import { useRouter } from "next/navigation";
import { useActionState, useEffect } from "react";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { EMPTY_ACTION_STATE } from "@/lib/types";

import { changePasswordAction } from "../(panel)/account/actions";

export default function FirstLoginForm() {
  const router = useRouter();
  const [state, formAction] = useActionState(changePasswordAction, EMPTY_ACTION_STATE);

  useEffect(() => {
    if (state.ok) router.replace("/dashboard");
  }, [state.ok, router]);

  return (
    <form action={formAction} className="space-y-4">
      <FormMessage state={state} />

      <div>
        <label className="label" htmlFor="current_password">
          Temporary password (from the email)
        </label>
        <input
          id="current_password"
          name="current_password"
          type="password"
          className="input"
          autoComplete="current-password"
          required
        />
        <FieldError errors={state.errors} name="current_password" />
      </div>

      <div>
        <label className="label" htmlFor="password">
          New password
        </label>
        <input
          id="password"
          name="password"
          type="password"
          className="input"
          autoComplete="new-password"
          minLength={10}
          maxLength={200}
          required
        />
        <p className="hint">At least 10 characters, and different from the temporary one.</p>
        <FieldError errors={state.errors} name="password" />
      </div>

      <div>
        <label className="label" htmlFor="password_confirmation">
          Confirm new password
        </label>
        <input
          id="password_confirmation"
          name="password_confirmation"
          type="password"
          className="input"
          autoComplete="new-password"
          minLength={10}
          required
        />
      </div>

      <SubmitButton className="btn-primary w-full" pendingLabel="Saving…">
        Save and continue
      </SubmitButton>
    </form>
  );
}
