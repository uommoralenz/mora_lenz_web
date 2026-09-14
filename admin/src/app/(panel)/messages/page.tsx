import Link from "next/link";

import { ConfirmSubmit } from "@/components/form";
import { Badge, EmptyState, PageHeader, formatDateTime } from "@/components/ui";
import { api } from "@/lib/api";
import type { MessageItem, MessagesMeta } from "@/lib/types";

import { deleteMessageAction, toggleReadAction } from "./actions";

export const dynamic = "force-dynamic";

export default async function MessagesPage({
  searchParams,
}: {
  searchParams: Promise<{ page?: string; unread?: string }>;
}) {
  const params = await searchParams;
  const page = Math.max(1, Number(params.page ?? 1) || 1);
  const unreadOnly = params.unread === "1";

  const query = new URLSearchParams({ page: String(page), per_page: "25" });
  if (unreadOnly) query.set("unread_only", "1");

  const { data: messages, meta } = await api.get<{
    data: MessageItem[];
    meta: MessagesMeta;
  }>(`/messages?${query.toString()}`);

  return (
    <>
      <PageHeader
        title="Messages"
        description="Everything submitted through the contact form on the public site."
      />

      <div className="mb-4 flex flex-wrap items-center gap-2">
        <Link
          href="/messages"
          className={unreadOnly ? "btn-secondary" : "btn-primary"}
          aria-current={unreadOnly ? undefined : "page"}
        >
          All ({meta.total})
        </Link>
        <Link
          href="/messages?unread=1"
          className={unreadOnly ? "btn-primary" : "btn-secondary"}
          aria-current={unreadOnly ? "page" : undefined}
        >
          Unread ({meta.unread})
        </Link>
      </div>

      {messages.length === 0 ? (
        <EmptyState title={unreadOnly ? "Nothing unread." : "No messages yet."} />
      ) : (
        <ul className="space-y-3">
          {messages.map((message) => (
            <li
              key={message.id}
              className={`card p-4 ${message.is_read ? "" : "border-sky-900/70"}`}
            >
              <div className="flex flex-wrap items-start justify-between gap-3">
                <div className="min-w-0">
                  <div className="flex flex-wrap items-center gap-2">
                    <p className="font-medium text-slate-100">{message.name}</p>
                    <Badge tone="info">{message.subject_label}</Badge>
                    {!message.is_read ? <Badge tone="warning">New</Badge> : null}
                  </div>
                  <p className="mt-0.5 text-xs text-slate-500">
                    <a
                      href={`mailto:${message.email}?subject=Re: ${encodeURIComponent(message.subject_label)}`}
                      className="hover:text-sky-400"
                    >
                      {message.email}
                    </a>
                    {" · "}
                    {formatDateTime(message.created_at)}
                  </p>
                </div>

                <div className="flex items-center gap-1">
                  <form action={toggleReadAction}>
                    <input type="hidden" name="id" value={message.id} />
                    <input
                      type="hidden"
                      name="is_read"
                      value={message.is_read ? "0" : "1"}
                    />
                    <button type="submit" className="btn-secondary">
                      {message.is_read ? "Mark unread" : "Mark read"}
                    </button>
                  </form>

                  <form action={deleteMessageAction}>
                    <input type="hidden" name="id" value={message.id} />
                    <ConfirmSubmit confirm={`Delete the message from ${message.name}?`}>
                      Delete
                    </ConfirmSubmit>
                  </form>
                </div>
              </div>

              <p className="mt-3 whitespace-pre-wrap border-t border-ink-700 pt-3 text-sm leading-relaxed text-slate-300">
                {message.message}
              </p>
            </li>
          ))}
        </ul>
      )}

      {meta.last_page > 1 ? (
        <nav className="mt-6 flex items-center justify-between gap-3">
          {page > 1 ? (
            <Link
              href={`/messages?page=${page - 1}${unreadOnly ? "&unread=1" : ""}`}
              className="btn-secondary"
            >
              ← Newer
            </Link>
          ) : (
            <span />
          )}

          <span className="text-xs text-slate-500">
            Page {meta.current_page} of {meta.last_page}
          </span>

          {page < meta.last_page ? (
            <Link
              href={`/messages?page=${page + 1}${unreadOnly ? "&unread=1" : ""}`}
              className="btn-secondary"
            >
              Older →
            </Link>
          ) : (
            <span />
          )}
        </nav>
      ) : null}
    </>
  );
}
