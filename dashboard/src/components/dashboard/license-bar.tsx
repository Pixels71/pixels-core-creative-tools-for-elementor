import { useState, type FormEvent } from "react";
import { KeyRound, Loader2, RefreshCw } from "lucide-react";
import { activateLicense, checkLicense, deactivateLicense } from "@/lib/api";
import { getDashboardI18n, t } from "@/lib/i18n";
import { useToast } from "@/components/ui/toast";
import type { PixelsCoreLicense } from "@/types/wordpress";

type LicenseBarProps = {
  license: PixelsCoreLicense;
  onLicenseChange: (license: PixelsCoreLicense) => void;
};

type PendingAction = "activate" | "deactivate" | "check" | null;

const getExpiryText = (license: PixelsCoreLicense): string => {
  if (license.lifetime) {
    return t("licenseLifetime");
  }

  if (!license.expiresLabel) {
    return "";
  }

  return license.expired
    ? t("licenseExpiredOn", license.expiresLabel)
    : t("licenseExpiresOn", license.expiresLabel);
};

/**
 * Activates and deactivates the Pro license in place through Pro's /license route.
 */
const LicenseBar = ({ license, onLicenseChange }: LicenseBarProps) => {
  const i18n = getDashboardI18n();
  const toast = useToast();
  const [licenseKey, setLicenseKey] = useState("");
  const [pending, setPending] = useState<PendingAction>(null);

  const isActive = Boolean(license.active);
  const hasKey = Boolean(license.hasKey ?? license.maskedKey);
  const isBusy = pending !== null;
  const expiryText = getExpiryText(license);

  const run = async (
    action: Exclude<PendingAction, null>,
    request: () => ReturnType<typeof checkLicense>,
    fallbackMessage: string,
  ) => {
    setPending(action);

    try {
      const response = await request();
      onLicenseChange(response.license);
      toast.success(response.message ?? fallbackMessage);
      return true;
    } catch (error) {
      toast.error(
        error instanceof Error ? error.message : i18n.requestFailed,
      );
      return false;
    } finally {
      setPending(null);
    }
  };

  const handleActivate = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    const key = licenseKey.trim();

    if (!key) {
      toast.error(i18n.enterLicenseKey);
      return;
    }

    const activated = await run(
      "activate",
      () => activateLicense(key),
      i18n.licenseActivated,
    );

    if (activated) {
      setLicenseKey("");
    }
  };

  const handleDeactivate = () => {
    if (!window.confirm(i18n.confirmDeactivateLicense)) {
      return;
    }

    void run("deactivate", deactivateLicense, i18n.licenseDeactivated);
  };

  const handleCheck = () => {
    void run("check", checkLicense, i18n.settingsSaved);
  };

  const spinner = <Loader2 className="size-4 animate-spin" />;

  const checkButton = hasKey ? (
    <button
      type="button"
      onClick={handleCheck}
      disabled={isBusy}
      title={i18n.checkAgain}
      className="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-white/15 px-3 py-2 text-sm font-semibold text-slate-200 transition-colors hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-60"
    >
      {pending === "check" ? spinner : <RefreshCw className="size-4" />}
      <span className="hidden sm:inline">
        {pending === "check" ? i18n.checking : i18n.checkAgain}
      </span>
    </button>
  ) : null;

  const deactivateButton = (
    <button
      type="button"
      onClick={handleDeactivate}
      disabled={isBusy}
      className={`inline-flex shrink-0 items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-60 ${
        isActive
          ? "bg-rose-500 text-white hover:bg-rose-600"
          : "border border-white/15 text-slate-200 hover:bg-white/10"
      }`}
    >
      {pending === "deactivate" ? spinner : null}
      {pending === "deactivate" ? i18n.deactivating : i18n.deactivate}
    </button>
  );

  if (isActive) {
    return (
      <div className="mt-5 flex flex-wrap items-center gap-3 rounded-xl bg-[#091146] px-4 py-3">
        <KeyRound
          className="size-5 shrink-0 text-slate-300"
          strokeWidth={1.75}
        />
        <div className="min-w-0 flex-1">
          <span className="block truncate font-mono text-sm text-slate-200">
            {license.maskedKey || i18n.noLicenseKey}
          </span>
          {expiryText ? (
            <span className="mt-0.5 block text-xs text-slate-400">
              {expiryText}
            </span>
          ) : null}
        </div>
        {checkButton}
        {deactivateButton}
      </div>
    );
  }

  return (
    <div className="mt-5 rounded-xl bg-[#091146] px-4 py-3">
      <form
        onSubmit={handleActivate}
        className="flex flex-wrap items-center gap-3"
      >
        <KeyRound
          className="size-5 shrink-0 text-slate-300"
          strokeWidth={1.75}
        />
        <label htmlFor="pixeccte-license-key" className="sr-only">
          {i18n.licenseKeyLabel}
        </label>
        <input
          id="pixeccte-license-key"
          type="text"
          value={licenseKey}
          onChange={(event) => setLicenseKey(event.target.value)}
          placeholder={
            hasKey && license.maskedKey
              ? license.maskedKey
              : i18n.licenseKeyPlaceholder
          }
          autoComplete="off"
          spellCheck={false}
          disabled={isBusy}
          className="min-w-0 flex-1 basis-48 rounded-lg border border-white/15 bg-white/5 px-3 py-2 font-mono text-sm text-slate-100 placeholder:text-slate-400 focus:border-white/40 focus:outline-none disabled:opacity-60"
        />
        <button
          type="submit"
          disabled={isBusy}
          className="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-rose-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-rose-600 disabled:cursor-not-allowed disabled:opacity-60"
        >
          {pending === "activate" ? spinner : null}
          {pending === "activate" ? i18n.activating : i18n.activate}
        </button>
        {checkButton}
        {hasKey ? deactivateButton : null}
      </form>
      {hasKey ? (
        <p className="mt-2 text-xs text-amber-200">
          {expiryText && license.expired ? `${expiryText}. ` : ""}
          {i18n.licenseInvalidHint}
        </p>
      ) : null}
    </div>
  );
};

export default LicenseBar;
