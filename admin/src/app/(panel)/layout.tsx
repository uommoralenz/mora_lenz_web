import { redirect } from "next/navigation";

import { requireAdmin } from "@/lib/api";

import Shell from "./shell";
import { logoutAction } from "./actions";

export const dynamic = "force-dynamic";

export default async function PanelLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  // One auth check for every page under (panel) — each page still calls
  // requireAdmin() itself when it needs the admin object.
  const admin = await requireAdmin();

  // A temporary (emailed) password must be replaced before anything else.
  if (admin.must_change_password) {
    redirect("/first-login");
  }

  return (
    <Shell admin={admin} logoutAction={logoutAction}>
      {children}
    </Shell>
  );
}
