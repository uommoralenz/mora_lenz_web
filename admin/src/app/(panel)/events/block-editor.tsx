"use client";

import { useRef, useState } from "react";

import { uploadEventImageAction } from "./actions";
import type { EventBlock } from "@/lib/types";

type BlockType = EventBlock["type"];

const ADD_OPTIONS: { type: BlockType; label: string }[] = [
  { type: "heading", label: "Heading" },
  { type: "text", label: "Text" },
  { type: "image", label: "Image" },
  { type: "button", label: "Button" },
  { type: "video", label: "Video" },
  { type: "callout", label: "Callout" },
  { type: "divider", label: "Divider" },
];

const BLOCK_NAMES: Record<BlockType, string> = {
  heading: "Heading",
  text: "Text",
  image: "Image",
  button: "Button",
  video: "YouTube video",
  callout: "Callout",
  divider: "Divider line",
};

type Keyed = EventBlock & { key: string };

let counter = 0;
const nextKey = () => `b${Date.now().toString(36)}${counter++}`;

function blank(type: BlockType): EventBlock {
  switch (type) {
    case "heading":
      return { type, text: "", level: 2 };
    case "text":
      return { type, text: "", align: "left" };
    case "image":
      return { type, url: "", caption: "" };
    case "button":
      return { type, label: "", url: "", style: "primary" };
    case "video":
      return { type, url: "" };
    case "callout":
      return { type, title: "", text: "" };
    case "divider":
      return { type };
  }
}

