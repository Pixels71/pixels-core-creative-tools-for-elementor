import { useCallback, useEffect, useMemo, useState } from "react";
import {
  ensureDashboardConfig,
  fetchFeatures,
  getBootstrappedFeatures,
  saveFeatures,
} from "@/lib/api";
import { getDashboardI18n } from "@/lib/i18n";
import { useToast } from "@/components/ui/toast";
import FeatureCard from "./feature-card";
import FeaturesToolbar, { type FeatureFilter } from "./features-toolbar";
import type { FeatureItem } from "./types";

const FeaturesPanel = () => {
  const i18n = getDashboardI18n();
  const toast = useToast();
  const [features, setFeatures] = useState<FeatureItem[]>(
    getBootstrappedFeatures,
  );
  const [search, setSearch] = useState("");
  const [filter, setFilter] = useState<FeatureFilter>("all");
  const [isLoading, setIsLoading] = useState(
    () => getBootstrappedFeatures().length === 0,
  );
  const [isSaving, setIsSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    ensureDashboardConfig();

    let cancelled = false;

    const loadFeatures = async () => {
      if (getBootstrappedFeatures().length === 0) {
        setIsLoading(true);
      }
      setError(null);

      try {
        const data = await fetchFeatures();
        if (!cancelled) {
          setFeatures(data);
        }
      } catch (loadError) {
        if (!cancelled) {
          setError(
            loadError instanceof Error
              ? loadError.message
              : i18n.failedLoadFeatures,
          );
        }
      } finally {
        if (!cancelled) {
          setIsLoading(false);
        }
      }
    };

    void loadFeatures();

    return () => {
      cancelled = true;
    };
  }, [i18n.failedLoadFeatures]);

  const allEnabled =
    features.length > 0 && features.every((feature) => feature.enabled);

  const filteredFeatures = useMemo(() => {
    const query = search.trim().toLowerCase();

    return features.filter((feature) => {
      const matchesSearch =
        query.length === 0 ||
        feature.name.toLowerCase().includes(query) ||
        feature.description?.toLowerCase().includes(query);
      const matchesFilter =
        filter === "all" ||
        (filter === "enabled" && feature.enabled) ||
        (filter === "disabled" && !feature.enabled);

      return matchesSearch && matchesFilter;
    });
  }, [features, search, filter]);

  const handleToggle = (id: string, enabled: boolean) => {
    setFeatures((prev) =>
      prev.map((feature) =>
        feature.id === id ? { ...feature, enabled } : feature,
      ),
    );
  };

  const handleToggleAll = (enabled: boolean) => {
    setFeatures((prev) => prev.map((feature) => ({ ...feature, enabled })));
  };

  const handleSave = useCallback(async () => {
    setIsSaving(true);
    setError(null);

    try {
      const saved = await saveFeatures(features);
      if (saved) {
        // The server recomputes effective/forced state after saving.
        setFeatures(saved);
      }
      toast.success(i18n.featuresSaved);
    } catch (saveError) {
      const message =
        saveError instanceof Error
          ? saveError.message
          : i18n.failedSaveFeatures;
      setError(message);
      toast.error(message);
    } finally {
      setIsSaving(false);
    }
  }, [features, i18n.failedSaveFeatures, i18n.featuresSaved, toast]);

  if (isLoading) {
    return (
      <div className="rounded-xl border border-slate-200 bg-white p-8 text-sm text-slate-500">
        {i18n.loadingFeatures}
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

      <FeaturesToolbar
        search={search}
        onSearchChange={setSearch}
        filter={filter}
        onFilterChange={setFilter}
        allEnabled={allEnabled}
        onToggleAll={handleToggleAll}
      />

      <section className="space-y-4">
        {filteredFeatures.length === 0 ? (
          <div className="rounded-xl border border-dashed border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500">
            {i18n.noFeaturesFound}
          </div>
        ) : (
          <div className="grid gap-4 sm:grid-cols-2">
            {filteredFeatures.map((feature) => (
              <FeatureCard
                key={feature.id}
                feature={feature}
                onToggle={handleToggle}
              />
            ))}
          </div>
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

export default FeaturesPanel;
