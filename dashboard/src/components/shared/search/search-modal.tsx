import type { DashboardTab } from "@/components/dashboard/sidebar";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import {
  ensureDashboardConfig,
  fetchDashboardSearchIndex,
  getBootstrappedExtensions,
  getBootstrappedFeatures,
  getBootstrappedWidgets,
} from "@/lib/api";
import { pcControlSearchModal } from "@/lib/form-control";
import { getDashboardI18n } from "@/lib/i18n";
import { Search } from "lucide-react";
import { useEffect, useMemo, useState, type ReactNode } from "react";
import SearchResultItem from "./search-result-item";
import type { SearchResult } from "./types";

type SearchModalProps = {
  trigger: ReactNode;
  onNavigate?: (tab: DashboardTab) => void;
};

const buildBootstrappedSearchIndex = (): SearchResult[] => {
  ensureDashboardConfig();

  return [
    ...getBootstrappedWidgets().map((widget) => ({
      id: widget.id,
      name: widget.name,
      category: widget.category,
      type: "widget" as const,
      enabled: widget.enabled,
      tab: "widgets" as const,
    })),
    ...getBootstrappedExtensions().map((extension) => ({
      id: extension.id,
      name: extension.name,
      category: extension.category,
      type: "extension" as const,
      enabled: extension.enabled,
      tab: "extension" as const,
    })),
    ...getBootstrappedFeatures().map((feature) => ({
      id: feature.id,
      name: feature.name,
      category: feature.category,
      type: "feature" as const,
      enabled: feature.enabled,
      tab: "features" as const,
    })),
  ];
};

const SearchModal = ({ trigger, onNavigate }: SearchModalProps) => {
  const i18n = getDashboardI18n();
  const [open, setOpen] = useState(false);
  const [query, setQuery] = useState("");
  const [index, setIndex] = useState<SearchResult[]>(buildBootstrappedSearchIndex);
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    const handleKeyDown = (event: KeyboardEvent) => {
      if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === "k") {
        event.preventDefault();
        setOpen(true);
      }
    };

    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, []);

  useEffect(() => {
    if (!open) {
      return;
    }

    let cancelled = false;

    const loadIndex = async () => {
      setIsLoading(true);

      try {
        const data = await fetchDashboardSearchIndex();
        if (!cancelled) {
          setIndex(data);
        }
      } catch {
        if (!cancelled) {
          setIndex(buildBootstrappedSearchIndex());
        }
      } finally {
        if (!cancelled) {
          setIsLoading(false);
        }
      }
    };

    void loadIndex();

    return () => {
      cancelled = true;
    };
  }, [open]);

  const results = useMemo(() => {
    const normalizedQuery = query.trim().toLowerCase();
    if (!normalizedQuery) return index;

    return index.filter(
      (item) =>
        item.name.toLowerCase().includes(normalizedQuery) ||
        item.category.toLowerCase().includes(normalizedQuery),
    );
  }, [index, query]);

  const widgetResults = results.filter((item) => item.type === "widget");
  const extensionResults = results.filter((item) => item.type === "extension");
  const featureResults = results.filter((item) => item.type === "feature");

  const handleSelect = (result: SearchResult) => {
    onNavigate?.(result.tab);
    setOpen(false);
    setQuery("");
  };

  const handleOpenChange = (nextOpen: boolean) => {
    setOpen(nextOpen);
    if (!nextOpen) setQuery("");
  };

  return (
    <Dialog open={open} onOpenChange={handleOpenChange}>
      <DialogTrigger asChild>{trigger}</DialogTrigger>

      <DialogContent
        showCloseButton={false}
        className="fixed top-[12%] left-1/2 z-50 w-[min(100vw-2rem,560px)] -translate-x-1/2 overflow-hidden rounded-xl border border-slate-200 bg-white p-0 shadow-xl outline-none"
      >
        <DialogHeader className="border-b border-slate-100 px-9 py-6">
          <DialogTitle className="sr-only">
            {i18n.searchModalTitle}
          </DialogTitle>
          <DialogDescription className="sr-only">
            {i18n.searchModalDescription}
          </DialogDescription>

          <label className="relative block">
            <Search
              className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400"
              strokeWidth={1.75}
            />
            <input
              type="search"
              value={query}
              onChange={(event) => setQuery(event.target.value)}
              placeholder={i18n.searchWidgetsExtensions}
              autoFocus
              className={pcControlSearchModal}
            />
            <kbd className="pointer-events-none absolute top-1/2 right-3 hidden -translate-y-1/2 rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] font-medium text-slate-400 sm:inline-block">
              {i18n.keyboardShortcut}
            </kbd>
          </label>
        </DialogHeader>

        <div className="max-h-[min(60vh,420px)] overflow-y-auto p-7">
          {isLoading ? (
            <p className="px-3 py-10 text-center text-sm text-slate-400">
              {i18n.loadingSearchIndex}
            </p>
          ) : results.length === 0 ? (
            <p className="px-3 py-10 text-center text-sm text-slate-400">
              {i18n.noSearchResults}
            </p>
          ) : (
            <>
              {widgetResults.length > 0 ? (
                <section className="mb-2">
                  <h3 className="px-3 py-2 text-xs font-semibold tracking-wide text-slate-400 uppercase">
                    {i18n.widgetsLabel}
                  </h3>
                  <ul>
                    {widgetResults.map((result) => (
                      <li key={result.id}>
                        <SearchResultItem
                          result={result}
                          onSelect={handleSelect}
                        />
                      </li>
                    ))}
                  </ul>
                </section>
              ) : null}

              {extensionResults.length > 0 ? (
                <section className="mb-2">
                  <h3 className="px-3 py-2 text-xs font-semibold tracking-wide text-slate-400 uppercase">
                    {i18n.extensionsLabel}
                  </h3>
                  <ul>
                    {extensionResults.map((result) => (
                      <li key={result.id}>
                        <SearchResultItem
                          result={result}
                          onSelect={handleSelect}
                        />
                      </li>
                    ))}
                  </ul>
                </section>
              ) : null}

              {featureResults.length > 0 ? (
                <section>
                  <h3 className="px-3 py-2 text-xs font-semibold tracking-wide text-slate-400 uppercase">
                    {i18n.featuresLabel}
                  </h3>
                  <ul>
                    {featureResults.map((result) => (
                      <li key={result.id}>
                        <SearchResultItem
                          result={result}
                          onSelect={handleSelect}
                        />
                      </li>
                    ))}
                  </ul>
                </section>
              ) : null}
            </>
          )}
        </div>
      </DialogContent>
    </Dialog>
  );
};

export default SearchModal;
