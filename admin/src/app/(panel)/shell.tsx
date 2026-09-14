"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useState } from "react";

import type { AdminUser } from "@/lib/types";

const NAV = [
  { href: "/dashboard", label: "Dashboard" },
  { href: "/events", label: "Events" },
  { href: "/gallery", label: "Featured gallery" },
  { href: "/services", label: "Services" },
  { href: "/team", label: "Team" },
  { href: "/messages", label: "Messages" },
];

const SUPER_NAV = [{ href: "/admins", label: "Admin accounts" }];

export default function Shell({
  admin,
  logoutAction,
  children,
}: {
  admin: AdminUser;
  logoutAction: () => Promise<void>;
  children: React.ReactNode;
}) {
  const pathname = usePathname();
  const [menuOpen, setMenuOpen] = useState(false);

  const links = admin.is_super_admin ? [...NAV, ...SUPER_NAV] : NAV;

  const isActive = (href: string) =>
    pathname === href || pathname.startsWith(`${href}/`);

  const navLinks = (
    <nav className="space-y-1">
      {links.map((link) => (
        <Link
          key={link.href}
          href={link.href}
          onClick={() => setMenuOpen(false)}
          className={`block rounded-lg px-3 py-2 text-sm transition-colors ${
            isActive(link.href)
              ? "bg-sky-600/15 font-medium text-sky-300"
              : "text-slate-400 hover:bg-ink-800 hover:text-slate-200"
          }`}
        >
          {link.label}
        </Link>
      ))}
    </nav>
  );

  return (
    <div className="min-h-screen lg:flex">
      {/* Mobile header */}
      <header className="flex items-center justify-between border-b border-ink-700 bg-ink-900 px-4 py-3 lg:hidden">
        <span className="font-semibold text-slate-100">Mora Lenz Admin</span>
        <button
          type="button"
          className="btn-secondary px-3 py-1.5"
          aria-expanded={menuOpen}
          onClick={() => setMenuOpen((open) => !open)}
        >
          {menuOpen ? "Close" : "Menu"}
        </button>
      </header>

      {/* Sidebar */}
      <aside
        className={`${
          menuOpen ? "block" : "hidden"
        } border-b border-ink-700 bg-ink-900 p-4 lg:block lg:w-60 lg:shrink-0 lg:border-b-0 lg:border-r`}
      >
        <div className="mb-6 hidden lg:block">
          <p className="font-semibold text-slate-100">Mora Lenz</p>
          <p className="text-xs text-slate-500">Admin panel</p>
        </div>

        {navLinks}

        <div className="mt-6 border-t border-ink-700 pt-4">
          <Link
            href="/account"
            onClick={() => setMenuOpen(false)}
            className="block truncate rounded-lg px-3 py-2 text-sm text-slate-300 hover:bg-ink-800"
          >
            <span className="block truncate font-medium">{admin.name}</span>
            <span className="block truncate text-xs text-slate-500">
              {admin.is_super_admin ? "Super admin" : "Admin"} · @{admin.username}
            </span>
          </Link>

          <form action={logoutAction}>
            <button type="submit" className="btn-ghost mt-1 w-full justify-start px-3">
              Sign out
            </button>
          </form>
        </div>
      </aside>

      <main className="min-w-0 flex-1 p-4 sm:p-6 lg:p-8">
        <div className="mx-auto max-w-5xl">{children}</div>
      </main>
    </div>
  );
}
