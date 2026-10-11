import { redirect } from "next/navigation";

import { requireAdmin } from "@/lib/api";

import FirstLoginForm from "./first-login-form";

export const dynamic = "force-dynamic";

export default async function FirstLoginPage() {
  const admin = await requireAdmin();

  // Nothing to do once the temporary password has been replaced.
  if (!admin.must_change_password) {
    redirect("/dashboard");
  }

  return (
    <main className="flex min-h-screen items-center justify-center p-6">
      <div className="w-full max-w-sm">
        <div className="mb-8 text-center">
          <h1 className="text-2xl font-semibold tracking-tight text-slate-100">Mora Lenz</h1>
          <p className="mt-1 text-sm text-slate-500">Choose your own password</p>
        </div>

        <div className="card p-6">
          <p className="mb-4 text-sm text-slate-400">
            Hello {admin.name.split(" ")[0]}. You signed in with a temporary password.
            Choose a new one to continue — you will not be able to use the panel until
            you do.
          </p>
          <FirstLoginForm />
        </div>
      </div>
    </main>
  );
}
