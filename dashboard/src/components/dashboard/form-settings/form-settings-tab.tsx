import { useCallback, useEffect, useState } from "react";
import {
  ensureDashboardConfig,
  fetchFormSettings,
  getBootstrappedFormSettings,
  saveFormSettings,
} from "@/lib/api";
import { getDashboardI18n } from "@/lib/i18n";
import { useToast } from "@/components/ui/toast";
import FormSettingsPanel from "../widgets/form-settings-panel";
import type { FormSettings } from "../widgets/form-settings-types";

const FormSettingsTab = () => {
  const i18n = getDashboardI18n();
  const toast = useToast();
  const [settings, setSettings] = useState<FormSettings>(
    getBootstrappedFormSettings,
  );
  const [isLoading, setIsLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    ensureDashboardConfig();

    let cancelled = false;

    const loadSettings = async () => {
      setIsLoading(true);
      setError(null);

      try {
        const data = await fetchFormSettings();
        if (!cancelled) {
          setSettings(data);
        }
      } catch (loadError) {
        if (!cancelled) {
          setError(
            loadError instanceof Error
              ? loadError.message
              : i18n.failedLoadFormSettings,
          );
        }
      } finally {
        if (!cancelled) {
          setIsLoading(false);
        }
      }
    };

    void loadSettings();

    return () => {
      cancelled = true;
    };
  }, [i18n.failedLoadFormSettings]);

  const handleSave = useCallback(async () => {
    setIsSaving(true);
    setError(null);

    try {
      await saveFormSettings(settings);
      toast.success(i18n.settingsSaved);
    } catch (saveError) {
      const message =
        saveError instanceof Error
          ? saveError.message
          : i18n.failedSaveFormSettings;
      setError(message);
      toast.error(message);
    } finally {
      setIsSaving(false);
    }
  }, [settings, i18n.failedSaveFormSettings, i18n.settingsSaved, toast]);

  if (isLoading) {
    return (
      <div className="rounded-xl border border-slate-200 bg-white p-8 text-sm text-slate-500">
        {i18n.loadingFormSettings}
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {error ? (
        <div className="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          {error}
        </div>
      ) : null}

      <FormSettingsPanel
        settings={settings}
        onChange={setSettings}
      />

      <div className="flex justify-end pt-2">
        <button
          type="button"
          onClick={() => void handleSave()}
          disabled={isSaving}
          className="rounded-lg bg-[#091146] px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#091146]/80 disabled:cursor-not-allowed disabled:opacity-60"
        >
          {isSaving ? i18n.saving : i18n.saveSettings}
        </button>
      </div>
    </div>
  );
};

export default FormSettingsTab;