/** Which service a pasted link belongs to, to reassure the admin it is right. */
function describeLink(url: string): string | null {
  const value = url.trim().toLowerCase();
  if (!value) return null;
  if (value.startsWith("mailto:")) return "Email link";
  if (value.startsWith("tel:")) return "Phone link";
  if (!/^https?:\/\//.test(value)) return "Links must start with https://";
  if (/wa\.me|whatsapp\.com/.test(value)) return "WhatsApp";
  if (/facebook\.com|fb\.me|fb\.com/.test(value)) return "Facebook";
  if (/instagram\.com/.test(value)) return "Instagram";
  if (/youtube\.com|youtu\.be/.test(value)) return "YouTube";
  if (/forms\.gle|docs\.google\.com\/forms/.test(value)) return "Google Form";
  if (/drive\.google\.com|docs\.google\.com/.test(value)) return "Google Drive";
  return "Website link";
}

export default function BlockEditor({
  initial,
  name = "content",
}: {
  initial: EventBlock[];
  name?: string;
}) {
  const [blocks, setBlocks] = useState<Keyed[]>(() =>
    initial.map((block) => ({ ...block, key: nextKey() }))
  );

  const update = (key: string, patch: Partial<EventBlock>) =>
    setBlocks((list) =>
      list.map((block) => (block.key === key ? ({ ...block, ...patch } as Keyed) : block))
    );

  const remove = (key: string) =>
    setBlocks((list) => list.filter((block) => block.key !== key));

  const move = (key: string, delta: number) =>
    setBlocks((list) => {
      const from = list.findIndex((block) => block.key === key);
      const to = from + delta;
      if (from < 0 || to < 0 || to >= list.length) return list;
      const copy = [...list];
      [copy[from], copy[to]] = [copy[to], copy[from]];
      return copy;
    });

  const add = (type: BlockType) =>
    setBlocks((list) => [...list, { ...blank(type), key: nextKey() } as Keyed]);

  // The key is UI-only; the server rebuilds each block from a whitelist anyway.
  const serialised = JSON.stringify(blocks.map(({ key: _key, ...block }) => block));

  return (
    <div className="space-y-3">
      <input type="hidden" name={name} value={serialised} />

      {blocks.length === 0 ? (
        <p className="rounded-lg border border-dashed border-ink-600 p-6 text-center text-sm text-slate-500">
          No custom content yet. Add headings, text, images, buttons and videos below —
          they appear on the event page in this order.
        </p>
      ) : null}

      {blocks.map((block, index) => (
        <div key={block.key} className="rounded-lg border border-ink-700 bg-ink-950/40 p-3">
          <div className="mb-3 flex items-center justify-between gap-2">
            <span className="text-xs font-semibold uppercase tracking-wide text-slate-400">
              {index + 1}. {BLOCK_NAMES[block.type]}
            </span>
            <div className="flex gap-1">
              <button
                type="button"
                className="btn-ghost"
                onClick={() => move(block.key, -1)}
                disabled={index === 0}
                aria-label="Move up"
              >
                ↑
              </button>
              <button
                type="button"
                className="btn-ghost"
                onClick={() => move(block.key, 1)}
                disabled={index === blocks.length - 1}
                aria-label="Move down"
              >
                ↓
              </button>
              <button
                type="button"
                className="btn-ghost text-rose-400"
                onClick={() => remove(block.key)}
                aria-label="Remove block"
              >
                ✕
              </button>
            </div>
          </div>

          <BlockFields block={block} onChange={(patch) => update(block.key, patch)} />
        </div>
      ))}

      <div className="flex flex-wrap items-center gap-2 pt-1">
        <span className="text-xs text-slate-500">Add:</span>
        {ADD_OPTIONS.map((option) => (
          <button
            key={option.type}
            type="button"
            className="btn-secondary px-3 py-1.5"
            onClick={() => add(option.type)}
          >
            + {option.label}
          </button>
        ))}
      </div>
    </div>
  );
}

function BlockFields({
  block,
  onChange,
}: {
  block: Keyed;
  onChange: (patch: Partial<EventBlock>) => void;
}) {
  switch (block.type) {
    case "heading":
      return (
        <div className="flex gap-2">
          <input
            className="input"
            placeholder="Heading text"
            maxLength={200}
            value={block.text}
            onChange={(e) => onChange({ text: e.target.value })}
          />
          <select
            className="input w-32"
            value={block.level}
            onChange={(e) => onChange({ level: Number(e.target.value) === 3 ? 3 : 2 })}
            aria-label="Heading size"
          >
            <option value={2}>Large</option>
            <option value={3}>Small</option>
          </select>
        </div>
      );

    case "text":
      return (
        <div className="space-y-2">
          <textarea
            className="textarea min-h-28"
            placeholder="Write your text. Leave a blank line between paragraphs."
            maxLength={10000}
            value={block.text}
            onChange={(e) => onChange({ text: e.target.value })}
          />
          <select
            className="input w-40"
            value={block.align}
            onChange={(e) => onChange({ align: e.target.value === "center" ? "center" : "left" })}
            aria-label="Alignment"
          >
            <option value="left">Align left</option>
            <option value="center">Centre</option>
          </select>
        </div>
      );

    case "image":
      return <ImageFields block={block} onChange={onChange} />;

    case "button": {
      const kind = describeLink(block.url);
      return (
        <div className="space-y-2">
          <div className="grid gap-2 sm:grid-cols-2">
            <input
              className="input"
              placeholder="Button label, e.g. Join the WhatsApp group"
              maxLength={80}
              value={block.label}
              onChange={(e) => onChange({ label: e.target.value })}
            />
            <select
              className="input"
              value={block.style}
              onChange={(e) =>
                onChange({ style: e.target.value === "outline" ? "outline" : "primary" })
              }
              aria-label="Button style"
            >
              <option value="primary">Solid (white)</option>
              <option value="outline">Outline</option>
            </select>
          </div>
          <input
            className="input"
            placeholder="https://chat.whatsapp.com/… or a Facebook, Google Form or Drive link"
            maxLength={1000}
            value={block.url}
            onChange={(e) => onChange({ url: e.target.value })}
          />
          {kind ? (
            <p className={`hint ${kind.startsWith("Links must") ? "!text-rose-400" : ""}`}>
              {kind.startsWith("Links must") ? kind : `Detected: ${kind} — the matching icon is added automatically.`}
            </p>
          ) : (
            <p className="hint">
              Buttons placed one after another sit side by side on the page.
            </p>
          )}
        </div>
      );
    }

    case "video":
      return (
        <div className="space-y-2">
          <input
            className="input"
            placeholder="https://www.youtube.com/watch?v=…"
            maxLength={1000}
            value={block.url}
            onChange={(e) => onChange({ url: e.target.value })}
          />
          <p className="hint">Paste a YouTube link; it is embedded as a player.</p>
        </div>
      );

    case "callout":
      return (
        <div className="space-y-2">
          <input
            className="input"
            placeholder="Highlight title, e.g. Registration closes Friday"
            maxLength={200}
            value={block.title}
            onChange={(e) => onChange({ title: e.target.value })}
          />
          <textarea
            className="textarea min-h-20"
            placeholder="Details (optional)"
            maxLength={3000}
            value={block.text}
            onChange={(e) => onChange({ text: e.target.value })}
          />
        </div>
      );

    case "divider":
      return <p className="text-xs text-slate-500">A thin line separating two sections.</p>;
  }
}

function ImageFields({
  block,
  onChange,
}: {
  block: Extract<EventBlock, { type: "image" }>;
  onChange: (patch: Partial<EventBlock>) => void;
}) {
  const input = useRef<HTMLInputElement>(null);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");

  async function onFile(file: File | undefined) {
    if (!file) return;
    setBusy(true);
    setError("");

    const body = new FormData();
    body.set("image", file);

    const result = await uploadEventImageAction(body);

    setBusy(false);
    if (input.current) input.current.value = "";

    if (result.url) onChange({ url: result.url });
    else setError(result.message || "Upload failed.");
  }

  return (
    <div className="space-y-2">
      {block.url ? (
        // eslint-disable-next-line @next/next/no-img-element
        <img
          src={block.url}
          alt=""
          className="max-h-48 rounded-lg border border-ink-700 object-contain"
        />
      ) : null}

      <div className="flex flex-wrap items-center gap-2">
        <input
          ref={input}
          type="file"
          accept="image/*"
          className="input w-auto file:mr-3 file:rounded file:border-0 file:bg-ink-700 file:px-3 file:py-1 file:text-slate-200"
          disabled={busy}
          onChange={(e) => onFile(e.target.files?.[0])}
        />
        {busy ? <span className="text-xs text-slate-400">Uploading…</span> : null}
      </div>

      <input
        className="input"
        placeholder="Caption (optional)"
        maxLength={300}
        value={block.caption}
        onChange={(e) => onChange({ caption: e.target.value })}
      />
      {error ? <p className="error-text">{error}</p> : null}
      <p className="hint">JPG, PNG or WebP, up to 8 MB.</p>
    </div>
  );
}
