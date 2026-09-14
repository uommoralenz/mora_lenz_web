"use client";

import { useActionState, useRef } from "react";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { Thumb } from "@/components/ui";
import {
  EMPTY_ACTION_STATE,
  type ServicePackageItem,
  type ServiceType,
} from "@/lib/types";

import { savePackageAction } from "./actions";

export default function PackageForm({
  serviceType,
  pkg,
}: {
  serviceType: ServiceType;
  pkg?: ServicePackageItem;
}) {
  const formRef = useRef<HTMLFormElement>(null);
  const uid = pkg?.id ?? `new-${serviceType}`;

  const [state, formAction] = useActionState(
    async (prev: typeof EMPTY_ACTION_STATE, data: FormData) => {
      const result = await savePackageAction(prev, data);

      if (result.ok && !pkg) {
        formRef.current?.reset();
      }

      return result;
    },
    EMPTY_ACTION_STATE
  );

  return (
    <form ref={formRef} action={formAction} className="space-y-4">
      {pkg ? <input type="hidden" name="id" value={pkg.id} /> : null}
      <input type="hidden" name="service_type" value={pkg?.service_type ?? serviceType} />

      <FormMessage state={state} />

      <div>
        <label className="label" htmlFor={`name-${uid}`}>
          Package name
        </label>
        <input
          id={`name-${uid}`}
          name="name"
          className="input"
          defaultValue={pkg?.name ?? ""}
          maxLength={200}
          required
        />
        <FieldError errors={state.errors} name="name" />
      </div>

      <div className="grid gap-4 sm:grid-cols-2">
        <div>
          <label className="label" htmlFor={`price-${uid}`}>
            Price (LKR)
          </label>
          <input
            id={`price-${uid}`}
            name="price"
            type="number"
            min="0"
            step="0.01"
            className="input"
            defaultValue={pkg?.price ?? ""}
            required
          />
          <FieldError errors={state.errors} name="price" />
        </div>

        <div>
          <label className="label" htmlFor={`offered_price-${uid}`}>
            Offer price <span className="font-normal text-slate-500">(optional)</span>
          </label>
          <input
            id={`offered_price-${uid}`}
            name="offered_price"
            type="number"
            min="0"
            step="0.01"
            className="input"
            defaultValue={pkg?.offered_price ?? ""}
          />
          <p className="hint">
            Must be lower than the price. Shows a &ldquo;Special Offer&rdquo; badge.
          </p>
          <FieldError errors={state.errors} name="offered_price" />
        </div>
      </div>

      <div>
        <label className="label" htmlFor={`description-${uid}`}>
          What&rsquo;s included
        </label>
        <textarea
          id={`description-${uid}`}
          name="description"
          className="textarea"
          defaultValue={(pkg?.description ?? []).join("\n")}
          placeholder={"Up to 4 hours of coverage\nTwo photographers on site\n150+ edited images"}
        />
        <p className="hint">One bullet point per line.</p>
        <FieldError errors={state.errors} name="description" />
      </div>

      <div>
        <label className="label" htmlFor={`image-${uid}`}>
          Package image
        </label>
        {pkg?.image_url ? (
          <div className="mb-2 flex items-center gap-3">
            <Thumb src={pkg.image_url} alt={pkg.name} className="h-20 w-32" />
            <p className="text-xs text-slate-500">
              Current image. Choose a new file to replace it.
            </p>
          </div>
        ) : null}
        <input
          id={`image-${uid}`}
          name="image"
          type="file"
          accept="image/*"
          className="input file:mr-3 file:rounded file:border-0 file:bg-ink-700 file:px-3 file:py-1 file:text-slate-200"
          required={!pkg}
        />
        <FieldError errors={state.errors} name="image" />
      </div>

      <label className="flex items-center gap-2 text-sm text-slate-300">
        <input
          type="checkbox"
          name="is_active"
          value="1"
          defaultChecked={pkg ? pkg.is_active : true}
        />
        Visible on the site
      </label>

      <SubmitButton>{pkg ? "Save changes" : "Add package"}</SubmitButton>
    </form>
  );
}
