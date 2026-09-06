export type EventStatus = "upcoming" | "past";
export type CardSize = "sm" | "md" | "lg";
export type AdminRole = "super_admin" | "admin";
export type PanelPillarType = "panel" | "pillar";

export type AdminSession = {
  id: string;
  name: string;
  email: string;
  role: AdminRole;
};

export type AdminUserRecord = AdminSession & {
  password_hash: string;
  is_active: boolean | number;
  last_login_at: Date | string | null;
  created_at: Date | string | null;
  updated_at: Date | string | null;
};

export type EventRecord = {
  id: string;
  title: string;
  description: string;
  event_date: Date | string;
  location: string | null;
  image_urls: string[] | string | null;
  status: EventStatus;
  sort_order: number;
  created_at: Date | string | null;
  updated_at: Date | string | null;
};

export type MemberRecord = {
  id: string;
  first_name: string;
  last_name: string | null;
  pillar_or_panel: string | null;
  position: string;
  bio: string | null;
  photo_url: string | null;
  card_size: CardSize;
  sort_order: number;
  created_at: Date | string | null;
  updated_at: Date | string | null;
};

export type PanelPillarRecord = {
  id: string;
  type: PanelPillarType;
  icon: string;
  name: string;
  description: string | null;
  member_count: number;
  sort_order: number;
  created_at: Date | string | null;
  updated_at: Date | string | null;
};

export type GalleryImageRecord = {
  id: string;
  title: string;
  category: string | null;
  image_url: string;
  is_active: boolean | number;
  sort_order: number;
  created_at: Date | string | null;
  updated_at: Date | string | null;
};

export type ContactMessageRecord = {
  id: number;
  name: string;
  email: string;
  subject: string;
  message: string;
  read_at: Date | string | null;
  created_at: Date | string | null;
  updated_at: Date | string | null;
};

export type DashboardStats = {
  eventsCount: number;
  upcomingEventsCount: number;
  membersCount: number;
  panelsPillarsCount: number;
  galleryImagesCount: number;
  unreadMessagesCount: number;
  adminsCount: number;
};
