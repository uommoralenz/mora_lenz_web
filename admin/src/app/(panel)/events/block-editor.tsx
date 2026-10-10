"use client";

import { useRef, useState } from "react";

import { uploadEventImageAction } from "./actions";
import type { EventBlock } from "@/lib/types";

type BlockType = EventBlock["type"];

/** Grouped the way an admin thinks about them while building a page. */
const ADD_GROUPS: { heading: string; types: BlockType[] }[] = [
  { heading: "Write", types: ["heading", "text", "list", "quote", "callout"] },
  { heading: "Show", types: ["image", "gallery", "video"] },
  { heading: "Act", types: ["button"] },
  { heading: "Space", types: ["divider", "spacer"] },
];

const ALL_TYPES: BlockType[] = ADD_GROUPS.flatMap((group) => group.types);

const BLOCK_NAMES: Record<BlockType, string> = {
  heading: "Heading",
  text: "Text",
  quote: "Quote",
  list: "List",
  image: "Image",
  gallery: "Image row",
  button: "Button",
  video: "YouTube video",
  callout: "Highlight box",
  spacer: "Blank space",
  divider: "Divider line",
};

const MAX_LIST_ITEMS = 30;
const MAX_GALLERY_IMAGES = 12;

type Keyed = EventBlock & { key: string };

let counter = 0;
const nextKey = () => `b${Date.now().toString(36)}${counter++}`;

