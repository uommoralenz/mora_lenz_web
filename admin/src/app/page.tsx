import { redirect } from "next/navigation";

import { currentAdmin } from "@/lib/api";

export const dynamic = "force-dynamic";

export default async function IndexPage() {
  const admin = await currentAdmin().catch(() => null);

  redirect(admin ? "/dashboard" : "/login");
}
