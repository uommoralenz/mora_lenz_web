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

export interface EventItem {
  id: number;
  title: string;
  slug: string;
  description: string | null;
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
  images: GalleryImageItem[];
  sort_order: number;
  is_active: boolean;
  created_at: string | null;
}

export interface GalleryImageItem {
  id: number;
  image_url: string;
  description: string | null;
  sort_order: number;
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

export interface TeamMemberItem {
  id: number;
  group_id: number;
  subgroup_id: number | null;
  name: string;
  profession: string;
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
