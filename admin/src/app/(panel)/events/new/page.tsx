import { BackLink, Card, PageHeader } from "@/components/ui";

import EventForm from "../event-form";

export const dynamic = "force-dynamic";

export default function NewEventPage() {
  return (
    <>
      <BackLink href="/events">Back to events</BackLink>
      <div className="mt-3">
        <PageHeader title="New event" />
      </div>
      <Card>
        <EventForm />
      </Card>
    </>
  );
}
