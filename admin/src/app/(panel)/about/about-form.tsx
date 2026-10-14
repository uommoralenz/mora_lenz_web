"use client";

import { useActionState } from "react";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { Thumb } from "@/components/ui";
import { EMPTY_ACTION_STATE, EVENT_PAGE_DEFAULTS, type PageItem } from "@/lib/types";

import BlockEditor from "../events/block-editor";
import PageOptionsEditor from "../events/page-options";
import { saveAboutAction } from "./actions";

/**
 * The About page is fully custom: the site draws nothing but the blocks below,
 * between its navbar and footer. Title, description and image only feed the
 * browser tab, search results and link previews.
 */
export default function AboutForm({ page }: { page: PageItem }) {
  const [state, formAction] = useActionState(saveAboutAction, EMPTY_ACTION_STATE);

  return (
    <form action={formAction} className="space-y-5">
      <FormMessage state={state} />

      <div>
        <span className="label">Page content</span>
        <p className="hint mb-3 mt-0">
          This is the whole page &mdash; nothing else is added above it. To paste a page you
          designed elsewhere, add one <strong>Custom HTML</strong> block, set it to{" "}
          <em>Full width</em>, and set <em>Content width</em> below to <em>Full width</em> too.
        </p>
        <BlockEditor initial={page.content ?? []} />
        <FieldError errors={state.errors} name="content" />
      </div>

      <div>
        <span className="label">Page layout</span>
        <PageOptionsEditor
          initial={{ ...EVENT_PAGE_DEFAULTS, ...page.page_options }}
          showMetaOption={false}
          showHeroOption={false}
        />
        <FieldError errors={state.errors} name="page_options" />
      </div>

      <label className="flex items-start gap-3 text-sm">
        <input
          type="checkbox"
          name="show_teams"
          value="1"
          defaultChecked={page.page_options.show_teams ?? false}
          className="mt-0.5"
        />
        <span>
          <span className="font-medium text-slate-200">
            Add &ldquo;Our Teams Through the Years&rdquo; under the content
          </span>
          <span className="block text-xs text-slate-500">
            A card for every year&rsquo;s team, linking to its page. Leave unticked to keep the
            page exactly as you designed it.
          </span>
        </span>
      </label>

      <fieldset className="space-y-4 rounded-lg border border-ink-700 p-4">
        <legend className="px-1 text-xs uppercase tracking-wide text-slate-500">
          Search &amp; link previews (not shown on the page)
        </legend>

        <div>
          <label className="label" htmlFor="title">
            Page title
          </label>
          <input
            id="title"
            name="title"
            className="input"
            defaultValue={page.title}
            maxLength={200}
            required
          />
          <p className="hint">Shown in the browser tab and in Google results.</p>
          <FieldError errors={state.errors} name="title" />
        </div>

        <div>
          <label className="label" htmlFor="description">
            Description
          </label>
          <textarea
            id="description"
            name="description"
            className="textarea min-h-20"
            defaultValue={page.description ?? ""}
          />
          <p className="hint">The short summary search engines and shared links show.</p>
          <FieldError errors={state.errors} name="description" />
        </div>

        <div>
          <label className="label" htmlFor="image">
            Link preview image
          </label>
          {page.image_url ? (
            <div className="mb-2 flex items-center gap-3">
              <Thumb src={page.image_url} alt={page.title} className="h-20 w-28" />
              <label className="flex items-center gap-2 text-xs text-slate-400">
                <input type="checkbox" name="remove_image" value="1" />
                Remove this image
              </label>
            </div>
          ) : null}
          <input
            id="image"
            name="image"
            type="file"
            accept="image/*"
            className="input file:mr-3 file:rounded file:border-0 file:bg-ink-700 file:px-3 file:py-1 file:text-slate-200"
          />
          <p className="hint">Shown when the link is shared on WhatsApp or Facebook. Max 8 MB.</p>
          <FieldError errors={state.errors} name="image" />
        </div>
      </fieldset>

      <SubmitButton>Save About page</SubmitButton>
    </form>
  );
}
