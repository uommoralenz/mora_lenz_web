import { redirect } from "next/navigation";

/** Services has a page per type; the parent opens the first one. */
export default function ServicesPage() {
  redirect("/services/photography");
}
