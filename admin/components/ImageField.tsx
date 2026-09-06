"use client";

import { useEffect, useRef, useState } from "react";

type ImageFieldProps = {
  name: string;
  label: string;
  type: "events" | "members" | "gallery";
  defaultValue?: string;
  multiple?: boolean;
  maxLength?: number;
};

export function ImageField({ name, label, type, defaultValue = "", multiple = false, maxLength }: ImageFieldProps) {
  const [value, setValue] = useState(defaultValue);
  const [uploading, setUploading] = useState(false);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");
  const container = useRef<HTMLDivElement>(null);
  const busy = useRef(false);

  useEffect(() => {
    const form = container.current?.closest("form");
    const preventEarlySave = (event: Event) => {
      if (busy.current) {
        event.preventDefault();
        event.stopImmediatePropagation();
        setMessage("Please wait for the image upload to finish before saving.");
      }
    };
    form?.addEventListener("submit", preventEarlySave, true);
    return () => form?.removeEventListener("submit", preventEarlySave, true);
  }, []);

  async function upload(files: File[]) {
    if (!files.length || busy.current) return;
    setError("");
    setMessage("");
    if (files.some((file) => !["image/jpeg", "image/png", "image/webp", "image/gif", "image/avif"].includes(file.type) || !file.size || file.size > 6 * 1024 * 1024)) {
      setError("Choose JPG, PNG, WebP, GIF, or AVIF images up to 6 MB each.");
      return;
    }
    busy.current = true;
    setUploading(true);
    try {
      for (let index = 0; index < files.length; index++) {
        setMessage(`Uploading image ${index + 1} of ${files.length}…`);
        const signedResponse = await fetch("/api/uploads/sign", {
          method: "POST", headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ type }), signal: AbortSignal.timeout(30000),
        });
        if (signedResponse.redirected) throw new Error("Please sign in again before uploading.");
        const signed = await signedResponse.json();
        if (!signedResponse.ok) throw new Error(signed.error || "Unable to start upload.");
        const body = new FormData();
        for (const [key, field] of Object.entries(signed.fields as Record<string, string>)) body.append(key, field);
        body.append("file", files[index]);
        const response = await fetch(signed.endpoint, { method: "POST", body, signal: AbortSignal.timeout(120000) });
        const result = await response.json();
        if (!response.ok || typeof result.secure_url !== "string" || !result.secure_url.startsWith("https://")) {
          throw new Error("Image upload failed. Try again or paste an image URL.");
        }
        if (maxLength && result.secure_url.length > maxLength) throw new Error("The uploaded image URL is too long for this field.");
        setValue((current) => multiple ? [current.trim(), result.secure_url].filter(Boolean).join("\n") : result.secure_url);
      }
      setMessage("Upload complete. The image URL is filled in. Save the form to publish your changes.");
    } catch (cause) {
      setMessage("");
      setError(cause instanceof Error ? cause.message : "Upload failed. Try again or paste a URL.");
    } finally {
      busy.current = false;
      setUploading(false);
    }
  }

  return (
    <div ref={container} className="image-field" aria-busy={uploading}>
      <label>
        <span>{label}</span>
        {multiple ? (
          <textarea name={name} value={value} onChange={(event) => setValue(event.target.value)} readOnly={uploading} rows={4} placeholder="https://example.com/image.webp" />
        ) : (
          <input name={name} value={value} onChange={(event) => setValue(event.target.value)} readOnly={uploading} maxLength={maxLength} placeholder="https://example.com/image.webp" />
        )}
        <small>{multiple ? "Paste one URL per line, or upload images below. The first URL is the cover image." : "Paste an existing image URL, or upload an image below."}</small>
      </label>
      <label>
        <span>{multiple ? "Upload images" : "Upload image"}</span>
        <input type="file" accept="image/jpeg,image/png,image/webp,image/gif,image/avif" multiple={multiple} disabled={uploading}
          onChange={(event) => { const files = Array.from(event.target.files ?? []); event.target.value = ""; void upload(files); }} />
        <small>Up to 6 MB per image. Uploads automatically fill the URL {multiple ? "list" : "field"}.</small>
      </label>
      {message ? <p role="status">{message}</p> : null}
      {error ? <p role="alert" className="alert alert-error">{error}</p> : null}
    </div>
  );
}
