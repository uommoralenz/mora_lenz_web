export type ServiceType = "photography" | "videography";

/** Sections a non-super admin can be given. Mirrors the PHP Admin::PERMISSIONS. */
export type Permission = "events" | "pages" | "services" | "team" | "messages";

export const PERMISSIONS: { value: Permission; label: string; hint: string }[] = [
  { value: "events", label: "Events", hint: "Create, edit and delete events, their pages and their photo albums." },
  { value: "pages", label: "About page", hint: "Edit the content of the About page." },
  { value: "services", label: "Services", hint: "Photography and videography packages and carousel images." },
  { value: "team", label: "Team", hint: "Team groups, subgroups and members." },
  { value: "messages", label: "Messages", hint: "Read and delete messages from the contact form." },
];

export interface AdminUser {
  id: number;
  name: string;
  username: string;
  email: string | null;
  is_super_admin: boolean;
  /** Sections this admin can open. Always everything for a super admin. */
  permissions: Permission[];
  is_active: boolean;
  /** Still on a temporary password that was emailed to them. */
  must_change_password?: boolean;
  last_login_at: string | null;
  created_at?: string | null;
}

export type EventBlock =
  | { type: "heading"; text: string; level: 2 | 3 | 4; align: "left" | "center" }
  | {
      type: "text";
      text: string;
      align: "left" | "center" | "right";
      size: "normal" | "lead";
    }
  | { type: "quote"; text: string; cite: string }
  | { type: "list"; style: "bullet" | "number" | "check"; items: string[] }
  | { type: "image"; url: string; caption: string; width: "full" | "wide" | "narrow" }
  | { type: "gallery"; urls: string[]; caption: string }
  | { type: "button"; label: string; url: string; style: "primary" | "outline" }
  | { type: "video"; url: string }
  | { type: "callout"; title: string; text: string; tone: "info" | "warn" | "success" }
  /**
   * Admin-authored markup, printed as given: HTML, <style> and <script> in the
   * one field. "inline" drops it into the page, "frame" isolates it in a
   * sandboxed iframe (height 0 measures the content, anything else fixes it).
   */
  | {
      type: "html";
      code: string;
      width: "normal" | "wide" | "full";
      mode: "inline" | "frame";
      height: number;
    }
  | { type: "spacer"; size: "small" | "medium" | "large" }
  | { type: "divider" };

export const EVENT_HTML_MAX = 60000;
export const EVENT_PAGE_CODE_MAX = 20000;

/** How the page around the content blocks is laid out. */
export interface EventPageOptions {
  width: "normal" | "wide" | "full";
  hero: "photo" | "compact" | "plain";
  show_meta: boolean;
  /** Page-wide markup printed at the end of <head>: CSS, fonts, scripts. */
  custom_code: string;
}

export const EVENT_PAGE_DEFAULTS: EventPageOptions = {
  width: "normal",
  hero: "photo",
  show_meta: true,
  custom_code: "",
};

export interface EventItem {
  id: number;
  title: string;
  slug: string;
  description: string | null;
  content: EventBlock[];
  page_options: EventPageOptions;
  event_date: string;
  end_date: string | null;
  location: string | null;
  image_url: string | null;
  /** Facebook albums with the event's photos. */
  facebook_album_urls: string[];
  photo_count: number;
  /** Its end (or start) has passed; it shows under Past Events. */
  is_past: boolean;
  countdown_enabled: boolean;
  is_featured: boolean;
  is_active: boolean;
  sort_order: number;
  created_at: string | null;
}

export interface EventPhoto {
  id: number;
  image_url: string;
  caption: string | null;
}

/** A free-form page (the About page), built like an event page. */
export interface PageItem {
  slug: string;
  title: string;
  description: string | null;
  content: EventBlock[];
  /** show_teams: add the list of every year's team under the content. */
  page_options: EventPageOptions & { show_teams?: boolean };
  image_url: string | null;
  updated_at: string | null;
}

export interface ServicePackageItem {
  id: number;
  service_type: ServiceType;
  name: string;
  description: string[];
  price: number;
  offered_price: number | null;
  image_url: string | null;
  sort_order: number;
  is_active: boolean;
  created_at: string | null;
}

export interface ServiceImageItem {
  id: number;
  service_type: ServiceType;
  image_url: string;
  caption: string | null;
  sort_order: number;
  is_active: boolean;
}

/** Rank inside a group: 1 the top role, 2 the office-bearers, 3 everyone else. */
export type TeamTier = 1 | 2 | 3;

export const TEAM_TIERS: { value: TeamTier; label: string; hint: string }[] = [
  { value: 1, label: "Top role", hint: "Shown alone at the top, in the largest card." },
  { value: 2, label: "Office-bearer", hint: "Shown in a row under the top role." },
  { value: 3, label: "Member", hint: "Shown in the main grid with everyone else." },
];

export interface TeamMemberItem {
  id: number;
  group_id: number;
  subgroup_id: number | null;
  name: string;
  profession: string;
  tier: TeamTier;
  description: string | null;
  image_url: string | null;
  sort_order: number;
  is_active: boolean;
}

export interface TeamSubgroupItem {
  id: number;
  group_id: number;
  name: string;
  description: string | null;
  sort_order: number;
  is_active: boolean;
  members: TeamMemberItem[];
}

/** One year's team. Each has its own page on the site. */
export interface TeamTermItem {
  id: number;
  name: string;
  slug: string;
  year: number;
  description: string | null;
  is_current: boolean;
  is_active: boolean;
  group_count: number;
  member_count: number;
}

export interface TeamGroupItem {
  id: number;
  term_id: number;
  name: string;
  description: string | null;
  sort_order: number;
  is_active: boolean;
  direct_members: TeamMemberItem[];
  subgroups: TeamSubgroupItem[];
}

export interface MessageItem {
  id: number;
  name: string;
  email: string;
  subject: string;
  subject_label: string;
  message: string;
  is_read: boolean;
  created_at: string | null;
}

export interface MessagesMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  unread: number;
}

export interface Stats {
  events: number;
  events_active: number;
  events_upcoming: number;
  events_past: number;
  event_photos: number;
  service_packages: number;
  team_groups: number;
  team_members: number;
  team_terms: number;
  admins: number;
  messages: number;
  messages_unread: number;
}

/**
 * Whether the database has had every update that shipped with the code.
 * Super admin only; see App\Http\Controllers\Admin\SystemController.
 */
export interface MigrationStatus {
  ok: boolean;
  pending: string[];
  message: string;
}

/** Shape returned by every server action, consumed by useActionState. */
export interface ActionState {
  ok: boolean;
  message: string;
  /** Field name -> first validation error, straight from Laravel. */
  errors?: Record<string, string>;
}

export const EMPTY_ACTION_STATE: ActionState = { ok: false, message: "" };
