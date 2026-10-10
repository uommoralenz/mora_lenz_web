"use client";

import { useActionState } from "react";

import { ConfirmSubmit, SubmitButton } from "@/components/form";
import { EMPTY_ACTION_STATE, type MessageItem } from "@/lib/types";

import { deleteMessageAction, toggleReadAction } from "./actions";

/**
 * The read/delete controls for one message.
 *
 * Both actions go through useActionState so a failure lands in a line under
 * the row instead of throwing out of the server action — which is what used to
 * replace the whole page with the error boundary when an update was refused.
 */
export default function MessageActions({ message }: { message: MessageItem }) {
  const [readState, toggleRead] = useActionState(toggleReadAction, EMPTY_ACTION_STATE);
  const [deleteState, remove] = useActionState(deleteMessageAction, EMPTY_ACTION_STATE);

  // A successful toggle re-renders the list from the server, so only failures
  // are worth showing here; a failed delete leaves the row in place.
  const error = [readState, deleteState].find((state) => !state.ok && state.message);

  return (
    <div className="flex flex-col items-end gap-1">
      <div className="flex items-center gap-1">
        <form action={toggleRead}>
          <input type="hidden" name="id" value={message.id} />
          <input type="hidden" name="is_read" value={message.is_read ? "0" : "1"} />
          <SubmitButton className="btn-secondary" pendingLabel="Working…">
            {message.is_read ? "Mark unread" : "Mark read"}
          </SubmitButton>
        </form>

        <form action={remove}>
          <input type="hidden" name="id" value={message.id} />
          <ConfirmSubmit confirm={`Delete the message from ${message.name}?`}>
            Delete
          </ConfirmSubmit>
        </form>
      </div>

      {error ? (
        <p role="alert" className="error-text text-right">
          {error.message}
        </p>
      ) : null}
    </div>
  );
}
