import { Check, CircleCheck, Copy, KeyRound, Loader2 } from "lucide-react";
import { useEffect, useState } from "react";
import { useToast } from "@/components/ui/toast";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogTitle,
} from "@/components/ui/dialog";
import {
  createMcpConnection,
  fetchMcpStatus,
  type McpClientKey,
  type McpConnection,
  type McpCreatedConnection,
} from "@/lib/api";
import { getDashboardI18n, t } from "@/lib/i18n";
import { cn } from "@/lib/utils";
import { clientHints, copyText } from "./utils";

/** Mount with a new `key` each time it opens so every visit starts at step one. */
type ConnectWizardProps = {
  open: boolean;
  clients: Record<McpClientKey, string>;
  onOpenChange: (open: boolean) => void;
  /** Called whenever the wizard learns something that changes the connection list. */
  onConnectionsChanged: () => void;
};

/** How often to look for the new client's first request. */
const POLL_MS = 3000;

const ConnectWizard = ({
  open,
  clients,
  onOpenChange,
  onConnectionsChanged,
}: ConnectWizardProps) => {
  const i18n = getDashboardI18n();
  const toast = useToast();
  const hints = clientHints();
  const [client, setClient] = useState<McpClientKey>("claude-code");
  const [created, setCreated] = useState<McpCreatedConnection | null>(null);
  const [connected, setConnected] = useState<McpConnection | null>(null);
  const [isCreating, setIsCreating] = useState(false);
  const [copied, setCopied] = useState(false);

  // Wait for the first MCP request made with the new password.
  useEffect(() => {
    if (!open || !created || connected) {
      return;
    }

    const uuid = created.connection.uuid;
    const timer = window.setInterval(async () => {
      try {
        const status = await fetchMcpStatus();
        const match = status.connections.find((item) => item.uuid === uuid);

        if (match && match.last_seen > 0) {
          setConnected(match);
          onConnectionsChanged();
        }
      } catch {
        // Keep waiting; a failed poll is not worth interrupting the user for.
      }
    }, POLL_MS);

    return () => window.clearInterval(timer);
  }, [open, created, connected, onConnectionsChanged]);

  const handleCreate = async () => {
    setIsCreating(true);

    try {
      setCreated(await createMcpConnection(client));
      onConnectionsChanged();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : i18n.requestFailed);
    } finally {
      setIsCreating(false);
    }
  };

  const handleCopy = async () => {
    if (created && (await copyText(created.setup.snippet))) {
      setCopied(true);
      toast.success(i18n.mcpCopied);
    }
  };

  const clientName = clients[client];

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="fixed top-1/2 left-1/2 z-50 max-h-[90vh] w-[calc(100%-2rem)] max-w-2xl -translate-x-1/2 -translate-y-1/2 overflow-y-auto rounded-2xl bg-white p-6 shadow-xl outline-none">
        <DialogTitle className="pr-8 text-lg font-semibold text-slate-900">
          {created ? t("mcpWizardSetupTitle", clientName) : i18n.mcpWizardTitle}
        </DialogTitle>
        <DialogDescription className="mt-1 text-sm text-slate-500">
          {created ? created.setup.where : i18n.mcpWizardChooseClient}
        </DialogDescription>

        {!created ? (
          <>
            <div className="mt-5 grid gap-3 sm:grid-cols-2">
              {(Object.keys(clients) as McpClientKey[]).map((key) => (
                <button
                  key={key}
                  type="button"
                  onClick={() => setClient(key)}
                  aria-pressed={client === key}
                  className={cn(
                    "rounded-xl border p-4 text-left transition-colors",
                    client === key
                      ? "border-[#091146] bg-[#091146]/5 ring-1 ring-[#091146]"
                      : "border-slate-200 hover:border-slate-300",
                  )}
                >
                  <span className="block text-sm font-semibold text-slate-900">
                    {clients[key]}
                  </span>
                  <span className="mt-0.5 block text-xs text-slate-500">
                    {hints[key]}
                  </span>
                </button>
              ))}
            </div>

            <p className="mt-4 text-xs text-slate-500">{i18n.mcpClaudeAiSoon}</p>

            <div className="mt-6 flex justify-end">
              <button
                type="button"
                onClick={() => void handleCreate()}
                disabled={isCreating}
                className="inline-flex items-center gap-2 rounded-lg bg-[#091146] px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#091146]/80 disabled:cursor-not-allowed disabled:opacity-60"
              >
                {isCreating ? (
                  <Loader2 className="size-4 animate-spin" />
                ) : (
                  <KeyRound className="size-4" />
                )}
                {isCreating ? i18n.mcpWizardCreating : i18n.mcpWizardCreate}
              </button>
            </div>
          </>
        ) : (
          <>
            <div className="relative mt-5">
              <pre className="max-h-64 overflow-auto rounded-xl bg-slate-900 p-4 pr-24 text-xs leading-relaxed whitespace-pre-wrap break-all text-slate-100">
                {created.setup.snippet}
              </pre>
              <button
                type="button"
                onClick={() => void handleCopy()}
                className="absolute top-3 right-3 inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-3 py-1.5 text-xs font-medium text-white transition-colors hover:bg-white/20"
              >
                {copied ? (
                  <Check className="size-3.5" />
                ) : (
                  <Copy className="size-3.5" />
                )}
                {i18n.mcpCopy}
              </button>
            </div>

            <p className="mt-3 rounded-lg bg-amber-50 px-3.5 py-2.5 text-xs text-amber-800">
              {i18n.mcpWizardOnce}
            </p>

            <div
              className={cn(
                "mt-5 flex items-start gap-3 rounded-xl border p-4",
                connected
                  ? "border-emerald-200 bg-emerald-50"
                  : "border-slate-200 bg-slate-50",
              )}
              aria-live="polite"
            >
              {connected ? (
                <CircleCheck className="mt-0.5 size-5 shrink-0 text-emerald-600" />
              ) : (
                <Loader2 className="mt-0.5 size-5 shrink-0 animate-spin text-slate-400" />
              )}
              <div>
                <p
                  className={cn(
                    "text-sm font-semibold",
                    connected ? "text-emerald-800" : "text-slate-800",
                  )}
                >
                  {connected
                    ? t(
                        "mcpWizardConnected",
                        connected.client
                          ? `${connected.client}${connected.client_version ? ` ${connected.client_version}` : ""}`
                          : clientName,
                      )
                    : t("mcpWizardWaiting", clientName)}
                </p>
                {!connected ? (
                  <p className="mt-1 text-sm text-slate-500">
                    {i18n.mcpWizardWaitingHint}
                  </p>
                ) : null}
              </div>
            </div>

            <div className="mt-6 flex flex-wrap items-center justify-between gap-3">
              <p className="text-xs text-slate-500">
                {connected ? null : i18n.mcpWizardCloseHint}
              </p>
              <button
                type="button"
                onClick={() => onOpenChange(false)}
                className="rounded-lg bg-[#091146] px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#091146]/80"
              >
                {i18n.mcpWizardDone}
              </button>
            </div>
          </>
        )}
      </DialogContent>
    </Dialog>
  );
};

export default ConnectWizard;
