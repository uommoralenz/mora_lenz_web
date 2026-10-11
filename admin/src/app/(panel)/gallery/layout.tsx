import { requirePermission } from "@/lib/api";

export default async function Layout({ children }: { children: React.ReactNode }) {
  await requirePermission("gallery");

  return children;
}
