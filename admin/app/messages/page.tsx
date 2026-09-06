import { deleteMessageAction, markMessageReadAction } from "@/app/messages/actions";
import { AdminShell } from "@/components/AdminShell";
import { getContactMessages } from "@/lib/data";
import { formatDate } from "@/lib/format";
import { requireAdmin } from "@/lib/auth";

export const runtime = "nodejs";

export default async function MessagesPage() {
  const admin = await requireAdmin();
  const messages = await getContactMessages();

  return (
    <AdminShell
      admin={admin}
      title="Messages"
      description="Review contact form submissions from the PHP website."
    >
      <section className="panel">
        <div className="message-list">
          {messages.length > 0 ? (
            messages.map((message) => (
              <article className={message.read_at ? "message-card read" : "message-card"} key={message.id}>
                <div className="message-main">
                  <div className="message-heading">
                    <div>
                      <span className={message.read_at ? "status-pill read" : "status-pill unread"}>
                        {message.read_at ? "read" : "unread"}
                      </span>
                      <h2>{message.subject}</h2>
                    </div>
                    <time>{formatDate(message.created_at)}</time>
                  </div>
                  <p className="message-sender">
                    {message.name} &lt;{message.email}&gt;
                  </p>
                  <p className="message-body">{message.message}</p>
                </div>

                <div className="row-actions">
                  {!message.read_at ? (
                    <form action={markMessageReadAction}>
                      <input type="hidden" name="id" value={message.id} />
                      <button type="submit" className="button button-muted">
                        Mark Read
                      </button>
                    </form>
                  ) : null}
                  <form action={deleteMessageAction}>
                    <input type="hidden" name="id" value={message.id} />
                    <button type="submit" className="button button-danger">
                      Delete
                    </button>
                  </form>
                </div>
              </article>
            ))
          ) : (
            <p className="empty-state">No messages yet.</p>
          )}
        </div>
      </section>
    </AdminShell>
  );
}
