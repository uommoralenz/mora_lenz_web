"use client";

import { useActionState, useRef } from "react";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { EMPTY_ACTION_STATE } from "@/lib/types";

import { changePasswordAction } from "./actions";

export default function PasswordForm() {
  const formRef = useRef<HTMLFormElement>(null);

  const [state, formAction] = useActionState(
    async (prev: typeof EMPTY_ACTION_STATE, data: FormData) => {
      const result = await changePasswordAction(prev, data);
      if (result.ok) formRef.current?.reset();
      return result;
    },
    EMPTY_ACTION_STATE
  );

  return (
    <form ref={formRef} action={formAction} className="max-w-md space-y-4">
      <FormMessage state={state} />

      <div>
        <label className="label" htmlFor="current_password">
          Current password
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
        <p className="hint">At least 10 characters.</p>
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

      <SubmitButton>Change password</SubmitButton>
    </form>
  );
}
