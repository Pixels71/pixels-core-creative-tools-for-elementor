import type { DashboardTab } from "@/components/dashboard/sidebar";
import { getDashboardI18n } from "@/lib/i18n";
import { Search } from "lucide-react";
import SearchModal from "./search/search-modal";
import logo from "/images/main-logo.svg";

type HeaderProps = {
  onTabNavigate?: (tab: DashboardTab) => void;
};

const Header = ({ onTabNavigate }: HeaderProps) => {
  const i18n = getDashboardI18n();

  return (
    <header className="bg-white py-5">
      <nav className="main-container">
        <div className="flex items-center justify-between gap-x-5">
          <a
            href={window.pixeccteDashboard?.adminUrl ?? "/"}
            className="inline-flex items-center"
          >
            <img
              src={logo}
              alt={i18n.logoAlt}
              className="h-9 w-auto"
              width={174}
              height={44}
            />
          </a>

          <div className="flex items-center gap-2.5">
            <SearchModal
              onNavigate={onTabNavigate}
              trigger={
                <button
                  type="button"
                  className="flex size-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 transition-colors hover:bg-gray-50"
                  aria-label={i18n.search}
                >
                  <Search className="size-5" strokeWidth={1.75} />
                </button>
              }
            />
          </div>
        </div>
      </nav>
    </header>
  );
};

export default Header;
