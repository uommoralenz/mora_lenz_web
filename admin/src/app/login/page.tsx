import { redirect } from "next/navigation";

import { currentAdmin } from "@/lib/api";

import LoginForm from "./login-form";

export const dynamic = "force-dynamic";

export default async function LoginPage() {
  // Already signed in? Skip the form.
  const admin = await currentAdmin().catch(() => null);

  if (admin) {
    redirect("/dashboard");
  }

  return (
    <main className="flex min-h-screen items-center justify-center p-6">
      <div className="w-full max-w-sm">
        <div className="mb-8 text-center">
          <h1 className="text-2xl font-semibold tracking-tight text-slate-100">
            Mora Lenz
          </h1>
          <p className="mt-1 text-sm text-slate-500">Admin panel</p>
        </div>

        <div className="card p-6">
          <LoginForm />
        </div>

        <p className="mt-6 text-center text-xs leading-relaxed text-slate-600">
          Accounts are created by the super admin. There is no self sign-up and no
          password reset — ask the super admin if you cannot get in.
        </p>
      </div>
    </main>
  );
}
