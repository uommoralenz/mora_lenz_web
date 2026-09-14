"use client";

import { useActionState, useState } from "react";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { EMPTY_ACTION_STATE } from "@/lib/types";

import { loginAction } from "./actions";

export default function LoginForm() {
  const [state, formAction] = useActionState(loginAction, EMPTY_ACTION_STATE);

  // React resets an uncontrolled form once a server action finishes, which
  // would wipe the username after a failed attempt — leaving the field empty
  // while it still looks filled in, so the next click does nothing. Keeping
  // the username in state makes it survive. The password is left uncontrolled
  // on purpose: clearing it after a failure is what you want.
  const [username, setUsername] = useState("");

  return (
    <form action={formAction} className="space-y-4">
      <FormMessage state={state} />

      <div>
        <label className="label" htmlFor="username">
          Username
        </label>
        <input
          id="username"
          name="username"
          type="text"
          className="input"
          value={username}
          onChange={(event) => setUsername(event.target.value)}
          autoComplete="username"
          autoCapitalize="none"
          autoCorrect="off"
          required
          autoFocus
        />
        <FieldError errors={state.errors} name="username" />
      </div>

      <div>
        <label className="label" htmlFor="password">
          Password
        </label>
        <input
          id="password"
          name="password"
          type="password"
          className="input"
          autoComplete="current-password"
          required
        />
        <FieldError errors={state.errors} name="password" />
      </div>

      <SubmitButton className="btn-primary w-full" pendingLabel="Signing in…">
        Sign in
      </SubmitButton>
    </form>
  );
}
