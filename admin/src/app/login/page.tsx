import { redirect } from "next/navigation";

import { currentAdmin } from "@/lib/api";

import LoginForm from "./login-form";

export const dynamic = "force-dynamic";

export default async function LoginPage({ searchParams }: {
  searchParams: Promise<{ reason?: string }>;
}) {
  const { reason } = await searchParams;
  const notice = reason === "session-missing"
    ? "No session cookie was received. If you just signed in, allow cookies for this site and check that you are using the same website address."
    : reason === "session-rejected"
      ? "Your session was rejected by the login server. Please sign in again. If this repeats immediately, the site administrator needs to check API authentication."
      : null;
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
          {notice && <p role="alert" className="mb-4 text-sm text-amber-300">{notice}</p>}
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
