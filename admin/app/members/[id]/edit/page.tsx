import { updateMemberAction } from "@/app/members/actions";
import { AdminShell } from "@/components/AdminShell";
import { MemberForm } from "@/components/MemberForm";
import { getMember, getPanelPillars } from "@/lib/data";
import { requireAdmin } from "@/lib/auth";
import { notFound } from "next/navigation";

export const runtime = "nodejs";

type EditMemberPageProps = {
  params: Promise<{
    id: string;
  }>;
};

export default async function EditMemberPage({ params }: EditMemberPageProps) {
  const admin = await requireAdmin();
  const { id } = await params;
  const [member, panelsPillars] = await Promise.all([getMember(id), getPanelPillars()]);

  if (!member) {
    notFound();
  }

  return (
    <AdminShell admin={admin} title="Edit Member" description="Update team information shown on the PHP website.">
      <MemberForm action={updateMemberAction} member={member} panelsPillars={panelsPillars} submitLabel="Save Member" />
    </AdminShell>
  );
}
