"use client";

import { useActionState, useRef } from "react";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { EMPTY_ACTION_STATE, type ServiceType } from "@/lib/types";

import { addServiceImagesAction } from "./actions";

export default function ImageForm({ serviceType }: { serviceType: ServiceType }) {
  const formRef = useRef<HTMLFormElement>(null);

  const [state, formAction] = useActionState(
    async (prev: typeof EMPTY_ACTION_STATE, data: FormData) => {
      const result = await addServiceImagesAction(prev, data);

      if (result.ok) formRef.current?.reset();

      return result;
    },
    EMPTY_ACTION_STATE
  );

  return (
    <form ref={formRef} action={formAction} className="space-y-4">
      <input type="hidden" name="service_type" value={serviceType} />

      <FormMessage state={state} />

      <div>
        <label className="label" htmlFor={`images-${serviceType}`}>
          Carousel images
        </label>
        <input
          id={`images-${serviceType}`}
          name="images"
          type="file"
          accept="image/*"
          multiple
          className="input file:mr-3 file:rounded file:border-0 file:bg-ink-700 file:px-3 file:py-1 file:text-slate-200"
          required
        />
        <p className="hint">
          Up to 10 at a time. They fade from one to the next every 5 seconds at the top of
          the page.
        </p>
        <FieldError errors={state.errors} name="images" />
        <FieldError errors={state.errors} name="images.0" />
      </div>

      <SubmitButton pendingLabel="Uploading…">Upload</SubmitButton>
    </form>
  );
}