function blank(type: BlockType): EventBlock {
  switch (type) {
    case "heading":
      return { type, text: "", level: 2, align: "left" };
    case "text":
      return { type, text: "", align: "left", size: "normal" };
    case "quote":
      return { type, text: "", cite: "" };
    case "list":
      return { type, style: "bullet", items: [""] };
    case "image":
      return { type, url: "", caption: "", width: "full" };
    case "gallery":
      return { type, urls: [], caption: "" };
    case "button":
      return { type, label: "", url: "", style: "primary" };
    case "video":
      return { type, url: "" };
    case "callout":
      return { type, title: "", text: "", tone: "info" };
    case "spacer":
      return { type, size: "medium" };
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

  /** Copy a block in place — quicker than rebuilding a similar one by hand. */
  const duplicate = (key: string) =>
    setBlocks((list) => {
      const at = list.findIndex((block) => block.key === key);
      if (at < 0) return list;
      const copy = [...list];
      copy.splice(at + 1, 0, { ...list[at], key: nextKey() });
      return copy;
    });

  /** Add at the end, or straight after `after` when inserting mid-page. */
  const add = (type: BlockType, after?: string) =>
    setBlocks((list) => {
      const made = { ...blank(type), key: nextKey() } as Keyed;
      if (!after) return [...list, made];

      const at = list.findIndex((block) => block.key === after);
      if (at < 0) return [...list, made];

      const copy = [...list];
      copy.splice(at + 1, 0, made);
      return copy;
    });

  // The key is UI-only; the server rebuilds each block from a whitelist anyway.
  const serialised = JSON.stringify(blocks.map(({ key: _key, ...block }) => block));

  return (
    <div className="space-y-3">
      <input type="hidden" name={name} value={serialised} />

      {blocks.length === 0 ? (
        <p className="rounded-lg border border-dashed border-ink-600 p-6 text-center text-sm text-slate-500">
          No page content yet. Add headings, text, lists, images, buttons and videos
          below — they appear on the event page in this order.
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
                title="Move up"
              >
                ↑
              </button>
              <button
                type="button"
                className="btn-ghost"
                onClick={() => move(block.key, 1)}
                disabled={index === blocks.length - 1}
                aria-label="Move down"
                title="Move down"
              >
                ↓
              </button>
              <button
                type="button"
                className="btn-ghost"
                onClick={() => duplicate(block.key)}
                aria-label="Duplicate block"
                title="Duplicate"
              >
                ⧉
              </button>
              <button
                type="button"
                className="btn-ghost text-rose-400"
                onClick={() => remove(block.key)}
                aria-label="Remove block"
                title="Remove"
              >
                ✕
              </button>
            </div>
          </div>

          <BlockFields block={block} onChange={(patch) => update(block.key, patch)} />

          <InsertAfter onAdd={(type) => add(type, block.key)} />
        </div>
      ))}

      <AddBar onAdd={(type) => add(type)} />

      <p className="hint">
        In any text you can write <code>**bold**</code>, <code>*italic*</code> and{" "}
        <code>[link text](https://example.com)</code>.
      </p>
    </div>
  );
}

/** The full palette, shown under the list. */
function AddBar({ onAdd }: { onAdd: (type: BlockType) => void }) {
  return (
    <div className="space-y-2 rounded-lg border border-ink-700 p-3">
      {ADD_GROUPS.map((group) => (
        <div key={group.heading} className="flex flex-wrap items-center gap-2">
          <span className="w-14 text-xs uppercase tracking-wide text-slate-500">
            {group.heading}
          </span>
          {group.types.map((type) => (
            <button
              key={type}
              type="button"
              className="btn-secondary px-3 py-1.5"
              onClick={() => onAdd(type)}
            >
              + {BLOCK_NAMES[type]}
            </button>
          ))}
        </div>
      ))}
    </div>
  );
}

/**
 * A collapsed "insert here" control on every block, so something can be added
 * into the middle of a long page instead of appended and walked up one step at
 * a time.
 */
function InsertAfter({ onAdd }: { onAdd: (type: BlockType) => void }) {
  const [open, setOpen] = useState(false);

  if (!open) {
    return (
      <button
        type="button"
        className="mt-3 w-full rounded border border-dashed border-ink-700 py-1 text-xs text-slate-500 hover:border-ink-600 hover:text-slate-300"
        onClick={() => setOpen(true)}
      >
        + Insert below
      </button>
    );
  }

  return (
    <div className="mt-3 rounded border border-dashed border-ink-700 p-2">
      <div className="flex flex-wrap gap-1.5">
        {ALL_TYPES.map((type) => (
          <button
            key={type}
            type="button"
            className="btn-secondary px-2 py-1 text-xs"
            onClick={() => {
              onAdd(type);
              setOpen(false);
            }}
          >
            {BLOCK_NAMES[type]}
          </button>
        ))}
        <button
          type="button"
          className="btn-ghost px-2 py-1 text-xs"
          onClick={() => setOpen(false)}
        >
          Cancel
        </button>
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
        <div className="grid gap-2 sm:grid-cols-[1fr_8rem_8rem]">
          <input
            className="input"
            placeholder="Heading text"
            maxLength={200}
            value={block.text}
            onChange={(e) => onChange({ text: e.target.value })}
          />
          <select
            className="input"
            value={block.level}
            onChange={(e) => {
              const level = Number(e.target.value);
              onChange({ level: level === 3 ? 3 : level === 4 ? 4 : 2 });
            }}
            aria-label="Heading size"
          >
            <option value={2}>Large</option>
            <option value={3}>Medium</option>
            <option value={4}>Small</option>
          </select>
          <select
            className="input"
            value={block.align}
            onChange={(e) =>
              onChange({ align: e.target.value === "center" ? "center" : "left" })
            }
            aria-label="Heading alignment"
          >
            <option value="left">Left</option>
            <option value="center">Centre</option>
          </select>
        </div>
      );

    case "text":
      return (
        <div className="space-y-2">
          <textarea
            className="textarea min-h-28"
            placeholder="Write your text. Leave a blank line between paragraphs. **bold**, *italic* and [links](https://…) work here."
            maxLength={10000}
            value={block.text}
            onChange={(e) => onChange({ text: e.target.value })}
          />
          <div className="grid gap-2 sm:grid-cols-2">
            <select
              className="input"
              value={block.align}
              onChange={(e) => {
                const value = e.target.value;
                onChange({
                  align:
                    value === "center" ? "center" : value === "right" ? "right" : "left",
                });
              }}
              aria-label="Alignment"
            >
              <option value="left">Align left</option>
              <option value="center">Centre</option>
              <option value="right">Align right</option>
            </select>
            <select
              className="input"
              value={block.size}
              onChange={(e) =>
                onChange({ size: e.target.value === "lead" ? "lead" : "normal" })
              }
              aria-label="Text size"
            >
              <option value="normal">Normal size</option>
              <option value="lead">Larger (intro paragraph)</option>
            </select>
          </div>
        </div>
      );

    case "quote":
      return (
        <div className="space-y-2">
          <textarea
            className="textarea min-h-20"
            placeholder="The quote itself"
            maxLength={2000}
            value={block.text}
            onChange={(e) => onChange({ text: e.target.value })}
          />
          <input
            className="input"
            placeholder="Who said it (optional)"
            maxLength={160}
            value={block.cite}
            onChange={(e) => onChange({ cite: e.target.value })}
          />
        </div>
      );

    case "list":
      return <ListFields block={block} onChange={onChange} />;

    case "image":
      return <ImageFields block={block} onChange={onChange} />;

    case "gallery":
      return <GalleryFields block={block} onChange={onChange} />;

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
              {kind.startsWith("Links must")
                ? kind
                : `Detected: ${kind} — the matching icon is added automatically.`}
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
          <div className="grid gap-2 sm:grid-cols-[1fr_10rem]">
            <input
              className="input"
              placeholder="Highlight title, e.g. Registration closes Friday"
              maxLength={200}
              value={block.title}
              onChange={(e) => onChange({ title: e.target.value })}
            />
            <select
              className="input"
              value={block.tone}
              onChange={(e) => {
                const value = e.target.value;
                onChange({
                  tone:
                    value === "warn" ? "warn" : value === "success" ? "success" : "info",
                });
              }}
              aria-label="Highlight tone"
            >
              <option value="info">Blue (note)</option>
              <option value="warn">Amber (deadline)</option>
              <option value="success">Green (good news)</option>
            </select>
          </div>
          <textarea
            className="textarea min-h-20"
            placeholder="Details (optional)"
            maxLength={3000}
            value={block.text}
            onChange={(e) => onChange({ text: e.target.value })}
          />
        </div>
      );

    case "spacer":
      return (
        <select
          className="input w-48"
          value={block.size}
          onChange={(e) => {
            const value = e.target.value;
            onChange({
              size: value === "small" ? "small" : value === "large" ? "large" : "medium",
            });
          }}
          aria-label="Space height"
        >
          <option value="small">Small gap</option>
          <option value="medium">Medium gap</option>
          <option value="large">Large gap</option>
        </select>
      );

    case "divider":
      return <p className="text-xs text-slate-500">A thin line separating two sections.</p>;
  }
}

