import { Copy, Plus } from "lucide-react";
import { useCallback, useEffect, useState } from "react";
import { useToast } from "@/components/ui/toast";
import {
  fetchMcpHealth,
  fetchMcpStatus,
  revokeMcpConnection,
  setMcpServerEnabled,
  type McpConnection,
  type McpHealthCheck,
  type McpStatus,
} from "@/lib/api";
import { getDashboardI18n, t } from "@/lib/i18n";
import { cn } from "@/lib/utils";
import ToggleSwitch from "../widgets/toggle-switch";
import ConnectWizard from "./connect-wizard";
import ConnectionList from "./connection-list";
import HealthCheck from "./health-check";
import { copyText } from "./utils";

/** Refresh the list now and then so "Active" turns into "Connected" without a reload. */
const REFRESH_MS = 30000;

const McpPanel = () => {
  const i18n = getDashboardI18n();
  const toast = useToast();
  const [status, setStatus] = useState<McpStatus | null>(null);
  const [checks, setChecks] = useState<McpHealthCheck[] | null>(null);
  const [error, setError] = useState<string | null>(null);
  // The first check starts on mount.
  const [isChecking, setIsChecking] = useState(true);
  const [isToggling, setIsToggling] = useState(false);
  const [revoking, setRevoking] = useState<string | null>(null);
  const [wizardOpen, setWizardOpen] = useState(false);
  const [wizardKey, setWizardKey] = useState(0);

  const loadStatus = useCallback(async () => {
    try {
      setStatus(await fetchMcpStatus());
      setError(null);
    } catch (loadError) {
      setError(
        loadError instanceof Error ? loadError.message : i18n.mcpFailedLoad,
      );
    }
  }, [i18n.mcpFailedLoad]);

  const checkHealth = useCallback(async () => {
    try {
      setChecks(await fetchMcpHealth());
    } catch (checkError) {
      toast.error(
        checkError instanceof Error ? checkError.message : i18n.requestFailed,
      );
    } finally {
      setIsChecking(false);
    }
  }, [i18n.requestFailed, toast]);

  const runHealthCheck = () => {
    setIsChecking(true);
    void checkHealth();
  };

  useEffect(() => {
    const load = async () => {
      await Promise.all([loadStatus(), checkHealth()]);
    };

    void load();

    const timer = window.setInterval(() => void loadStatus(), REFRESH_MS);

    return () => window.clearInterval(timer);
  }, [loadStatus, checkHealth]);

  const handleToggleServer = async (enabled: boolean) => {
    setIsToggling(true);

    try {
      await setMcpServerEnabled(enabled);
      toast.success(enabled ? i18n.mcpServerEnabled : i18n.mcpServerDisabled);
      await loadStatus();
      runHealthCheck();
    } catch (toggleError) {
      toast.error(
        toggleError instanceof Error ? toggleError.message : i18n.requestFailed,
      );
    } finally {
      setIsToggling(false);
    }
  };

  const handleRevoke = async (connection: McpConnection) => {
    const label = connection.client || connection.name;

    if (!window.confirm(t("mcpRevokeConfirm", label))) {
      return;
    }

    setRevoking(connection.uuid);

    try {
      const connections = await revokeMcpConnection(connection.uuid);
      setStatus((previous) =>
        previous ? { ...previous, connections } : previous,
      );
      toast.success(i18n.mcpRevoked);
    } catch (revokeError) {
      toast.error(
        revokeError instanceof Error ? revokeError.message : i18n.requestFailed,
      );
    } finally {
      setRevoking(null);
    }
  };

  const handleCopyEndpoint = async () => {
    if (status && (await copyText(status.endpoint))) {
      toast.success(i18n.mcpCopied);
    }
  };

  if (!status) {
    return (
      <div
        className={cn(
          "rounded-xl border p-8 text-sm",
          error
            ? "border-red-200 bg-red-50 text-red-700"
            : "border-slate-200 bg-white text-slate-500",
        )}
      >
        {error ?? i18n.mcpLoading}
      </div>
    );
  }

  const activeCount = status.connections.filter(
    (connection) => connection.state === "active",
  ).length;

  return (
    <div className="space-y-6">
      <section className="rounded-2xl bg-white p-6">
        <div className="flex flex-wrap items-start justify-between gap-4">
          <div className="min-w-0">
            <div className="flex flex-wrap items-center gap-2">
              <h2 className="text-base font-semibold text-slate-900">
                {i18n.mcpServerTitle}
              </h2>
              {status.enabled && status.connections.length > 0 ? (
                <span
                  className={cn(
                    "inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-medium ring-1",
                    activeCount > 0
                      ? "bg-emerald-50 text-emerald-700 ring-emerald-200"
                      : "bg-slate-100 text-slate-600 ring-slate-200",
                  )}
                >
                  <span
                    className={cn(
                      "size-1.5 rounded-full",
                      activeCount > 0
                        ? "animate-pulse bg-emerald-500"
                        : "bg-slate-400",
                    )}
                  />
                  {activeCount > 0
                    ? t("mcpSummaryActive", activeCount)
                    : t("mcpSummaryTotal", status.connections.length)}
                </span>
              ) : null}
            </div>
            <p className="mt-1 text-sm text-slate-500">
              {status.enabled ? i18n.mcpServerOn : i18n.mcpServerOff}
            </p>
          </div>

          <ToggleSwitch
            checked={status.enabled}
            onChange={(enabled) => void handleToggleServer(enabled)}
            label={i18n.mcpToggleServer}
            disabled={isToggling}
          />
        </div>

        {status.enabled ? (
          <div className="mt-5 flex flex-wrap items-center gap-3">
            <div className="flex min-w-0 flex-1 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 py-1.5 pr-1.5 pl-3">
              <span className="shrink-0 text-xs font-medium text-slate-500">
                {i18n.mcpEndpointLabel}
              </span>
              <code className="min-w-0 flex-1 truncate text-xs text-slate-800">
                {status.endpoint}
              </code>
              <button
                type="button"
                onClick={() => void handleCopyEndpoint()}
                className="inline-flex shrink-0 items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-slate-600 transition-colors hover:bg-white"
              >
                <Copy className="size-3.5" />
                {i18n.mcpCopy}
              </button>
            </div>

            <button
              type="button"
              onClick={() => {
                setWizardKey((key) => key + 1);
                setWizardOpen(true);
              }}
              className="inline-flex items-center gap-2 rounded-lg bg-[#091146] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#091146]/80"
            >
              <Plus className="size-4" />
              {i18n.mcpConnectClient}
            </button>
          </div>
        ) : (
          <p className="mt-4 text-sm text-slate-500">{i18n.mcpEnableFirst}</p>
        )}
      </section>

      <HealthCheck
        checks={checks}
        isRunning={isChecking}
        onRun={runHealthCheck}
      />

      <ConnectionList
        connections={status.connections}
        now={status.now}
        revoking={revoking}
        onRevoke={(connection) => void handleRevoke(connection)}
      />

      <ConnectWizard
        key={wizardKey}
        open={wizardOpen}
        clients={status.clients}
        onOpenChange={setWizardOpen}
        onConnectionsChanged={loadStatus}
      />
    </div>
  );
};

export default McpPanel;
