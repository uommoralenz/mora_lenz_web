"use client";

import { useFormStatus } from "react-dom";

import type { ActionState } from "@/lib/types";

/** Submit button that disables and relabels itself while the action runs. */
export function SubmitButton({
  children = "Save",
  pendingLabel = "Saving…",
  className = "btn-primary",
}: {
  children?: React.ReactNode;
  pendingLabel?: string;
  className?: string;
}) {
  const { pending } = useFormStatus();

  return (
    <button type="submit" className={className} disabled={pending}>
      {pending ? pendingLabel : children}
    </button>
  );
}

/** A destructive submit that asks first, so a stray click cannot delete data. */
export function ConfirmSubmit({
  children = "Delete",
  confirm = "Delete this permanently? This cannot be undone.",
  className = "btn-danger",
}: {
  children?: React.ReactNode;
  confirm?: string;
  className?: string;
}) {
  const { pending } = useFormStatus();

  return (
    <button
      type="submit"
      className={className}
      disabled={pending}
      onClick={(event) => {
        if (!window.confirm(confirm)) {
          event.preventDefault();
        }
      }}
    >
      {pending ? "Working…" : children}
    </button>
  );
}

/** Success / failure banner driven by the action's returned state. */
export function FormMessage({ state }: { state: ActionState }) {
  if (!state.message) return null;

  return (
    <p
      role={state.ok ? "status" : "alert"}
      className={`rounded-lg border px-3 py-2 text-sm ${
        state.ok
          ? "border-emerald-900/70 bg-emerald-950/40 text-emerald-300"
          : "border-rose-900/70 bg-rose-950/40 text-rose-300"
      }`}
    >
      {state.message}
    </p>
  );
}

export function FieldError({
  errors,
  name,
}: {
  errors?: Record<string, string>;
  name: string;
}) {
  const message = errors?.[name];

  if (!message) return null;

  return <p className="error-text">{message}</p>;
}
