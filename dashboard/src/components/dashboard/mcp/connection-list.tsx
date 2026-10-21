import { Bot, Trash2 } from "lucide-react";
import type { McpConnection } from "@/lib/api";
import { getDashboardI18n, t } from "@/lib/i18n";
import { cn } from "@/lib/utils";
import { relativeTime } from "./utils";

type ConnectionListProps = {
  connections: McpConnection[];
  now: number;
  revoking: string | null;
  onRevoke: (connection: McpConnection) => void;
};

const stateStyles: Record<McpConnection["state"], string> = {
  active: "bg-emerald-50 text-emerald-700 ring-emerald-200",
  idle: "bg-sky-50 text-sky-700 ring-sky-200",
  never: "bg-slate-100 text-slate-600 ring-slate-200",
};

const dotStyles: Record<McpConnection["state"], string> = {
  active: "bg-emerald-500",
  idle: "bg-sky-500",
  never: "bg-slate-400",
};

const ConnectionList = ({
  connections,
  now,
  revoking,
  onRevoke,
}: ConnectionListProps) => {
  const i18n = getDashboardI18n();
  const stateLabels: Record<McpConnection["state"], string> = {
    active: i18n.mcpStateActive,
    idle: i18n.mcpStateIdle,
    never: i18n.mcpStateNever,
  };

  return (
    <section className="rounded-2xl bg-white p-6">
      <h2 className="text-base font-semibold text-slate-900">
        {i18n.mcpConnectionsTitle}
      </h2>
      <p className="mt-1 text-sm text-slate-500">
        {i18n.mcpConnectionsDescription}
      </p>

      {connections.length === 0 ? (
        <div className="mt-5 rounded-xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
          {i18n.mcpNoConnections}
        </div>
      ) : (
        <ul className="mt-5 space-y-3">
          {connections.map((connection) => (
            <li
              key={connection.uuid}
              className="flex flex-wrap items-center gap-4 rounded-xl border border-slate-200 p-4"
            >
              <span className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-[#091146]/5 text-[#091146]">
                <Bot className="size-5" strokeWidth={1.75} />
              </span>

              <div className="min-w-0 flex-1">
                <div className="flex flex-wrap items-center gap-2">
                  <p className="truncate text-sm font-semibold text-slate-900">
                    {connection.client
                      ? `${connection.client}${connection.client_version ? ` ${connection.client_version}` : ""}`
                      : connection.name}
                  </p>
                  <span
                    className={cn(
                      "inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-medium ring-1",
                      stateStyles[connection.state],
                    )}
                  >
                    <span
                      className={cn(
                        "size-1.5 rounded-full",
                        dotStyles[connection.state],
                        connection.state === "active" && "animate-pulse",
                      )}
                    />
                    {stateLabels[connection.state]}
                  </span>
                </div>

                <p className="mt-1 text-xs text-slate-500">
                  {[
                    connection.client ? connection.name : null,
                    t("mcpUser", connection.user),
                    connection.last_seen
                      ? t("mcpLastSeen", relativeTime(connection.last_seen, now))
                      : connection.created
                        ? t("mcpCreatedOn", relativeTime(connection.created, now))
                        : null,
                    connection.tool_calls
                      ? t("mcpToolCalls", connection.tool_calls)
                      : null,
                    connection.last_tool
                      ? t("mcpLastTool", connection.last_tool)
                      : null,
                  ]
                    .filter(Boolean)
                    .join(" · ")}
                </p>
              </div>

              <button
                type="button"
                onClick={() => onRevoke(connection)}
                disabled={revoking === connection.uuid}
                className="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-60"
              >
                <Trash2 className="size-4" />
                {i18n.mcpRevoke}
              </button>
            </li>
          ))}
        </ul>
      )}
    </section>
  );
};

export default ConnectionList;
