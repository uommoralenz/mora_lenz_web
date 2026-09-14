import Disclosure from "@/components/disclosure";
import { ConfirmSubmit } from "@/components/form";
import { Badge, Card, PageHeader, formatDateTime } from "@/components/ui";
import { api, requireSuperAdmin } from "@/lib/api";
import type { AdminUser } from "@/lib/types";

import { deleteAdminAction } from "./actions";
import AdminForm from "./admin-form";

export const dynamic = "force-dynamic";

export default async function AdminsPage() {
  const me = await requireSuperAdmin();

  const { data: admins } = await api.get<{ data: AdminUser[] }>("/admins");

  const superAdmins = admins.filter((a) => a.is_super_admin && a.is_active).length;

  return (
    <>
      <PageHeader
        title="Admin accounts"
        description="Only super admins see this page. Accounts are created here and handed over in person — there is no sign-up and no password reset."
      />

      <Card className="mb-6">
        <Disclosure label="New admin" variant="primary">
          <AdminForm currentAdminId={me.id} />
        </Disclosure>
      </Card>

      <ul className="space-y-3">
        {admins.map((admin) => {
          const isSelf = admin.id === me.id;
          const isLastSuper = admin.is_super_admin && admin.is_active && superAdmins <= 1;

          return (
            <li key={admin.id} className="card p-4">
              <div className="flex flex-wrap items-start justify-between gap-3">
                <div className="min-w-0">
                  <div className="flex flex-wrap items-center gap-2">
                    <p className="font-medium text-slate-100">{admin.name}</p>
                    {admin.is_super_admin ? (
                      <Badge tone="info">Super admin</Badge>
                    ) : null}
                    {isSelf ? <Badge>You</Badge> : null}
                    {!admin.is_active ? <Badge tone="danger">Deactivated</Badge> : null}
                  </div>
                  <p className="mt-0.5 text-xs text-slate-500">
                    @{admin.username}
                    {admin.email ? ` · ${admin.email}` : ""}
                  </p>
                  <p className="text-xs text-slate-600">
                    Last signed in {formatDateTime(admin.last_login_at)}
                  </p>
                </div>

                {!isSelf && !isLastSuper ? (
                  <form action={deleteAdminAction}>
                    <input type="hidden" name="id" value={admin.id} />
                    <ConfirmSubmit
                      confirm={`Delete the account for ${admin.name}? They are signed out immediately and this cannot be undone.`}
                    >
                      Delete
                    </ConfirmSubmit>
                  </form>
                ) : null}
              </div>

              {isLastSuper ? (
                <p className="mt-3 rounded-lg border border-amber-900/60 bg-amber-950/30 px-3 py-2 text-xs text-amber-300">
                  This is the only active super admin, so it cannot be deleted, demoted or
                  deactivated. Promote a second super admin first if you need to change it.
                </p>
              ) : null}

              <div className="mt-4 border-t border-ink-700 pt-4">
                <Disclosure label="Edit" variant="ghost">
                  <AdminForm admin={admin} currentAdminId={me.id} />
                </Disclosure>
              </div>
            </li>
          );
        })}
      </ul>
    </>
  );
}
