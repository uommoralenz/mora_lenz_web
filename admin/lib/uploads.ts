import "server-only";
import { createImageUpload } from "@/lib/cloudinary";

type UploadType = "events" | "members" | "gallery";

type UploadResponse = {
  url: string;
};

function isUploadableFile(value: FormDataEntryValue): value is File {
  return value instanceof File && value.size > 0;
}

export async function uploadImages(values: FormDataEntryValue[], type: UploadType) {
  const files = values.filter(isUploadableFile);

  if (files.length === 0) {
    return [];
  }

  const uploads = await Promise.all(files.map((file) => uploadImage(file, type)));

  return uploads.map((upload) => upload.url);
}

export async function uploadOptionalImage(value: FormDataEntryValue | null, type: UploadType) {
  if (!value || !isUploadableFile(value)) {
    return null;
  }

  return (await uploadImage(value, type)).url;
}

async function uploadImage(file: File, type: UploadType): Promise<UploadResponse> {
  const { endpoint, fields } = createImageUpload(type);

  if (!file.type.startsWith("image/")) {
    throw new Error("Only image uploads are allowed.");
  }

  if (file.size > 6 * 1024 * 1024) {
    throw new Error("Image uploads must be 6 MB or smaller.");
  }

  const body = new FormData();
  for (const [key, value] of Object.entries(fields)) body.append(key, value);
  body.append("file", file, file.name);

  const response = await fetch(endpoint, {
    method: "POST",
    headers: {
      Accept: "application/json",
    },
    body,
    signal: AbortSignal.timeout(120000),
  });

  if (!response.ok) {
    throw new Error("Image upload failed. Try again or paste an image URL.");
  }

  const result = await response.json();
  if (typeof result.secure_url !== "string" || !result.secure_url.startsWith("https://")) {
    throw new Error("The image service did not return a valid image URL.");
  }
  return { url: result.secure_url };
}
