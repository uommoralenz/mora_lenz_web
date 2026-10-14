"use client";

import { useActionState } from "react";

import { FieldError, FormMessage, SubmitButton } from "@/components/form";
import { Thumb } from "@/components/ui";
import { EMPTY_ACTION_STATE, EVENT_PAGE_DEFAULTS, type PageItem } from "@/lib/types";

import BlockEditor from "../events/block-editor";
import PageOptionsEditor from "../events/page-options";
import { saveAboutAction } from "./actions";

export default function AboutForm({ page }: { page: PageItem }) {
  const [state, formAction] = useActionState(saveAboutAction, EMPTY_ACTION_STATE);

  return (
    <form action={formAction} className="space-y-5">
      <FormMessage state={state} />

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
        <FieldError errors={state.errors} name="title" />
      </div>

      <div>
        <label className="label" htmlFor="description">
          Intro text
        </label>
        <textarea
          id="description"
          name="description"
          className="textarea min-h-32"
          defaultValue={page.description ?? ""}
        />
        <p className="hint">
          Shown first on the page and in search results. Leave a blank line between paragraphs.
        </p>
        <FieldError errors={state.errors} name="description" />
      </div>

      <div>
        <label className="label" htmlFor="image">
          Cover image
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
        <p className="hint">Shown behind the title. Landscape works best. Max 8 MB.</p>
        <FieldError errors={state.errors} name="image" />
      </div>

      <div>
        <span className="label">Page appearance</span>
        <PageOptionsEditor
          initial={{ ...EVENT_PAGE_DEFAULTS, ...page.page_options }}
          showMetaOption={false}
        />
        <FieldError errors={state.errors} name="page_options" />
      </div>

      <div>
        <span className="label">Page content</span>
        <p className="hint mb-3 mt-0">
          Build the page from headings, text, lists, quotes, images, videos, buttons and custom
          HTML, in any order. The &ldquo;Teams through the years&rdquo; list is added below it
          automatically.
        </p>
        <BlockEditor initial={page.content ?? []} />
        <FieldError errors={state.errors} name="content" />
      </div>

      <SubmitButton>Save About page</SubmitButton>
    </form>
  );
}
