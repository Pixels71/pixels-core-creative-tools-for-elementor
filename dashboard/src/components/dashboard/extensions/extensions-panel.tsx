import { useCallback, useEffect, useMemo, useState } from "react";
import {
  ensureDashboardConfig,
  fetchExtensions,
  getBootstrappedExtensions,
  saveExtensions,
} from "@/lib/api";
import { getDashboardI18n } from "@/lib/i18n";
import { useToast } from "@/components/ui/toast";
import ToggleSwitch from "../widgets/toggle-switch";
import ExtensionGrid from "./extension-grid";
import ExtensionsToolbar from "./extensions-toolbar";
import type { ExtensionItem } from "./types";

const ExtensionsPanel = () => {
  const i18n = getDashboardI18n();
  const toast = useToast();
  const [extensions, setExtensions] = useState<ExtensionItem[]>(
    getBootstrappedExtensions,
  );
  const [search, setSearch] = useState("");
  const [filter, setFilter] = useState<"all" | "enabled" | "disabled">("all");
  const [isLoading, setIsLoading] = useState(
    () => getBootstrappedExtensions().length === 0,
  );
  const [isSaving, setIsSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    ensureDashboardConfig();

    let cancelled = false;

    const loadExtensions = async () => {
      if (getBootstrappedExtensions().length === 0) {
        setIsLoading(true);
      }
      setError(null);

      try {
        const data = await fetchExtensions();
        if (!cancelled) {
          setExtensions(data);
        }
      } catch (loadError) {
        if (!cancelled) {
          setError(
            loadError instanceof Error
              ? loadError.message
              : i18n.failedLoadExtensions,
          );
        }
      } finally {
        if (!cancelled) {
          setIsLoading(false);
        }
      }
    };

    void loadExtensions();

    return () => {
      cancelled = true;
    };
  }, [i18n.failedLoadExtensions]);

  const allEnabled =
    extensions.filter((extension) => extension.available !== false).length >
      0 &&
    extensions
      .filter((extension) => extension.available !== false)
      .every((extension) => extension.enabled);

  const filteredExtensions = useMemo(() => {
    const query = search.trim().toLowerCase();

    return extensions
      .filter((extension) => {
        const matchesSearch =
          query.length === 0 ||
          extension.name.toLowerCase().includes(query) ||
          extension.description?.toLowerCase().includes(query);
        const matchesFilter =
          filter === "all" ||
          (filter === "enabled" && extension.enabled) ||
          (filter === "disabled" && !extension.enabled);

        return matchesSearch && matchesFilter;
      })
      .sort((a, b) => a.name.localeCompare(b.name));
  }, [extensions, search, filter]);

  const visibleAllEnabled =
    filteredExtensions.filter((extension) => extension.available !== false)
      .length > 0 &&
    filteredExtensions
      .filter((extension) => extension.available !== false)
      .every((extension) => extension.enabled);

  const handleToggle = (id: string, enabled: boolean) => {
    setExtensions((prev) =>
      prev.map((extension) =>
        extension.id === id && extension.available !== false
          ? { ...extension, enabled }
          : extension,
      ),
    );
  };

  const handleToggleAll = (enabled: boolean) => {
    setExtensions((prev) =>
      prev.map((extension) =>
        extension.available === false ? extension : { ...extension, enabled },
      ),
    );
  };

  const handleToggleVisible = (enabled: boolean) => {
    const visibleIds = new Set(
      filteredExtensions.map((extension) => extension.id),
    );
    setExtensions((prev) =>
      prev.map((extension) =>
        visibleIds.has(extension.id) && extension.available !== false
          ? { ...extension, enabled }
          : extension,
      ),
    );
  };

  const handleSave = useCallback(async () => {
    setIsSaving(true);
    setError(null);

    try {
      await saveExtensions(extensions);
      toast.success(i18n.settingsSaved);
    } catch (saveError) {
      const message =
        saveError instanceof Error
          ? saveError.message
          : i18n.failedSaveExtensions;
      setError(message);
      toast.error(message);
    } finally {
      setIsSaving(false);
    }
  }, [extensions, i18n.failedSaveExtensions, i18n.settingsSaved, toast]);

  if (isLoading) {
    return (
      <div className="rounded-xl border border-slate-200 bg-white p-8 text-sm text-slate-500">
        {i18n.loadingExtensions}
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

      <ExtensionsToolbar
        search={search}
        onSearchChange={setSearch}
        filter={filter}
        onFilterChange={setFilter}
        allEnabled={allEnabled}
        onToggleAll={handleToggleAll}
      />

      <section className="space-y-4">
        <div className="flex items-center justify-between gap-4">
          <h2 className="text-base font-semibold text-slate-800">
            {i18n.allExtensions}
          </h2>
          <ToggleSwitch
            checked={visibleAllEnabled}
            onChange={handleToggleVisible}
            label={i18n.enableAllExtensions}
            caption={i18n.enableAll}
            size="sm"
          />
        </div>

        {filteredExtensions.length === 0 ? (
          <div className="rounded-xl border border-dashed border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500">
            {i18n.noExtensionsFound}
          </div>
        ) : (
          <ExtensionGrid
            extensions={filteredExtensions}
            onToggle={handleToggle}
          />
        )}
      </section>

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

export default ExtensionsPanel;
