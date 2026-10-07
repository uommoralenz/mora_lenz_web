/**
 * Browser-side photo shrinking.
 *
 * The admin panel runs on Vercel, which rejects any request body over ~4.5 MB,
 * and phone photos are routinely 4-10 MB. Shrinking each photo to a web-sized
 * JPEG (usually 300-900 KB) before it is sent keeps every upload far below that
 * limit and also makes the public gallery load much faster.
 */

const MAX_EDGE = 2400;
/** Default ceiling; callers can pass a lower one. */
const DEFAULT_LIMIT = 1.9 * 1024 * 1024;

function toBlob(canvas: HTMLCanvasElement, quality: number): Promise<Blob | null> {
  return new Promise((resolve) => canvas.toBlob(resolve, "image/jpeg", quality));
}

export async function shrinkImage(file: File, limit = DEFAULT_LIMIT): Promise<File> {
  // Animated GIFs and anything small enough already are sent as they are.
  if (file.type === "image/gif" || !file.type.startsWith("image/")) return file;

  let bitmap: ImageBitmap;

  try {
    bitmap = await createImageBitmap(file, { imageOrientation: "from-image" });
  } catch {
    // Unreadable in the browser (e.g. HEIC): let the server decide.
    return file;
  }

  const scale = Math.min(1, MAX_EDGE / Math.max(bitmap.width, bitmap.height));
  const width = Math.round(bitmap.width * scale);
  const height = Math.round(bitmap.height * scale);

  if (scale === 1 && file.size <= limit && file.type === "image/jpeg") {
    bitmap.close();
    return file;
  }

  const canvas = document.createElement("canvas");
  canvas.width = width;
  canvas.height = height;

  const context = canvas.getContext("2d");
  if (!context) {
    bitmap.close();
    return file;
  }

  // JPEG has no transparency: paint white so PNG cut-outs do not turn black.
  context.fillStyle = "#fff";
  context.fillRect(0, 0, width, height);
  context.drawImage(bitmap, 0, 0, width, height);
  bitmap.close();

  let blob: Blob | null = null;

  for (const quality of [0.86, 0.78, 0.7, 0.6, 0.5, 0.4]) {
    blob = await toBlob(canvas, quality);
    if (blob && blob.size <= limit) break;
  }

  if (!blob) return file;

  const name = file.name.replace(/\.[^.]+$/, "") || "photo";
  return new File([blob], `${name}.jpg`, { type: "image/jpeg" });
}
