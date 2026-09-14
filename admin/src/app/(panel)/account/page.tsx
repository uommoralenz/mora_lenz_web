import { Badge, Card, PageHeader, SectionTitle, formatDateTime } from "@/components/ui";
import { requireAdmin } from "@/lib/api";

import PasswordForm from "./password-form";

export const dynamic = "force-dynamic";

export default async function AccountPage() {
  const admin = await requireAdmin();

  return (
    <>
      <PageHeader title="Your account" />

      <Card className="mb-6">
        <SectionTitle>Details</SectionTitle>
        <dl className="grid gap-3 text-sm sm:grid-cols-2">
          <div>
            <dt className="text-xs text-slate-500">Name</dt>
            <dd className="text-slate-200">{admin.name}</dd>
          </div>
          <div>
            <dt className="text-xs text-slate-500">Username</dt>
            <dd className="text-slate-200">@{admin.username}</dd>
          </div>
          <div>
            <dt className="text-xs text-slate-500">Email</dt>
            <dd className="text-slate-200">{admin.email || "—"}</dd>
          </div>
          <div>
            <dt className="text-xs text-slate-500">Role</dt>
            <dd>
              {admin.is_super_admin ? (
                <Badge tone="info">Super admin</Badge>
              ) : (
                <Badge>Admin</Badge>
              )}
            </dd>
          </div>
          <div>
            <dt className="text-xs text-slate-500">Last signed in</dt>
            <dd className="text-slate-200">{formatDateTime(admin.last_login_at)}</dd>
          </div>
        </dl>

        <p className="mt-4 text-xs text-slate-500">
          Your name and username can only be changed by a super admin.
        </p>
      </Card>

      <Card>
        <SectionTitle>Change password</SectionTitle>
        <p className="mb-4 text-sm text-slate-400">
          Changing your password signs you out everywhere else. This device stays signed
          in.
        </p>
        <PasswordForm />
      </Card>
    </>
  );
}
