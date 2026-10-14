import Link from "next/link";

import Disclosure from "@/components/disclosure";
import { Badge, Card, EmptyState, PageHeader, SectionTitle } from "@/components/ui";
import { api } from "@/lib/api";
import type { TeamTermItem } from "@/lib/types";

import { TermForm } from "./term-forms";

export const dynamic = "force-dynamic";

function counts(term: TeamTermItem) {
  return `${term.group_count} group${term.group_count === 1 ? "" : "s"} · ${term.member_count} member${
    term.member_count === 1 ? "" : "s"
  }`;
}

/** Every year's team. Open one to edit its groups and members. */
export default async function TeamsPage() {
  const { terms } = await api.get<{ terms: TeamTermItem[] }>("/team");

  const current = terms.find((t) => t.is_current) ?? null;
  const past = terms.filter((t) => t.id !== current?.id);

  return (
    <>
      <PageHeader
        title="Teams"
        description="Each year's team is kept separately with its own page on the site. Open a team to edit its groups and members."
      />

      <Card className="mb-6">
        <Disclosure label="New team year" variant="primary">
          <TermForm terms={terms} />
        </Disclosure>
      </Card>

      {terms.length === 0 ? (
        <EmptyState
          title="No teams yet."
          hint="Create the first team year, then add its groups and members."
        />
      ) : null}

      {current ? (
        <section className="mb-8">
          <SectionTitle>Current team</SectionTitle>
          <Link
            href={`/team/${current.id}`}
            className="card flex flex-wrap items-center gap-4 border-sky-600/50 p-5 transition-colors hover:border-sky-500 hover:bg-ink-800"
          >
            <div className="min-w-0 flex-1">
              <div className="flex flex-wrap items-center gap-2">
                <p className="text-xl font-semibold text-slate-100">{current.name}</p>
                <Badge tone="info">Current</Badge>
                {!current.is_active ? <Badge tone="danger">Hidden</Badge> : null}
              </div>
              <p className="mt-1 text-sm text-slate-500">{counts(current)} · shown at /team</p>
            </div>
            <span className="btn-primary">Edit team</span>
          </Link>
        </section>
      ) : null}

      {past.length > 0 ? (
        <section>
          <SectionTitle>Past teams ({past.length})</SectionTitle>
          <ul className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {past.map((term) => (
              <li key={term.id}>
                <Link
                  href={`/team/${term.id}`}
                  className="card block h-full p-4 transition-colors hover:border-ink-500 hover:bg-ink-800"
                >
                  <div className="flex flex-wrap items-center gap-2">
                    <p className="font-medium text-slate-100">{term.name}</p>
                    {!term.is_active ? <Badge tone="danger">Hidden</Badge> : null}
                  </div>
                  <p className="mt-1 text-xs text-slate-500">{counts(term)}</p>
                  <p className="mt-0.5 truncate text-xs text-slate-600">/team/{term.slug}</p>
                </Link>
              </li>
            ))}
          </ul>
        </section>
      ) : null}
    </>
  );
}
