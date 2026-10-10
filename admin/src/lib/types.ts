export type ServiceType = "photography" | "videography";

export interface AdminUser {
  id: number;
  name: string;
  username: string;
  email: string | null;
  is_super_admin: boolean;
  is_active: boolean;
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
  countdown_enabled: boolean;
  is_featured: boolean;
  is_active: boolean;
  sort_order: number;
  created_at: string | null;
}

export interface GalleryItem {
  show_on_homepage: boolean;
  id: number;
  title: string;
  description: string | null;
  facebook_album_url: string | null;
  image_url: string | null;
  image_count: number;
  sort_order: number;
  is_active: boolean;
  created_at: string | null;
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

export interface TeamGroupItem {
  id: number;
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
  galleries: number;
  service_packages: number;
  team_groups: number;
  team_members: number;
  admins: number;
  messages: number;
  messages_unread: number;
}

/** Shape returned by every server action, consumed by useActionState. */
export interface ActionState {
  ok: boolean;
  message: string;
  /** Field name -> first validation error, straight from Laravel. */
  errors?: Record<string, string>;
}

export const EMPTY_ACTION_STATE: ActionState = { ok: false, message: "" };
