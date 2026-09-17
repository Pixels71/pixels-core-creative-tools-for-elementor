import { Info } from "lucide-react";
import { getDashboardI18n, t } from "@/lib/i18n";
import ToggleSwitch from "../widgets/toggle-switch";
import type { FeatureItem } from "./types";

type FeatureCardProps = {
  feature: FeatureItem;
  onToggle: (id: string, enabled: boolean) => void;
};

const FeatureCard = ({ feature, onToggle }: FeatureCardProps) => {
  const i18n = getDashboardI18n();
  const forcedMessage = feature.forced
    ? feature.effective
      ? i18n.featureForcedOn
      : i18n.featureForcedOff
    : null;

  return (
    <article className="flex min-h-[108px] flex-col justify-between rounded-xl border border-slate-100 bg-white p-5">
      <div>
        <div className="flex items-start justify-between gap-3">
          <div className="min-w-0 flex-1">
            <div className="flex items-center gap-2">
              <h3 className="truncate text-sm font-semibold leading-5 text-slate-900">
                {feature.name}
              </h3>
              {feature.isPro ? (
                <span className="shrink-0 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800">
                  {i18n.pro}
                </span>
              ) : null}
            </div>
          </div>

          <ToggleSwitch
            checked={feature.enabled}
            onChange={(enabled) => onToggle(feature.id, enabled)}
            label={t("toggleItem", feature.name)}
            size="sm"
            disabled={feature.available === false}
          />
        </div>

        {feature.description ? (
          <p className="mt-2 text-xs leading-5 text-slate-500">
            {feature.description}
          </p>
        ) : null}

        {feature.impact ? (
          <p className="mt-2 flex items-start gap-1.5 text-[11px] leading-4 text-slate-400">
            <Info className="mt-0.5 size-3 shrink-0" strokeWidth={1.75} />
            <span>{feature.impact}</span>
          </p>
        ) : null}

        {forcedMessage ? (
          <p className="mt-2 rounded-md bg-amber-50 px-2 py-1 text-[11px] leading-4 text-amber-800">
            {forcedMessage}
          </p>
        ) : null}
      </div>
    </article>
  );
};

export default FeatureCard;
