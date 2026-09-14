"use client";

import { useActionState } from "react";
import { ConfirmSubmit, FormMessage } from "@/components/form";
import { EMPTY_ACTION_STATE } from "@/lib/types";
import { deleteEventAction } from "./actions";

export default function DeleteEventForm({ id, title }: { id: number; title: string }) {
  const [state, action] = useActionState(deleteEventAction, EMPTY_ACTION_STATE);
  return (
    <form action={action} className="max-w-xs space-y-2">
      <input type="hidden" name="id" value={id} />
      <ConfirmSubmit confirm={`Delete "${title}"? This also removes its image and cannot be undone.`}>Delete</ConfirmSubmit>
      <FormMessage state={state} />
    </form>
  );
}
