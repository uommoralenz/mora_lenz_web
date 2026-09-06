import type { EventRecord, MemberRecord } from "@/lib/types";

export function parseImageUrls(value: EventRecord["image_urls"]) {
  if (Array.isArray(value)) {
    return value;
  }

  if (!value) {
    return [];
  }

  try {
    const parsed = JSON.parse(value);

    return Array.isArray(parsed) ? parsed.filter(Boolean) : [];
  } catch {
    return [value].filter(Boolean);
  }
}

export function imageUrlsFromInput(value: FormDataEntryValue | null) {
  return String(value ?? "")
    .split(/\r?\n/)
    .map((item) => item.trim())
    .filter(Boolean);
}

export function formatImageUrlsForTextarea(value: EventRecord["image_urls"]) {
  return parseImageUrls(value).join("\n");
}

export function coverImage(event: EventRecord) {
  return parseImageUrls(event.image_urls)[0] ?? "";
}

export function displayName(member: MemberRecord) {
  return [member.first_name, member.last_name].filter(Boolean).join(" ");
}

export function formatDate(value: Date | string | null) {
  if (!value) {
    return "-";
  }

  return new Intl.DateTimeFormat("en", {
    dateStyle: "medium",
    timeStyle: "short",
  }).format(new Date(value));
}

export function formatDateOnly(value: Date | string | null) {
  if (!value) {
    return "-";
  }

  return new Intl.DateTimeFormat("en", {
    dateStyle: "medium",
  }).format(new Date(value));
}

export function formatDateTimeLocal(value: Date | string | null) {
  if (!value) {
    return "";
  }

  const date = new Date(value);
  const localDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000);

  return localDate.toISOString().slice(0, 16);
}

export function toMysqlDateTime(value: FormDataEntryValue | null) {
  const raw = String(value ?? "").trim();

  if (!raw) {
    return "";
  }

  return raw.length === 16 ? `${raw.replace("T", " ")}:00` : raw.replace("T", " ");
}

export function shortText(value: string | null, length = 110) {
  if (!value) {
    return "";
  }

  return value.length > length ? `${value.slice(0, length - 3)}...` : value;
}
