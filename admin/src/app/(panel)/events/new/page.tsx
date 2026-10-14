import { BackLink, Card, PageHeader } from "@/components/ui";

import EventForm from "../event-form";

export const dynamic = "force-dynamic";

export default async function NewEventPage({
  searchParams,
}: {
  searchParams: Promise<{ past?: string }>;
}) {
  const past = (await searchParams).past === "1";

  return (
    <>
      <BackLink href="/events">Back to events</BackLink>
      <div className="mt-3">
        <PageHeader title={past ? "Add a past event" : "New event"} />
      </div>
      <Card>
        <EventForm past={past} />
      </Card>
    </>
  );
}
