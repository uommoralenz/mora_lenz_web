import type { PanelPillarRecord } from "@/lib/types";
import Link from "next/link";

const iconOptions = [
  "users",
  "camera",
  "video",
  "dollar-sign",
  "user-check",
  "message-circle",
  "file-text",
  "pen-tool",
  "mic",
  "award",
  "calendar",
  "book-open",
];

type PanelPillarFormProps = {
  action: (formData: FormData) => Promise<void>;
  panelPillar?: PanelPillarRecord;
  submitLabel: string;
};

export function PanelPillarForm({ action, panelPillar, submitLabel }: PanelPillarFormProps) {
  return (
    <form action={action} className="form-panel">
      {panelPillar ? <input type="hidden" name="id" value={panelPillar.id} /> : null}

      <div className="form-grid">
        <label>
          <span>Name</span>
          <input name="name" defaultValue={panelPillar?.name ?? ""} required maxLength={255} />
        </label>

        <label>
          <span>Type</span>
          <select name="type" defaultValue={panelPillar?.type ?? "panel"}>
            <option value="panel">Panel</option>
            <option value="pillar">Pillar</option>
          </select>
        </label>

        <label>
          <span>Icon</span>
          <select name="icon" defaultValue={panelPillar?.icon ?? "users"}>
            {iconOptions.map((icon) => (
              <option value={icon} key={icon}>
                {icon}
              </option>
            ))}
          </select>
        </label>

        <label>
          <span>Member count</span>
          <input type="number" name="member_count" defaultValue={panelPillar?.member_count ?? 0} min="0" />
        </label>

        <label>
          <span>Sort order</span>
          <input type="number" name="sort_order" defaultValue={panelPillar?.sort_order ?? 0} min="0" />
        </label>
      </div>

      <label>
        <span>Description</span>
        <textarea name="description" defaultValue={panelPillar?.description ?? ""} rows={5} />
      </label>

      <div className="form-actions">
        <Link href="/panels-pillars" className="button button-muted">
          Cancel
        </Link>
        <button type="submit" className="button button-primary">
          {submitLabel}
        </button>
      </div>
    </form>
  );
}
