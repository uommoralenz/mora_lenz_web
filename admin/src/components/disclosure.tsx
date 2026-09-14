"use client";

import { useState } from "react";

/**
 * Show/hide wrapper used for inline "Add…" and "Edit" forms, so the panel needs
 * no modal library and every form stays a plain HTML form.
 */
export default function Disclosure({
  label,
  openLabel,
  children,
  variant = "secondary",
  defaultOpen = false,
}: {
  label: string;
  openLabel?: string;
  children: React.ReactNode;
  variant?: "primary" | "secondary" | "ghost";
  defaultOpen?: boolean;
}) {
  const [open, setOpen] = useState(defaultOpen);

  const className =
    variant === "primary"
      ? "btn-primary"
      : variant === "ghost"
        ? "btn-ghost"
        : "btn-secondary";

  return (
    <div>
      <button
        type="button"
        className={className}
        aria-expanded={open}
        onClick={() => setOpen((value) => !value)}
      >
        {open ? (openLabel ?? "Cancel") : label}
      </button>

      {open ? <div className="mt-4">{children}</div> : null}
    </div>
  );
}
