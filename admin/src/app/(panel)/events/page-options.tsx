"use client";

import { useState } from "react";

import type { EventPageOptions } from "@/lib/types";

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
  const [options, setOptions] = useState<EventPageOptions>(initial);

  const set = (patch: Partial<EventPageOptions>) =>
    setOptions((current) => ({ ...current, ...patch }));

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
            onChange={(e) => set({ width: e.target.value === "wide" ? "wide" : "normal" })}
          >
            <option value="normal">Normal — easiest to read</option>
            <option value="wide">Wide — more room for photos</option>
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
    </div>
  );
}