function ListFields({
  block,
  onChange,
}: {
  block: Extract<EventBlock, { type: "list" }>;
  onChange: (patch: Partial<EventBlock>) => void;
}) {
  const setItem = (index: number, value: string) =>
    onChange({ items: block.items.map((item, i) => (i === index ? value : item)) });

  const removeItem = (index: number) =>
    onChange({ items: block.items.filter((_item, i) => i !== index) });

  return (
    <div className="space-y-2">
      <select
        className="input w-48"
        value={block.style}
        onChange={(e) => {
          const value = e.target.value;
          onChange({
            style:
              value === "number" ? "number" : value === "check" ? "check" : "bullet",
          });
        }}
        aria-label="List style"
      >
        <option value="bullet">Bulleted</option>
        <option value="number">Numbered</option>
        <option value="check">Ticks</option>
      </select>

      {block.items.map((item, index) => (
        <div key={index} className="flex gap-2">
          <input
            className="input"
            placeholder={`Item ${index + 1}`}
            maxLength={300}
            value={item}
            onChange={(e) => setItem(index, e.target.value)}
          />
          <button
            type="button"
            className="btn-ghost text-rose-400"
            onClick={() => removeItem(index)}
            disabled={block.items.length === 1}
            aria-label={`Remove item ${index + 1}`}
          >
            ✕
          </button>
        </div>
      ))}

      <button
        type="button"
        className="btn-secondary px-3 py-1.5"
        onClick={() => onChange({ items: [...block.items, ""] })}
        disabled={block.items.length >= MAX_LIST_ITEMS}
      >
        + Add item
      </button>
      <p className="hint">Empty items are dropped when you save.</p>
    </div>
  );
}

/** Shared uploader: hands one chosen file to the server and returns its URL. */
function useUpload() {
  const input = useRef<HTMLInputElement>(null);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");

  async function upload(file: File | undefined): Promise<string | null> {
    if (!file) return null;

    setBusy(true);
    setError("");

    const body = new FormData();
    body.set("image", file);

    const result = await uploadEventImageAction(body);

    setBusy(false);
    if (input.current) input.current.value = "";

    if (result.url) return result.url;

    setError(result.message || "Upload failed.");
    return null;
  }

  return { input, busy, error, upload };
}

