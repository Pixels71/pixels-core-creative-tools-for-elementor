import { LayoutGrid, Puzzle, Sparkles } from "lucide-react";
import { cn } from "@/lib/utils";
import type { SearchResult } from "./types";

type SearchResultItemProps = {
  result: SearchResult;
  onSelect: (result: SearchResult) => void;
};

const SearchResultItem = ({ result, onSelect }: SearchResultItemProps) => {
  const Icon =
    result.type === "widget"
      ? LayoutGrid
      : result.type === "feature"
        ? Sparkles
        : Puzzle;

  return (
    <button
      type="button"
      onClick={() => onSelect(result)}
      className="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left transition-colors hover:bg-slate-50"
    >
      <span
        className={cn(
          "flex size-9 shrink-0 items-center justify-center rounded-lg",
          result.type === "widget"
            ? "bg-[#091146] text-white"
            : result.type === "feature"
              ? "bg-amber-50 text-amber-600"
              : "bg-sky-50 text-sky-600",
        )}
      >
        <Icon className="size-4" strokeWidth={1.75} />
      </span>

      <span className="min-w-0 flex-1">
        <span className="block truncate text-sm font-medium text-slate-900">
          {result.name}
        </span>
        <span className="mt-0.5 block text-xs capitalize text-slate-400">
          {result.category}
        </span>
      </span>

      <span
        className={cn(
          "shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide",
          result.enabled
            ? "bg-emerald-50 text-emerald-600"
            : "bg-slate-100 text-slate-500",
        )}
      >
        {result.enabled ? "Active" : "Inactive"}
      </span>
    </button>
  );
};

export default SearchResultItem;
