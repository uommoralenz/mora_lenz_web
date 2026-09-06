import Link from "next/link";
import { logoutAction } from "@/app/login/actions";
import type { AdminSession } from "@/lib/types";

type AdminShellProps = {
  admin: AdminSession;
  title: string;
  description?: string;
  action?: React.ReactNode;
  children: React.ReactNode;
};

const navItems = [
  { href: "/dashboard", label: "Dashboard" },
  { href: "/events", label: "Events" },
  { href: "/members", label: "Members" },
  { href: "/panels-pillars", label: "Panels & Pillars" },
  { href: "/gallery", label: "Gallery" },
  { href: "/messages", label: "Messages" },
];

export function AdminShell({ admin, title, description, action, children }: AdminShellProps) {
  const visibleNavItems =
    admin.role === "super_admin" ? [...navItems, { href: "/admins", label: "Admins" }] : navItems;

  return (
    <div className="admin-layout">
      <aside className="sidebar">
        <Link href="/dashboard" className="brand">
          <span className="brand-mark">ML</span>
          <span>
            <strong>Mora Lenz</strong>
            <small>Admin</small>
          </span>
        </Link>

        <nav className="sidebar-nav" aria-label="Admin navigation">
          {visibleNavItems.map((item) => (
            <Link href={item.href} key={item.href}>
              {item.label}
            </Link>
          ))}
        </nav>
      </aside>

      <div className="main-column">
        <header className="topbar">
          <div>
            <span className="eyebrow">Signed in as</span>
            <strong>{admin.name}</strong>
            <span className="role-label">{admin.email} - {admin.role.replace("_", " ")}</span>
          </div>
          <form action={logoutAction}>
            <button type="submit" className="button button-muted">
              Sign Out
            </button>
          </form>
        </header>

        <main className="content">
          <div className="page-header">
            <div>
              <h1>{title}</h1>
              {description ? <p>{description}</p> : null}
            </div>
            {action ? <div className="page-action">{action}</div> : null}
          </div>

          {children}
        </main>
      </div>
    </div>
  );
}
