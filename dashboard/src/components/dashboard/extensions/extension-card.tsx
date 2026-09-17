import { getDashboardI18n, t } from "@/lib/i18n";
import ToggleSwitch from "../widgets/toggle-switch";
import type { ExtensionItem } from "./types";

type ExtensionCardProps = {
  extension: ExtensionItem;
  onToggle: (id: string, enabled: boolean) => void;
};

const ExtensionCard = ({ extension, onToggle }: ExtensionCardProps) => {
  const i18n = getDashboardI18n();
  const isProLocked = extension.isPro && extension.available === false;
  const upgradeUrl =
    extension.upgradeUrl || "https://pixels71.com/pixels-core-pro/";

  return (
    <article className="flex min-h-[108px] flex-col justify-between rounded-xl border border-slate-100 bg-white p-5">
      <div>
        <div className="flex items-start justify-between gap-3">
          <div className="min-w-0 flex-1">
            <div className="flex items-center gap-2">
              <h3 className="truncate text-sm font-semibold leading-5 text-slate-900">
                {extension.name}
              </h3>
              {extension.isPro ? (
                <span className="shrink-0 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800">
                  {i18n.pro}
                </span>
              ) : null}
            </div>
          </div>

          {isProLocked ? (
            <a
              href={upgradeUrl}
              target="_blank"
              rel="noreferrer"
              className="shrink-0 rounded-md bg-slate-900 px-2.5 py-1 text-[11px] font-medium text-white transition-colors hover:bg-slate-700"
            >
              {i18n.upgrade}
            </a>
          ) : (
            <ToggleSwitch
              checked={extension.enabled}
              onChange={(enabled) => onToggle(extension.id, enabled)}
              label={t("toggleItem", extension.name)}
              size="sm"
              disabled={extension.available === false}
            />
          )}
        </div>

        {extension.description ? (
          <p className="mt-2 line-clamp-2 text-xs leading-5 text-slate-500">
            {extension.description}
          </p>
        ) : null}
      </div>

    </article>
  );
};

export default ExtensionCard;
