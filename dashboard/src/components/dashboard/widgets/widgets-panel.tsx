import { useCallback, useEffect, useMemo, useState } from "react";
import {
  ensureDashboardConfig,
  fetchWidgets,
  getBootstrappedWidgets,
  saveWidgets,
} from "@/lib/api";
import { getDashboardI18n } from "@/lib/i18n";
import { useToast } from "@/components/ui/toast";
import ToggleSwitch from "./toggle-switch";
import WidgetGrid from "./widget-grid";
import WidgetsToolbar from "./widgets-toolbar";
import type { WidgetItem } from "./types";

const WidgetsPanel = () => {
  const i18n = getDashboardI18n();
  const toast = useToast();
  const [widgets, setWidgets] = useState<WidgetItem[]>(getBootstrappedWidgets);
  const [search, setSearch] = useState("");
  const [filter, setFilter] = useState<"all" | "enabled" | "disabled">("all");
  const [isLoading, setIsLoading] = useState(
    () => getBootstrappedWidgets().length === 0,
  );
  const [isSaving, setIsSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    ensureDashboardConfig();

    let cancelled = false;

    const loadData = async () => {
      if (getBootstrappedWidgets().length === 0) {
        setIsLoading(true);
      }
      setError(null);

      try {
        const widgetData = await fetchWidgets();

        if (!cancelled) {
          setWidgets(widgetData);
        }
      } catch (loadError) {
        if (!cancelled) {
          setError(
            loadError instanceof Error
              ? loadError.message
              : i18n.failedLoadWidgets,
          );
        }
      } finally {
        if (!cancelled) {
          setIsLoading(false);
        }
      }
    };

    void loadData();

    return () => {
      cancelled = true;
    };
  }, [i18n.failedLoadWidgets]);

  const allEnabled =
    widgets.filter((widget) => widget.available !== false).length > 0 &&
    widgets
      .filter((widget) => widget.available !== false)
      .every((widget) => widget.enabled);

  const filteredWidgets = useMemo(() => {
    const query = search.trim().toLowerCase();

    return widgets
      .filter((widget) => {
        const matchesSearch =
          query.length === 0 ||
          widget.name.toLowerCase().includes(query) ||
          widget.description?.toLowerCase().includes(query);
        const matchesFilter =
          filter === "all" ||
          (filter === "enabled" && widget.enabled) ||
          (filter === "disabled" && !widget.enabled);

        return matchesSearch && matchesFilter;
      })
      .sort((a, b) => a.name.localeCompare(b.name));
  }, [widgets, search, filter]);

  const visibleAllEnabled =
    filteredWidgets.filter((widget) => widget.available !== false).length > 0 &&
    filteredWidgets
      .filter((widget) => widget.available !== false)
      .every((widget) => widget.enabled);

  const handleToggle = (id: string, enabled: boolean) => {
    setWidgets((prev) =>
      prev.map((widget) =>
        widget.id === id && widget.available !== false
          ? { ...widget, enabled }
          : widget,
      ),
    );
  };

  const handleToggleAll = (enabled: boolean) => {
    setWidgets((prev) =>
      prev.map((widget) =>
        widget.available === false ? widget : { ...widget, enabled },
      ),
    );
  };

  const handleToggleVisible = (enabled: boolean) => {
    const visibleIds = new Set(filteredWidgets.map((widget) => widget.id));
    setWidgets((prev) =>
      prev.map((widget) =>
        visibleIds.has(widget.id) && widget.available !== false
          ? { ...widget, enabled }
          : widget,
      ),
    );
  };

  const handleSave = useCallback(async () => {
    setIsSaving(true);
    setError(null);

    try {
      await saveWidgets(widgets);
      toast.success(i18n.settingsSaved);
    } catch (saveError) {
      const message =
        saveError instanceof Error
          ? saveError.message
          : i18n.failedSaveWidgets;
      setError(message);
      toast.error(message);
    } finally {
      setIsSaving(false);
    }
  }, [widgets, i18n.failedSaveWidgets, i18n.settingsSaved, toast]);

  if (isLoading) {
    return (
      <div className="rounded-xl border border-slate-200 bg-white p-8 text-sm text-slate-500">
        {i18n.loadingWidgets}
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

      <WidgetsToolbar
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
            {i18n.allElements}
          </h2>
          <ToggleSwitch
            checked={visibleAllEnabled}
            onChange={handleToggleVisible}
            label={i18n.enableAllElements}
            caption={i18n.enableAll}
            size="sm"
          />
        </div>

        {filteredWidgets.length === 0 ? (
          <div className="rounded-xl border border-dashed border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500">
            {i18n.noWidgetsFound}
          </div>
        ) : (
          <WidgetGrid widgets={filteredWidgets} onToggle={handleToggle} />
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

export default WidgetsPanel;
