import Link from "next/link";

import Disclosure from "@/components/disclosure";
import { ConfirmSubmit } from "@/components/form";
import { Badge, Card, EmptyState, PageHeader, Thumb } from "@/components/ui";
import { api } from "@/lib/api";
import type { TeamGroupItem, TeamMemberItem, TeamTermItem } from "@/lib/types";

import {
  deleteGroupAction,
  deleteMemberAction,
  deleteSubgroupAction,
  reorderGroupsAction,
  reorderMembersAction,
  reorderSubgroupsAction,
} from "./actions";
import { GroupForm, MemberForm, SubgroupForm } from "./team-forms";
import { DeleteTermForm, TermForm } from "./term-forms";

export const dynamic = "force-dynamic";

function swapPayload<T extends { id: number }>(items: T[], index: number, delta: number) {
  const other = index + delta;

  if (other < 0 || other >= items.length) return null;

  return JSON.stringify([
    { id: items[index].id, sort_order: other },
    { id: items[other].id, sort_order: index },
  ]);
}

function MemberRow({
  member,
  siblings,
  index,
  groups,
}: {
  member: TeamMemberItem;
  siblings: TeamMemberItem[];
  index: number;
  groups: TeamGroupItem[];
}) {
  const up = swapPayload(siblings, index, -1);
  const down = swapPayload(siblings, index, 1);

  return (
    <li className="rounded-lg border border-ink-700 p-3">
      <div className="flex flex-wrap items-center gap-3">
        <Thumb
          src={member.image_url}
          alt={member.name}
          className="h-11 w-11 rounded-full"
        />

        <div className="min-w-0 flex-1">
          <div className="flex flex-wrap items-center gap-2">
            <p className="truncate text-sm font-medium text-slate-100">{member.name}</p>
            {!member.is_active ? <Badge tone="danger">Hidden</Badge> : null}
          </div>
          <p className="truncate text-xs text-slate-500">{member.profession}</p>
        </div>

        <div className="flex items-center gap-1">
          <form action={reorderMembersAction}>
            <input type="hidden" name="items" value={up ?? ""} />
            <button type="submit" className="btn-ghost" disabled={!up} aria-label="Move up">
              ↑
            </button>
          </form>
          <form action={reorderMembersAction}>
            <input type="hidden" name="items" value={down ?? ""} />
            <button
              type="submit"
              className="btn-ghost"
              disabled={!down}
              aria-label="Move down"
            >
              ↓
            </button>
          </form>
          <form action={deleteMemberAction}>
            <input type="hidden" name="id" value={member.id} />
            <ConfirmSubmit
              className="btn-ghost text-rose-400 hover:text-rose-300"
              confirm={`Remove ${member.name} from the team?`}
            >
              Delete
            </ConfirmSubmit>
          </form>
        </div>
      </div>

      <div className="mt-3 border-t border-ink-700 pt-3">
        <Disclosure label="Edit" variant="ghost">
          <MemberForm
            groupId={member.group_id}
            subgroupId={member.subgroup_id}
            groups={groups}
            member={member}
          />
        </Disclosure>
      </div>
    </li>
  );
}

