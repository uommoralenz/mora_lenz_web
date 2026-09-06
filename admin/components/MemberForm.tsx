import { ImageField } from "@/components/ImageField";
import type { MemberRecord, PanelPillarRecord } from "@/lib/types";
import Link from "next/link";

type MemberFormProps = {
  action: (formData: FormData) => Promise<void>;
  member?: MemberRecord;
  panelsPillars?: PanelPillarRecord[];
  submitLabel: string;
};

export function MemberForm({ action, member, panelsPillars = [], submitLabel }: MemberFormProps) {
  const currentPanelPillar = member?.pillar_or_panel ?? "";
  const hasCurrentPanelPillar =
    currentPanelPillar.length > 0 && panelsPillars.some((item) => item.name === currentPanelPillar);

  return (
    <form action={action} className="form-panel" encType="multipart/form-data">
      {member ? <input type="hidden" name="id" value={member.id} /> : null}

      <div className="form-grid">
        <label>
          <span>First name</span>
          <input name="first_name" defaultValue={member?.first_name ?? ""} required maxLength={255} />
        </label>

        <label>
          <span>Last name</span>
          <input name="last_name" defaultValue={member?.last_name ?? ""} maxLength={255} />
        </label>

        <label>
          <span>Position</span>
          <input name="position" defaultValue={member?.position ?? ""} required maxLength={255} />
        </label>

        <label>
          <span>Pillar or panel</span>
          <select name="pillar_or_panel" defaultValue={currentPanelPillar}>
            <option value="">None</option>
            {!hasCurrentPanelPillar && currentPanelPillar ? (
              <option value={currentPanelPillar}>{currentPanelPillar}</option>
            ) : null}
            {panelsPillars.map((item) => (
              <option value={item.name} key={item.id}>
                {item.name}
              </option>
            ))}
          </select>
        </label>

        <label>
          <span>Card size</span>
          <select name="card_size" defaultValue={member?.card_size ?? "sm"}>
            <option value="sm">Small</option>
            <option value="md">Medium</option>
            <option value="lg">Large advisor card</option>
          </select>
        </label>

        <label>
          <span>Sort order</span>
          <input type="number" name="sort_order" defaultValue={member?.sort_order ?? 0} min="0" />
        </label>
      </div>

      <ImageField name="photo_url" label="Photo URL" type="members" defaultValue={member?.photo_url ?? ""} maxLength={255} />

      <label>
        <span>Bio</span>
        <textarea name="bio" defaultValue={member?.bio ?? ""} rows={5} />
      </label>

      <div className="form-actions">
        <Link href="/members" className="button button-muted">
          Cancel
        </Link>
        <button type="submit" className="button button-primary">
          {submitLabel}
        </button>
      </div>
    </form>
  );
}

