import { updatePanelPillarAction } from "@/app/panels-pillars/actions";
import { AdminShell } from "@/components/AdminShell";
import { PanelPillarForm } from "@/components/PanelPillarForm";
import { requireAdmin } from "@/lib/auth";
import { getPanelPillar } from "@/lib/data";
import { notFound } from "next/navigation";

export const runtime = "nodejs";

type EditPanelPillarPageProps = {
  params: Promise<{
    id: string;
  }>;
};

export default async function EditPanelPillarPage({ params }: EditPanelPillarPageProps) {
  const admin = await requireAdmin();
  const { id } = await params;
  const panelPillar = await getPanelPillar(id);

  if (!panelPillar) {
    notFound();
  }

  return (
    <AdminShell admin={admin} title="Edit Panel Or Pillar" description="Update the public panel or pillar card.">
      <PanelPillarForm action={updatePanelPillarAction} panelPillar={panelPillar} submitLabel="Save Item" />
    </AdminShell>
  );
}
