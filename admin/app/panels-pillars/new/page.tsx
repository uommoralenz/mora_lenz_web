import { createPanelPillarAction } from "@/app/panels-pillars/actions";
import { AdminShell } from "@/components/AdminShell";
import { PanelPillarForm } from "@/components/PanelPillarForm";
import { requireAdmin } from "@/lib/auth";

export const runtime = "nodejs";

export default async function NewPanelPillarPage() {
  const admin = await requireAdmin();

  return (
    <AdminShell admin={admin} title="New Panel Or Pillar" description="Add a public panel or pillar card.">
      <PanelPillarForm action={createPanelPillarAction} submitLabel="Create Item" />
    </AdminShell>
  );
}
