import { getCurrentAdmin, requireAdmin } from "@/lib/auth";
import { createImageUpload } from "@/lib/cloudinary";
import { consumeRateLimit } from "@/lib/rate-limit";

export const runtime = "nodejs";

export async function POST(request: Request) {
  const origin = request.headers.get("origin");
  if (!origin || origin !== new URL(request.url).origin) {
    return Response.json({ error: "Invalid request origin." }, { status: 403 });
  }
  if (!await getCurrentAdmin()) {
    return Response.json({ error: "Please sign in before uploading." }, { status: 401 });
  }
  const admin = await requireAdmin();
  const body = await request.json().catch(() => null);
  const type = body?.type;
  if (type !== "events" && type !== "members" && type !== "gallery") {
    return Response.json({ error: "Invalid image category." }, { status: 400 });
  }
  try {
    if (!await consumeRateLimit("upload", admin.id, 30, 60)) {
      return Response.json({ error: "Too many uploads. Try again in a minute." }, { status: 429, headers: { "Retry-After": "60" } });
    }
    return Response.json(createImageUpload(type), { headers: { "Cache-Control": "no-store" } });
  } catch {
    return Response.json({ error: "Image uploads are not configured. You can still paste an image URL." }, { status: 503 });
  }
}
