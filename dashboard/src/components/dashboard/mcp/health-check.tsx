import { CircleAlert, CircleCheck, CircleX, RefreshCw } from "lucide-react";
import type { McpHealthCheck } from "@/lib/api";
import { getDashboardI18n } from "@/lib/i18n";
import { cn } from "@/lib/utils";

type HealthCheckProps = {
  checks: McpHealthCheck[] | null;
  isRunning: boolean;
  onRun: () => void;
};

const statusIcon = {
  pass: { icon: CircleCheck, className: "text-emerald-600" },
  warn: { icon: CircleAlert, className: "text-amber-500" },
  fail: { icon: CircleX, className: "text-red-600" },
};

const HealthCheck = ({ checks, isRunning, onRun }: HealthCheckProps) => {
  const i18n = getDashboardI18n();
  const failing = checks?.some((check) => check.status === "fail") ?? false;

  return (
    <section className="rounded-2xl bg-white p-6">
      <div className="flex flex-wrap items-start justify-between gap-4">
        <div>
          <h2 className="text-base font-semibold text-slate-900">
            {i18n.mcpHealthTitle}
          </h2>
          <p className="mt-1 text-sm text-slate-500">
            {i18n.mcpHealthDescription}
          </p>
        </div>
        <button
          type="button"
          onClick={onRun}
          disabled={isRunning}
          className="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3.5 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
        >
          <RefreshCw className={cn("size-4", isRunning && "animate-spin")} />
          {isRunning ? i18n.mcpRunningCheck : i18n.mcpRunCheck}
        </button>
      </div>

      {checks ? (
        <>
          <p
            className={cn(
              "mt-4 rounded-lg px-3.5 py-2.5 text-sm font-medium",
              failing
                ? "bg-red-50 text-red-700"
                : "bg-emerald-50 text-emerald-700",
            )}
          >
            {failing ? i18n.mcpHealthProblems : i18n.mcpHealthAllGood}
          </p>

          <ul className="mt-4 divide-y divide-slate-100">
            {checks.map((check) => {
              const { icon: Icon, className } = statusIcon[check.status];

              return (
                <li key={check.id} className="flex gap-3 py-3">
                  <Icon className={cn("mt-0.5 size-5 shrink-0", className)} />
                  <div className="min-w-0">
                    <p className="text-sm font-medium text-slate-800">
                      {check.label}
                    </p>
                    <p className="mt-0.5 text-sm text-slate-500">
                      {check.message}
                    </p>
                  </div>
                </li>
              );
            })}
          </ul>
        </>
      ) : null}
    </section>
  );
};

export default HealthCheck;
