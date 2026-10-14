import { Card, PageHeader } from "@/components/ui";
import { api } from "@/lib/api";
import type { PageItem } from "@/lib/types";

import AboutForm from "./about-form";

export const dynamic = "force-dynamic";

export default async function AboutPage() {
  const { data: page } = await api.get<{ data: PageItem }>("/pages/about");

  return (
    <>
      <PageHeader
        title="About page"
        description="Everything on /about is edited here. Below your content, the site lists every year's team automatically."
      />
      <Card>
        <AboutForm page={page} />
      </Card>
    </>
  );
}
