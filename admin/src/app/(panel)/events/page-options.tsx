"use client";

import { useState } from "react";

import {
  EVENT_PAGE_CODE_MAX,
  EVENT_PAGE_DEFAULTS,
  type EventPageOptions,
} from "@/lib/types";

/** A template literal, so the example keeps its line breaks. */
const PAGE_CODE_PLACEHOLDER = `<style>
  .event-hero__title { letter-spacing: -0.02em; }
</style>

<script>
  // Runs on this event page only
</script>`;

/**
 * The page-level layout choices for one event, posted as a single JSON field
 * the same way the content blocks are. The server rebuilds them from a
 * whitelist (App\Support\EventBlocks::pageOptions), so this only has to collect
 * them.
 */
export default function PageOptionsEditor({
  initial,
  name = "page_options",
}: {
  initial: EventPageOptions;
  name?: string;
}) {
  // The API may be a deploy behind this panel and not send every option yet,
  // so each one is filled in here rather than trusted to be present.
  const [options, setOptions] = useState<EventPageOptions>({
    ...EVENT_PAGE_DEFAULTS,
    ...initial,
  });

  const set = (patch: Partial<EventPageOptions>) =>
    setOptions((current) => ({ ...current, ...patch }));

  // The textarea is controlled, so this must never be undefined.
  const customCode = options.custom_code ?? "";

  return (
    <div className="space-y-4 rounded-lg border border-ink-700 p-4">
      <input type="hidden" name={name} value={JSON.stringify(options)} />

      <div className="grid gap-4 sm:grid-cols-2">
        <div>
          <label className="label" htmlFor="page-hero">
            Top of the page
          </label>
          <select
            id="page-hero"
            className="input"
            value={options.hero}
            onChange={(e) => {
              const value = e.target.value;
              set({
                hero:
                  value === "compact" ? "compact" : value === "plain" ? "plain" : "photo",
              });
            }}
          >
            <option value="photo">Full cover photo</option>
            <option value="compact">Short banner</option>
            <option value="plain">Title only, no photo</option>
          </select>
          <p className="hint">
            The cover image is still used for link previews when the page hides it.
          </p>
        </div>

        <div>
          <label className="label" htmlFor="page-width">
            Content width
          </label>
          <select
            id="page-width"
            className="input"
            value={options.width}
            onChange={(e) => {
              const value = e.target.value;
              set({ width: value === "wide" ? "wide" : value === "full" ? "full" : "normal" });
            }}
          >
            <option value="normal">Normal — easiest to read</option>
            <option value="wide">Wide — more room for photos</option>
            <option value="full">Full width — for custom HTML layouts</option>
          </select>
        </div>
      </div>

      <label className="flex items-start gap-3 text-sm">
        <input
          type="checkbox"
          checked={options.show_meta}
          onChange={(e) => set({ show_meta: e.target.checked })}
          className="mt-0.5"
        />
        <span>
          <span className="font-medium text-slate-200">Show the date, time and location</span>
          <span className="block text-xs text-slate-500">
            The pills under the title. Untick for a page where the date is not the point.
          </span>
        </span>
      </label>

      <div>
        <div className="flex flex-wrap items-center justify-between gap-2">
          <label className="label mb-0" htmlFor="page-custom-code">
            Page-wide HTML, CSS and JavaScript
          </label>
          <span className="text-xs text-slate-500">
            {customCode.length.toLocaleString()} /{" "}
            {EVENT_PAGE_CODE_MAX.toLocaleString()}
          </span>
        </div>

        <textarea
          id="page-custom-code"
          className="textarea mt-1 min-h-32 font-mono text-xs leading-relaxed"
          spellCheck={false}
          maxLength={EVENT_PAGE_CODE_MAX}
          placeholder={PAGE_CODE_PLACEHOLDER}
          value={customCode}
          onChange={(e) => set({ custom_code: e.target.value })}
        />
        <p className="hint">
          Added to the end of this page&apos;s <code>&lt;head&gt;</code>, after the site
          stylesheet — so rules here win over the site&apos;s own. Use it for styles and
          fonts that several blocks share; for one-off markup add a{" "}
          <strong>Custom HTML</strong> block to the content below instead. Applies to
          this event page only.
        </p>
      </div>
    </div>
  );
}
