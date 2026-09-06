import { createMemberAction } from "@/app/members/actions";
import { AdminShell } from "@/components/AdminShell";
import { MemberForm } from "@/components/MemberForm";
import { requireAdmin } from "@/lib/auth";
import { getPanelPillars } from "@/lib/data";

export const runtime = "nodejs";

export default async function NewMemberPage() {
  const admin = await requireAdmin();
  const panelsPillars = await getPanelPillars();

  return (
    <AdminShell admin={admin} title="New Member" description="Add a person to the public team section.">
      <MemberForm action={createMemberAction} panelsPillars={panelsPillars} submitLabel="Create Member" />
    </AdminShell>
  );
}
