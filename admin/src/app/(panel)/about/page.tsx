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
        description="/about is a fully custom page: it shows only the content you build here, between the site's navbar and footer."
      />
      <Card>
        <AboutForm page={page} />
      </Card>
    </>
  );
}