function ImageFields({
  block,
  onChange,
}: {
  block: Extract<EventBlock, { type: "image" }>;
  onChange: (patch: Partial<EventBlock>) => void;
}) {
  const { input, busy, error, upload } = useUpload();

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
          onChange={async (e) => {
            const url = await upload(e.target.files?.[0]);
            if (url) onChange({ url });
          }}
        />
        {busy ? <span className="text-xs text-slate-400">Uploading…</span> : null}
      </div>

      <div className="grid gap-2 sm:grid-cols-[1fr_10rem]">
        <input
          className="input"
          placeholder="Caption (optional)"
          maxLength={300}
          value={block.caption}
          onChange={(e) => onChange({ caption: e.target.value })}
        />
        <select
          className="input"
          value={block.width}
          onChange={(e) => {
            const value = e.target.value;
            onChange({
              width: value === "wide" ? "wide" : value === "narrow" ? "narrow" : "full",
            });
          }}
          aria-label="Image width"
        >
          <option value="full">Column width</option>
          <option value="wide">Wider than text</option>
          <option value="narrow">Narrow (portrait)</option>
        </select>
      </div>

      {error ? <p className="error-text">{error}</p> : null}
      <p className="hint">JPG, PNG or WebP, up to 8 MB.</p>
    </div>
  );
}

function GalleryFields({
  block,
  onChange,
}: {
  block: Extract<EventBlock, { type: "gallery" }>;
  onChange: (patch: Partial<EventBlock>) => void;
}) {
  const { input, busy, error, upload } = useUpload();

  const removeAt = (index: number) =>
    onChange({ urls: block.urls.filter((_url, i) => i !== index) });

  const moveAt = (index: number, delta: number) => {
    const to = index + delta;
    if (to < 0 || to >= block.urls.length) return;

    const copy = [...block.urls];
    [copy[index], copy[to]] = [copy[to], copy[index]];
    onChange({ urls: copy });
  };

  return (
    <div className="space-y-2">
      {block.urls.length ? (
        <div className="flex flex-wrap gap-2">
          {block.urls.map((url, index) => (
            <div key={`${url}-${index}`} className="space-y-1">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img
                src={url}
                alt=""
                className="h-24 w-32 rounded border border-ink-700 object-cover"
              />
              <div className="flex justify-center gap-1">
                <button
                  type="button"
                  className="btn-ghost px-1.5 py-0.5 text-xs"
                  onClick={() => moveAt(index, -1)}
                  disabled={index === 0}
                  aria-label="Move image left"
                >
                  ←
                </button>
                <button
                  type="button"
                  className="btn-ghost px-1.5 py-0.5 text-xs"
                  onClick={() => moveAt(index, 1)}
                  disabled={index === block.urls.length - 1}
                  aria-label="Move image right"
                >
                  →
                </button>
                <button
                  type="button"
                  className="btn-ghost px-1.5 py-0.5 text-xs text-rose-400"
                  onClick={() => removeAt(index)}
                  aria-label="Remove image"
                >
                  ✕
                </button>
              </div>
            </div>
          ))}
        </div>
      ) : null}

      <div className="flex flex-wrap items-center gap-2">
        <input
          ref={input}
          type="file"
          accept="image/*"
          multiple
          className="input w-auto file:mr-3 file:rounded file:border-0 file:bg-ink-700 file:px-3 file:py-1 file:text-slate-200"
          disabled={busy || block.urls.length >= MAX_GALLERY_IMAGES}
          onChange={async (e) => {
            const files = Array.from(e.target.files ?? []);
            const room = MAX_GALLERY_IMAGES - block.urls.length;
            const added: string[] = [];

            for (const file of files.slice(0, room)) {
              const url = await upload(file);
              if (url) added.push(url);
            }

            if (added.length) onChange({ urls: [...block.urls, ...added] });
          }}
        />
        {busy ? <span className="text-xs text-slate-400">Uploading…</span> : null}
      </div>

      <input
        className="input"
        placeholder="Caption for the row (optional)"
        maxLength={300}
        value={block.caption}
        onChange={(e) => onChange({ caption: e.target.value })}
      />

      {error ? <p className="error-text">{error}</p> : null}
      <p className="hint">
        Up to {MAX_GALLERY_IMAGES} images, shown as a grid. {block.urls.length}/
        {MAX_GALLERY_IMAGES} added.
      </p>
    </div>
  );
}
