import "server-only";

import { queryRows } from "@/lib/db";
import type {
  AdminUserRecord,
  ContactMessageRecord,
  DashboardStats,
  EventRecord,
  GalleryImageRecord,
  MemberRecord,
  PanelPillarRecord,
} from "@/lib/types";

export async function getDashboardStats(): Promise<DashboardStats> {
  const [stats] = await queryRows<DashboardStats>(`
    SELECT
      (SELECT COUNT(*) FROM events) AS eventsCount,
      (SELECT COUNT(*) FROM events WHERE status = 'upcoming') AS upcomingEventsCount,
      (SELECT COUNT(*) FROM members) AS membersCount,
      (SELECT COUNT(*) FROM panels_pillars) AS panelsPillarsCount,
      (SELECT COUNT(*) FROM gallery_images) AS galleryImagesCount,
      (SELECT COUNT(*) FROM contact_messages WHERE read_at IS NULL) AS unreadMessagesCount,
      (SELECT COUNT(*) FROM admin_users WHERE is_active = 1) AS adminsCount
  `);

  return stats;
}

export function getRecentMessages() {
  return queryRows<ContactMessageRecord>(`
    SELECT id, name, email, subject, message, read_at, created_at, updated_at
    FROM contact_messages
    ORDER BY created_at DESC
    LIMIT 5
  `);
}

export function getEvents() {
  return queryRows<EventRecord>(`
    SELECT id, title, description, event_date, location, image_urls, status, sort_order, created_at, updated_at
    FROM events
    ORDER BY status = 'past', sort_order ASC, event_date ASC
  `);
}

export async function getEvent(id: string) {
  const [event] = await queryRows<EventRecord>(
    `
      SELECT id, title, description, event_date, location, image_urls, status, sort_order, created_at, updated_at
      FROM events
      WHERE id = ?
      LIMIT 1
    `,
    [id],
  );

  return event ?? null;
}

export function getMembers() {
  return queryRows<MemberRecord>(`
    SELECT id, first_name, last_name, pillar_or_panel, position, bio, photo_url, card_size, sort_order, created_at, updated_at
    FROM members
    ORDER BY sort_order ASC, first_name ASC
  `);
}

export async function getMember(id: string) {
  const [member] = await queryRows<MemberRecord>(
    `
      SELECT id, first_name, last_name, pillar_or_panel, position, bio, photo_url, card_size, sort_order, created_at, updated_at
      FROM members
      WHERE id = ?
      LIMIT 1
    `,
    [id],
  );

  return member ?? null;
}

export function getPanelPillars() {
  return queryRows<PanelPillarRecord>(`
    SELECT id, type, icon, name, description, member_count, sort_order, created_at, updated_at
    FROM panels_pillars
    ORDER BY sort_order ASC, type ASC, name ASC
  `);
}

export async function getPanelPillar(id: string) {
  const [panelPillar] = await queryRows<PanelPillarRecord>(
    `
      SELECT id, type, icon, name, description, member_count, sort_order, created_at, updated_at
      FROM panels_pillars
      WHERE id = ?
      LIMIT 1
    `,
    [id],
  );

  return panelPillar ?? null;
}

export function getGalleryImages() {
  return queryRows<GalleryImageRecord>(`
    SELECT id, title, category, image_url, is_active, sort_order, created_at, updated_at
    FROM gallery_images
    ORDER BY sort_order ASC, title ASC
  `);
}

export async function getGalleryImage(id: string) {
  const [image] = await queryRows<GalleryImageRecord>(
    `
      SELECT id, title, category, image_url, is_active, sort_order, created_at, updated_at
      FROM gallery_images
      WHERE id = ?
      LIMIT 1
    `,
    [id],
  );

  return image ?? null;
}

export function getContactMessages() {
  return queryRows<ContactMessageRecord>(`
    SELECT id, name, email, subject, message, read_at, created_at, updated_at
    FROM contact_messages
    ORDER BY created_at DESC
  `);
}

export async function getAdminUsersCount() {
  const [row] = await queryRows<{ count: number }>("SELECT COUNT(*) AS count FROM admin_users");

  return row?.count ?? 0;
}

export function getAdminUsers() {
  return queryRows<AdminUserRecord>(`
    SELECT id, name, email, password_hash, role, is_active, last_login_at, created_at, updated_at
    FROM admin_users
    ORDER BY role = 'super_admin' DESC, name ASC
  `);
}

export async function getAdminUser(id: string) {
  const [admin] = await queryRows<AdminUserRecord>(
    `
      SELECT id, name, email, password_hash, role, is_active, last_login_at, created_at, updated_at
      FROM admin_users
      WHERE id = ?
      LIMIT 1
    `,
    [id],
  );

  return admin ?? null;
}
