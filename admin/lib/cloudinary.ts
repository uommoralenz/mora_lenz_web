import "server-only";
import crypto from "node:crypto";

export type UploadType = "events" | "members" | "gallery";

export function createImageUpload(type: UploadType) {
  const cloudName = process.env.CLOUDINARY_CLOUD_NAME;
  const apiKey = process.env.CLOUDINARY_API_KEY;
  const apiSecret = process.env.CLOUDINARY_API_SECRET;
  if (!cloudName || !apiKey || !apiSecret || !/^[a-zA-Z0-9_-]+$/.test(cloudName)) {
    throw new Error("Image uploads are not configured. You can still paste an image URL.");
  }
  const params = {
    allowed_formats: "jpg,jpeg,png,webp,gif,avif",
    folder: `mora-lenz/${type}`,
    overwrite: "false",
    public_id: crypto.randomUUID(),
    timestamp: String(Math.floor(Date.now() / 1000)),
  };
  const serialized = Object.entries(params).sort(([a], [b]) => a.localeCompare(b))
    .map(([key, value]) => `${key}=${value}`).join("&");
  return {
    endpoint: `https://api.cloudinary.com/v1_1/${cloudName}/image/upload`,
    fields: {
      ...params,
      api_key: apiKey,
      signature: crypto.createHash("sha256").update(serialized + apiSecret).digest("hex"),
    },
  };
}
