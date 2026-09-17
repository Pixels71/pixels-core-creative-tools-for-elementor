/* eslint-disable react-refresh/only-export-components */
import type { LucideIcon } from "lucide-react";
import { FileText, Home, LayoutGrid, Puzzle, Sparkles } from "lucide-react";
import { hasFeatures } from "@/lib/api";
import { getDashboardI18n } from "@/lib/i18n";
import { cn } from "../../lib/utils";

export type DashboardTab =
  | "introduction"
  | "widgets"
  | "extension"
  | "features"
  | "form-settings";

export const DASHBOARD_TABS: DashboardTab[] = [
  "introduction",
  "widgets",
  "extension",
  "features",
  "form-settings",
];

export const normalizeDashboardTab = (
  value: string | null,
): DashboardTab | null => {
  if (value === "extensions") {
    return "extension";
  }

  if (value === "form_settings" || value === "formsettings") {
    return "form-settings";
  }

  return isDashboardTab(value) ? value : null;
};

export const isDashboardTab = (value: string | null): value is DashboardTab =>
  value !== null && DASHBOARD_TABS.includes(value as DashboardTab);

type SidebarItem = {
  id: DashboardTab;
  label: string;
  icon: LucideIcon;
};

const getSidebarItems = (): SidebarItem[] => {
  const i18n = getDashboardI18n();

  return [
    { id: "introduction", label: i18n.sidebarIntroduction, icon: Home },
    { id: "widgets", label: i18n.sidebarWidgets, icon: LayoutGrid },
    { id: "extension", label: i18n.sidebarExtension, icon: Puzzle },
    { id: "features", label: i18n.sidebarFeatures, icon: Sparkles },
    { id: "form-settings", label: i18n.sidebarFormSettings, icon: FileText },
  ];
};

type SidebarProps = {
  activeTab: DashboardTab;
  onTabChange: (tab: DashboardTab) => void;
};

const Sidebar = ({ activeTab, onTabChange }: SidebarProps) => {
  const i18n = getDashboardI18n();
  const items = getSidebarItems();
  const formAvailable =
    window.pixeccteDashboard?.formSettings != null ||
    Boolean(window.pixeccteDashboard?.proActive);

  const featuresAvailable = hasFeatures();

  const visibleItems = items.filter((item) => {
    if (item.id === "form-settings") return formAvailable;
    if (item.id === "features") return featuresAvailable;
    return true;
  });

  return (
    <aside className="sticky top-5 self-start">
      <nav
        className="flex w-full flex-col gap-1 rounded-2xl bg-white p-3 sm:w-56"
        aria-label={i18n.dashboardNavLabel}
      >
        {visibleItems.map(({ id, label, icon: Icon }) => {
          const isActive = activeTab === id;

          return (
            <button
              key={id}
              type="button"
              onClick={() => onTabChange(id)}
              aria-current={isActive ? "page" : undefined}
              className={cn(
                "flex items-center gap-3 rounded-xl px-4 py-3 text-left text-sm group font-normal transition-colors",
                isActive
                  ? "bg-[#091146] text-white"
                  : "text-slate-500 hover:bg-[#091146]/10 hover:text-[#091146]",
              )}
            >
              <Icon
                className={cn(
                  "size-4.5 shrink-0",
                  isActive
                    ? "text-white"
                    : "text-slate-400 group-hover:text-[#091146]",
                )}
                strokeWidth={1.75}
              />
              {label}
            </button>
          );
        })}
      </nav>
    </aside>
  );
};

export default Sidebar;
