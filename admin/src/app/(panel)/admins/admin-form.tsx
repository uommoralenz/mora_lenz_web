"use client";

import { useActionState, useRef } from "react";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { EMPTY_ACTION_STATE, type AdminUser } from "@/lib/types";

import { saveAdminAction } from "./actions";

export default function AdminForm({
  admin,
  currentAdminId,
}: {
  admin?: AdminUser;
  currentAdminId: number;
}) {
  const formRef = useRef<HTMLFormElement>(null);
  const uid = admin?.id ?? "new";
  const isSelf = admin?.id === currentAdminId;

  const [state, formAction] = useActionState(
    async (prev: typeof EMPTY_ACTION_STATE, data: FormData) => {
      const result = await saveAdminAction(prev, data);
      if (result.ok && !admin) formRef.current?.reset();
      return result;
    },
    EMPTY_ACTION_STATE
  );

  return (
    <form ref={formRef} action={formAction} className="space-y-4">
      {admin ? <input type="hidden" name="id" value={admin.id} /> : null}

      <FormMessage state={state} />

      <div className="grid gap-4 sm:grid-cols-2">
        <div>
          <label className="label" htmlFor={`a-name-${uid}`}>
            Full name
          </label>
          <input
            id={`a-name-${uid}`}
            name="name"
            className="input"
            defaultValue={admin?.name ?? ""}
            maxLength={120}
            required
          />
          <FieldError errors={state.errors} name="name" />
        </div>

        <div>
          <label className="label" htmlFor={`a-username-${uid}`}>
            Username
          </label>
          <input
            id={`a-username-${uid}`}
            name="username"
            className="input"
            defaultValue={admin?.username ?? ""}
            autoCapitalize="none"
            autoCorrect="off"
            minLength={3}
            maxLength={60}
            pattern="[A-Za-z0-9_\-]+"
            required
          />
          <p className="hint">Letters, numbers, dashes and underscores.</p>
          <FieldError errors={state.errors} name="username" />
        </div>
      </div>

      <div>
        <label className="label" htmlFor={`a-email-${uid}`}>
          Email <span className="font-normal text-slate-500">(optional)</span>
        </label>
        <input
          id={`a-email-${uid}`}
          name="email"
          type="email"
          className="input"
          defaultValue={admin?.email ?? ""}
          maxLength={180}
        />
        <p className="hint">For your records only — it is never used to sign in.</p>
        <FieldError errors={state.errors} name="email" />
      </div>

      <div>
        <label className="label" htmlFor={`a-password-${uid}`}>
          Password{" "}
          {admin ? (
            <span className="font-normal text-slate-500">(leave blank to keep)</span>
          ) : null}
        </label>
        <input
          id={`a-password-${uid}`}
          name="password"
          type="text"
          className="input font-mono"
          autoComplete="off"
          minLength={10}
          maxLength={200}
          required={!admin}
          placeholder={admin ? "unchanged" : "at least 10 characters"}
        />
        <p className="hint">
          Shown in plain text so you can copy it before handing it over. There is no
          password reset — a forgotten password has to be set again here.
        </p>
        <FieldError errors={state.errors} name="password" />
      </div>

      <fieldset className="space-y-3 rounded-lg border border-ink-700 p-4">
        <legend className="px-1 text-xs uppercase tracking-wide text-slate-500">
          Access
        </legend>

        <label className="flex items-start gap-3 text-sm">
          <input
            type="checkbox"
            name="is_active"
            value="1"
            defaultChecked={admin ? admin.is_active : true}
            disabled={isSelf}
            className="mt-0.5"
          />
          <span>
            <span className="font-medium text-slate-200">Can sign in</span>
            <span className="block text-xs text-slate-500">
              {isSelf
                ? "You cannot deactivate your own account."
                : "Unticking signs them out immediately."}
            </span>
          </span>
        </label>

        <label className="flex items-start gap-3 text-sm">
          <input
            type="checkbox"
            name="is_super_admin"
            value="1"
            defaultChecked={admin?.is_super_admin ?? false}
            disabled={isSelf}
            className="mt-0.5"
          />
          <span>
            <span className="font-medium text-slate-200">Super admin</span>
            <span className="block text-xs text-slate-500">
              {isSelf
                ? "You cannot remove your own super admin rights."
                : "Can create, edit and delete admin accounts."}
            </span>
          </span>
        </label>

        {isSelf ? (
          <>
            {/* Disabled inputs submit nothing, so preserve the current values. */}
            <input type="hidden" name="is_active" value="1" />
            <input type="hidden" name="is_super_admin" value="1" />
          </>
        ) : null}
      </fieldset>

      <SubmitButton>{admin ? "Save changes" : "Create admin"}</SubmitButton>
    </form>
  );
}