export default async function TeamPage({
  searchParams,
}: {
  searchParams: Promise<{ term?: string }>;
}) {
  const { term: termParam } = await searchParams;
  const query = termParam && /^\d+$/.test(termParam) ? `?term=${termParam}` : "";

  const {
    data: groups,
    terms,
    term,
  } = await api.get<{ data: TeamGroupItem[]; terms: TeamTermItem[]; term: TeamTermItem | null }>(
    `/team${query}`
  );

  return (
    <>
      <PageHeader
        title="Team"
        description="Each year's team is kept separately with its own page on the site. Pick a year, then build it from groups, optional subgroups and members."
      />

      {/* Team years */}
      <Card className="mb-6">
        <div className="flex flex-wrap items-center gap-2">
          {terms.map((t) => (
            <Link
              key={t.id}
              href={`/team?term=${t.id}`}
              className={`rounded-full border px-3 py-1 text-sm transition-colors ${
                term?.id === t.id
                  ? "border-sky-500 bg-sky-600/15 text-sky-200"
                  : "border-ink-700 text-slate-400 hover:border-ink-500 hover:text-slate-200"
              }`}
            >
              {t.name}
              {t.is_current ? <span className="ml-1.5 text-xs text-sky-400">current</span> : null}
              {!t.is_active ? <span className="ml-1.5 text-xs text-rose-400">hidden</span> : null}
            </Link>
          ))}
        </div>

        {term ? (
          <p className="mt-3 text-xs text-slate-500">
            Editing the <span className="text-slate-300">{term.name}</span> team ·{" "}
            {term.is_current ? "shown at /team" : `shown at /team/${term.slug}`}
          </p>
        ) : null}

        <div className="mt-4 flex flex-wrap gap-2">
          <Disclosure label="New team year" variant="primary">
            <TermForm terms={terms} />
          </Disclosure>
          {term ? (
            <Disclosure label="Edit this year" variant="ghost">
              <div className="space-y-6">
                <TermForm key={term.id} term={term} terms={terms} />
                <div className="border-t border-ink-700 pt-4">
                  <DeleteTermForm term={term} />
                </div>
              </div>
            </Disclosure>
          ) : null}
        </div>
      </Card>

      {!term ? (
        <EmptyState
          title="No team years yet."
          hint="Create the first team year, then add its groups and members."
        />
      ) : (
        <>
      <Card className="mb-6">
        <Disclosure label="New group" variant="primary">
          <GroupForm termId={term.id} />
        </Disclosure>
      </Card>

      {groups.length === 0 ? (
        <EmptyState
          title="No groups in this year's team yet."
          hint="Create a group first — members live inside groups."
        />
      ) : (
        <div className="space-y-6">
          {groups.map((group, groupIndex) => {
            const up = swapPayload(groups, groupIndex, -1);
            const down = swapPayload(groups, groupIndex, 1);

            return (
              <Card key={group.id}>
                {/* Group header */}
                <div className="flex flex-wrap items-center gap-3 border-b border-ink-700 pb-4">
                  <div className="min-w-0 flex-1">
                    <div className="flex flex-wrap items-center gap-2">
                      <h2 className="truncate text-lg font-semibold text-slate-100">
                        {group.name}
                      </h2>
                      {!group.is_active ? <Badge tone="danger">Hidden</Badge> : null}
                    </div>
                    {group.description ? (
                      <p className="truncate text-xs text-slate-500">
                        {group.description}
                      </p>
                    ) : null}
                  </div>

                  <div className="flex items-center gap-1">
                    <form action={reorderGroupsAction}>
                      <input type="hidden" name="items" value={up ?? ""} />
                      <button
                        type="submit"
                        className="btn-ghost"
                        disabled={!up}
                        aria-label="Move group up"
                      >
                        ↑
                      </button>
                    </form>
                    <form action={reorderGroupsAction}>
                      <input type="hidden" name="items" value={down ?? ""} />
                      <button
                        type="submit"
                        className="btn-ghost"
                        disabled={!down}
                        aria-label="Move group down"
                      >
                        ↓
                      </button>
                    </form>
                    <form action={deleteGroupAction}>
                      <input type="hidden" name="id" value={group.id} />
                      <ConfirmSubmit
                        confirm={`Delete the "${group.name}" group? Every subgroup and member inside it is deleted too. This cannot be undone.`}
                      >
                        Delete group
                      </ConfirmSubmit>
                    </form>
                  </div>
                </div>

                <div className="mt-4 flex flex-wrap gap-2">
                  <Disclosure label="Edit group" variant="ghost">
                    <div className="mb-4">
                      <GroupForm group={group} />
                    </div>
                  </Disclosure>
                </div>

                {/* Members directly in the group */}
                <div className="mt-4">
                  <h3 className="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    In this group ({group.direct_members.length})
                  </h3>

                  {group.direct_members.length > 0 ? (
                    <ul className="space-y-2">
                      {group.direct_members.map((member, index) => (
                        <MemberRow
                          key={member.id}
                          member={member}
                          siblings={group.direct_members}
                          index={index}
                          groups={groups}
                        />
                      ))}
                    </ul>
                  ) : (
                    <p className="text-sm text-slate-500">
                      No members placed directly in this group.
                    </p>
                  )}

                  <div className="mt-3">
                    <Disclosure label="Add member here">
                      <MemberForm groupId={group.id} groups={groups} />
                    </Disclosure>
                  </div>
                </div>

                {/* Subgroups */}
                {group.subgroups.map((subgroup, subIndex) => {
                  const subUp = swapPayload(group.subgroups, subIndex, -1);
                  const subDown = swapPayload(group.subgroups, subIndex, 1);

                  return (
                    <div
                      key={subgroup.id}
                      className="mt-5 rounded-lg border border-ink-700 bg-ink-950/40 p-4"
                    >
                      <div className="flex flex-wrap items-center gap-3">
                        <div className="min-w-0 flex-1">
                          <div className="flex flex-wrap items-center gap-2">
                            <h3 className="truncate font-medium text-slate-100">
                              {subgroup.name}
                            </h3>
                            {!subgroup.is_active ? (
                              <Badge tone="danger">Hidden</Badge>
                            ) : null}
                            <Badge>{subgroup.members.length} members</Badge>
                          </div>
                          {subgroup.description ? (
                            <p className="truncate text-xs text-slate-500">
                              {subgroup.description}
                            </p>
                          ) : null}
                        </div>

                        <div className="flex items-center gap-1">
                          <form action={reorderSubgroupsAction}>
                            <input type="hidden" name="items" value={subUp ?? ""} />
                            <button
                              type="submit"
                              className="btn-ghost"
                              disabled={!subUp}
                              aria-label="Move subgroup up"
                            >
                              ↑
                            </button>
                          </form>
                          <form action={reorderSubgroupsAction}>
                            <input type="hidden" name="items" value={subDown ?? ""} />
                            <button
                              type="submit"
                              className="btn-ghost"
                              disabled={!subDown}
                              aria-label="Move subgroup down"
                            >
                              ↓
                            </button>
                          </form>
                          <form action={deleteSubgroupAction}>
                            <input type="hidden" name="id" value={subgroup.id} />
                            <ConfirmSubmit
                              className="btn-ghost text-rose-400 hover:text-rose-300"
                              confirm={`Delete the "${subgroup.name}" subgroup? Its members are kept and moved up into "${group.name}".`}
                            >
                              Delete
                            </ConfirmSubmit>
                          </form>
                        </div>
                      </div>

                      {subgroup.members.length > 0 ? (
                        <ul className="mt-3 space-y-2">
                          {subgroup.members.map((member, index) => (
                            <MemberRow
                              key={member.id}
                              member={member}
                              siblings={subgroup.members}
                              index={index}
                              groups={groups}
                            />
                          ))}
                        </ul>
                      ) : (
                        <p className="mt-3 text-sm text-slate-500">
                          No members in this subgroup yet.
                        </p>
                      )}

                      <div className="mt-3 flex flex-wrap gap-2">
                        <Disclosure label="Add member here">
                          <MemberForm
                            groupId={group.id}
                            subgroupId={subgroup.id}
                            groups={groups}
                          />
                        </Disclosure>
                        <Disclosure label="Edit subgroup" variant="ghost">
                          <SubgroupForm
                            groupId={group.id}
                            groups={groups}
                            subgroup={subgroup}
                          />
                        </Disclosure>
                      </div>
                    </div>
                  );
                })}

                <div className="mt-5 border-t border-ink-700 pt-4">
                  <Disclosure label="New subgroup">
                    <SubgroupForm groupId={group.id} groups={groups} />
                  </Disclosure>
                </div>
              </Card>
            );
          })}
        </div>
      )}
        </>
      )}
    </>
  );
}
