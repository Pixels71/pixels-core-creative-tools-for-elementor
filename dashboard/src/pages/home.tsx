import { Fragment, useMemo, useState } from "react";
import ExtensionsPanel from "../components/dashboard/extensions/extensions-panel";
import FeaturesPanel from "../components/dashboard/features/features-panel";
import FormSettingsTab from "../components/dashboard/form-settings/form-settings-tab";
import IntroductionPanel from "../components/dashboard/introduction-panel";
import Sidebar, {
  normalizeDashboardTab,
  type DashboardTab,
} from "../components/dashboard/sidebar";
import WidgetsPanel from "../components/dashboard/widgets/widgets-panel";
import Header from "../components/shared/header";
import { getDashboardI18n } from "@/lib/i18n";
import type { DashboardNotice } from "../types/wordpress";

const getTabTitles = () => {
  const i18n = getDashboardI18n();

  return {
    introduction: {
      title: i18n.tabIntroductionTitle,
      description: i18n.tabIntroductionDescription,
    },
    widgets: {
      title: i18n.tabWidgetsTitle,
      description: i18n.tabWidgetsDescription,
    },
    extension: {
      title: i18n.tabExtensionTitle,
      description: i18n.tabExtensionDescription,
    },
    features: {
      title: i18n.tabFeaturesTitle,
      description: i18n.tabFeaturesDescription,
    },
    "form-settings": {
      title: i18n.tabFormSettingsTitle,
      description: i18n.tabFormSettingsDescription,
    },
  } satisfies Record<DashboardTab, { title: string; description: string }>;
};

const noticeStyles: Record<DashboardNotice["type"], string> = {
  success: "border-emerald-200 bg-emerald-50 text-emerald-700",
  warning: "border-amber-200 bg-amber-50 text-amber-800",
  error: "border-red-200 bg-red-50 text-red-700",
  info: "border-sky-200 bg-sky-50 text-sky-700",
};

const getTabFromUrl = (): DashboardTab => {
  const tab = new URLSearchParams(window.location.search).get("tab");
  return normalizeDashboardTab(tab) ?? "introduction";
};

const tabToUrlValue = (tab: DashboardTab): string | null => {
  if (tab === "introduction") {
    return null;
  }

  if (tab === "extension") {
    return "extensions";
  }

  return tab;
};

const Home = () => {
  const [activeTab, setActiveTab] = useState<DashboardTab>(getTabFromUrl);
  const tabTitles = useMemo(() => getTabTitles(), []);
  const { title, description } = tabTitles[activeTab];
  const notices = window.pixeccteDashboard?.notices ?? [];

  function handleTabChange(tab: DashboardTab) {
    setActiveTab(tab);

    const url = new URL(window.location.href);
    const urlValue = tabToUrlValue(tab);

    if (urlValue === null) {
      url.searchParams.delete("tab");
    } else {
      url.searchParams.set("tab", urlValue);
    }

    window.history.replaceState({}, "", url);
  }

  return (
    <Fragment>
      <Header onTabNavigate={handleTabChange} />
      <main className="main-container py-8">
        {notices.length > 0 ? (
          <div className="mb-6 space-y-3">
            {notices.map((notice) => (
              <div
                key={notice.message}
                className={`rounded-lg border px-4 py-3 text-sm ${noticeStyles[notice.type]}`}
              >
                {notice.message}
              </div>
            ))}
          </div>
        ) : null}

        <div className="flex flex-col gap-6 sm:flex-row sm:items-start">
          <Sidebar activeTab={activeTab} onTabChange={handleTabChange} />

          <div className="min-w-0 flex-1">
            {activeTab === "widgets" ? (
              <WidgetsPanel />
            ) : activeTab === "extension" ? (
              <ExtensionsPanel />
            ) : activeTab === "features" ? (
              <FeaturesPanel />
            ) : activeTab === "form-settings" ? (
              <>
                <div className="mb-5">
                  <h1 className="text-2xl font-semibold text-slate-900">
                    {title}
                  </h1>
                  <p className="mt-1 text-sm text-slate-500">{description}</p>
                </div>
                <FormSettingsTab />
              </>
            ) : (
              <>
                <div className="mb-5">
                  <h1 className="text-2xl font-semibold text-slate-900">
                    {title}
                  </h1>
                  <p className="mt-1 text-sm text-slate-500">{description}</p>
                </div>

                {activeTab === "introduction" && (
                  <IntroductionPanel onTabChange={handleTabChange} />
                )}
              </>
            )}
          </div>
        </div>
      </main>
    </Fragment>
  );
};

export default Home;
