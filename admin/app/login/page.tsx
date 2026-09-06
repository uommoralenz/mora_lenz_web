import { getCurrentAdmin } from "@/lib/auth";
import { getAdminUsersCount } from "@/lib/data";
import { loginAction } from "@/app/login/actions";
import { redirect } from "next/navigation";

type LoginPageProps = {
  searchParams: Promise<{
    error?: string;
  }>;
};

export default async function LoginPage({ searchParams }: LoginPageProps) {
  const [admin, adminCount, params] = await Promise.all([getCurrentAdmin(), getAdminUsersCount(), searchParams]);

  if (admin) {
    redirect("/dashboard");
  }

  if (adminCount === 0) {
    return (
      <main className="login-page">
        <section className="login-panel">
          <h1>Mora Lenz Admin</h1>
          <p>Admin access has not been configured yet. Contact the site administrator.</p>
        </section>
      </main>
    );
  }

  return (
    <main className="login-page">
      <section className="login-panel">
        <div className="login-brand">
          <span className="brand-mark">ML</span>
          <div>
            <h1>Mora Lenz Admin</h1>
            <p>Manage public website content.</p>
          </div>
        </div>

        {params.error === "invalid" ? (
          <div className="alert alert-error">Invalid email or password.</div>
        ) : null}

        {params.error === "rate-limit" ? (
          <div className="alert alert-error">Too many sign-in attempts. Please try again in 15 minutes.</div>
        ) : null}

        <form action={loginAction} className="login-form">
          <label>
            <span>Email</span>
            <input type="email" name="email" placeholder="admin@moralenz.com" required />
          </label>

          <label>
            <span>Password</span>
            <input type="password" name="password" required />
          </label>

          <button type="submit" className="button button-primary">
            Sign In
          </button>
        </form>
      </section>
    </main>
  );
}

