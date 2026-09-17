import { ChevronDown, Search } from "lucide-react";
import { getDashboardI18n } from "@/lib/i18n";
import { pcControlSearch, pcControlSelect } from "@/lib/form-control";
import ToggleSwitch from "./toggle-switch";

type WidgetsToolbarProps = {
  search: string;
  onSearchChange: (value: string) => void;
  filter: "all" | "enabled" | "disabled";
  onFilterChange: (value: "all" | "enabled" | "disabled") => void;
  allEnabled: boolean;
  onToggleAll: (enabled: boolean) => void;
};

const WidgetsToolbar = ({
  search,
  onSearchChange,
  filter,
  onFilterChange,
  allEnabled,
  onToggleAll,
}: WidgetsToolbarProps) => {
  const i18n = getDashboardI18n();

  return (
    <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
      <h1 className="text-2xl font-semibold text-slate-900">
        {i18n.tabWidgetsTitle}
      </h1>

      <div className="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
        <label className="relative block min-w-[200px] flex-1 sm:max-w-xs">
          <span className="sr-only">{i18n.searchByName}</span>
          <Search
            className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400"
            strokeWidth={1.75}
          />
          <input
            type="search"
            value={search}
            onChange={(event) => onSearchChange(event.target.value)}
            placeholder={i18n.searchByName}
            className={pcControlSearch}
          />
        </label>

        <div className="relative">
          <select
            value={filter}
            onChange={(event) =>
              onFilterChange(
                event.target.value as "all" | "enabled" | "disabled",
              )
            }
            className={pcControlSelect}
          >
            <option value="all">{i18n.allWidgets}</option>
            <option value="enabled">{i18n.enabled}</option>
            <option value="disabled">{i18n.disabled}</option>
          </select>
          <ChevronDown
            className="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-slate-400"
            strokeWidth={1.75}
          />
        </div>

        <ToggleSwitch
          checked={allEnabled}
          onChange={onToggleAll}
          label={i18n.enableAllWidgets}
          caption={i18n.enableAllWidgetsCaption}
          size="sm"
        />
      </div>
    </div>
  );
};

export default WidgetsToolbar;
