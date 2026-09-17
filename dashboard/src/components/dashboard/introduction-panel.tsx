import { useEffect, useMemo, useState } from "react";
import {
  AlertTriangle,
  BookOpen,
  Check,
  FileText,
  KeyRound,
  LayoutGrid,
  Lock,
  MessageCircle,
  Puzzle,
  Star,
  Users,
} from "lucide-react";
import {
  ensureDashboardConfig,
  fetchExtensions,
  fetchWidgets,
  getBootstrappedExtensions,
  getBootstrappedWidgets,
} from "@/lib/api";
import { changelogEntries } from "@/components/shared/notifications/data";
import { categoryStyles } from "@/components/shared/notifications/types";
import { getDashboardI18n, t } from "@/lib/i18n";
import type { DashboardTab } from "./sidebar";

type IntroductionPanelProps = {
  onTabChange: (tab: DashboardTab) => void;
};

const IntroductionPanel = ({ onTabChange }: IntroductionPanelProps) => {
  const i18n = getDashboardI18n();
  const config = ensureDashboardConfig();
  const [widgets, setWidgets] = useState(getBootstrappedWidgets);
  const [extensions, setExtensions] = useState(getBootstrappedExtensions);

  useEffect(() => {
    ensureDashboardConfig();

    let cancelled = false;

    const loadStats = async () => {
      try {
        const [widgetData, extensionData] = await Promise.all([
          fetchWidgets(),
          fetchExtensions(),
        ]);

        if (!cancelled) {
          setWidgets(widgetData);
          setExtensions(extensionData);
        }
      } catch {
        // Keep bootstrapped counts if the API is unavailable.
      }
    };

    void loadStats();

    return () => {
      cancelled = true;
    };
  }, []);

  const proActive = config?.proActive ?? false;
  const showProUpsell = config?.showProUpsell ?? false;
  const showLicenseBar = proActive || showProUpsell;
  const upgradeUrl =
    config?.upgradeUrl ??
    config?.links?.pro ??
    "https://pixels71.com/pixels-core-pro/";
  const licenseActive = proActive ? (config?.license?.active ?? false) : false;
  const maskedKey = proActive
    ? (config?.license?.maskedKey ??
      (config?.license?.key
        ? `px-••••-••••-••••-${config.license.key.slice(-4)}`
        : i18n.noLicenseKey))
    : i18n.licenseProTeaser;
  const version = config?.version ?? "1.0";
  const elementorActive = config?.elementorActive ?? false;
  const nestedElementsActive = config?.nestedElementsActive ?? false;
  const formAvailable = config?.formSettings != null || proActive;
  const links = {
    tutorials: config?.links?.tutorials ?? "https://pixels71.com",
    help: config?.links?.help ?? "https://pixels71.com",
    community: config?.links?.community ?? "https://pixels71.com",
    knowledgeBase: config?.links?.knowledgeBase ?? "https://pixels71.com",
    review:
      config?.links?.review ??
      "https://wordpress.org/support/plugin/pixels-core-creative-tools-for-elementor/reviews/",
  };

  const widgetStats = useMemo(() => {
    const total = widgets.length;
    const active = widgets.filter((widget) => widget.enabled).length;
    return {
      total,
      active,
      inactive: Math.max(total - active, 0),
    };
  }, [widgets]);

  const extensionStats = useMemo(() => {
    const total = extensions.length;
    const active = extensions.filter((extension) => extension.enabled).length;
    return {
      total,
      active,
      inactive: Math.max(total - active, 0),
    };
  }, [extensions]);

  const recentUpdates = changelogEntries.slice(0, 3);

  const quickStartSteps = [
    {
      id: "widgets" as const,
      title: i18n.quickStartWidgetsTitle,
      description: i18n.quickStartWidgetsDescription,
      icon: LayoutGrid,
      iconClass: "bg-[#091146] text-white",
    },
    {
      id: "extension" as const,
      title: i18n.quickStartExtensionTitle,
      description: i18n.quickStartExtensionDescription,
      icon: Puzzle,
      iconClass: "bg-sky-50 text-sky-600",
    },
    ...(formAvailable
      ? [
          {
            id: "form-settings" as const,
            title: i18n.quickStartFormTitle,
            description: i18n.quickStartFormDescription,
            icon: FileText,
            iconClass: "bg-emerald-50 text-emerald-600",
          },
        ]
      : []),
  ];

  const quickStartDescription =
    quickStartSteps.length === 1
      ? i18n.quickStartDescriptionOne
      : quickStartSteps.length === 2
        ? i18n.quickStartDescriptionTwo
        : i18n.quickStartDescriptionThree;

  const supportCards = [
    {
      title: i18n.helpTitle,
      href: links.help,
      icon: MessageCircle,
      iconClass: "bg-[#091146] text-white",
    },
    {
      title: i18n.communityTitle,
      href: links.community,
      icon: Users,
      iconClass: "bg-sky-50 text-sky-600",
    },
    {
      title: i18n.knowledgeBaseTitle,
      href: links.knowledgeBase,
      icon: BookOpen,
      iconClass: "bg-emerald-50 text-emerald-600",
    },
  ];

  const statRows = (
    prefix: string,
    stats: { total: number; active: number; inactive: number },
  ) =>
    [
      { label: i18n.statsTotal, value: stats.total, iconClass: "bg-[#091146] text-white" },
      { label: i18n.statsActive, value: stats.active, iconClass: "bg-emerald-50 text-emerald-600" },
      { label: i18n.statsInactive, value: stats.inactive, iconClass: "bg-sky-50 text-sky-600" },
    ].map(({ label, value, iconClass }) => (
      <li key={`${prefix}-${label}`} className="flex items-center gap-3">
        <span
          className={`flex size-9 items-center justify-center rounded-lg text-xs font-bold ${iconClass}`}
        >
          {value}
        </span>
        <p className="text-sm font-semibold text-slate-800">
          {t("statsCountLabel", value, label)}
        </p>
      </li>
    ));

  return (
    <div className="grid gap-5 lg:grid-cols-[minmax(0,1fr)_240px]">
      <div className="flex flex-col gap-5">
        <section className="rounded-2xl bg-white p-5">
          <div className="flex flex-wrap items-start justify-between gap-4">
            <div className="flex gap-4">
              <div className="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500">
                <Lock className="size-5" strokeWidth={1.75} />
              </div>
              <div>
                <h2 className="text-lg font-semibold text-slate-900">
                  {i18n.welcomeTitle}
                </h2>
                <p className="mt-1 text-sm text-slate-500">
                  {i18n.welcomeDescription}
                </p>
              </div>
            </div>
            <span
              className={`inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ${
                licenseActive || !showLicenseBar
                  ? "bg-emerald-50 text-emerald-600"
                  : "bg-amber-50 text-amber-700"
              }`}
            >
              {licenseActive || !showLicenseBar ? (
                <Check className="size-3.5" strokeWidth={2.5} />
              ) : (
                <AlertTriangle className="size-3.5" strokeWidth={2.5} />
              )}
              {proActive
                ? licenseActive
                  ? i18n.licenseProActive
                  : i18n.licenseProInactive
                : i18n.licenseFree}
            </span>
          </div>

          {showLicenseBar ? (
            <div className="mt-5 flex items-center gap-3 rounded-xl bg-[#091146] px-4 py-3">
              <KeyRound
                className="size-5 shrink-0 text-[#091146]"
                strokeWidth={1.75}
              />
              <span className="flex-1 truncate font-mono text-sm text-slate-600">
                {maskedKey}
              </span>
              {proActive ? (
                <button
                  type="button"
                  className="shrink-0 rounded-lg bg-rose-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-rose-600 disabled:cursor-not-allowed disabled:opacity-60"
                  disabled={!licenseActive}
                >
                  {licenseActive ? i18n.deactivate : i18n.activate}
                </button>
              ) : (
                <a
                  href={upgradeUrl}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="shrink-0 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-slate-700"
                >
                  {i18n.getPro}
                </a>
              )}
            </div>
          ) : null}
        </section>
        <section className="overflow-hidden rounded-2xl bg-white">
          <div className="flex flex-col gap-6 p-5 md:flex-row md:items-center md:justify-between">
            <div className="max-w-md">
              <h2 className="text-xl font-semibold text-slate-900">
                {i18n.heroTitle}
              </h2>
              <p className="mt-2 text-sm leading-relaxed text-slate-500">
                {i18n.heroDescription}
              </p>
              <a
                href={links.tutorials}
                target="_blank"
                rel="noopener noreferrer"
                className="mt-5 inline-flex rounded-lg bg-[#091146] px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#091146]/80"
              >
                {i18n.watchTutorials}
              </a>
            </div>
            <div className="flex h-36 w-full items-center justify-center rounded-xl bg-linear-to-br from-violet-100 via-fuchsia-50 to-sky-100 md:h-40 md:max-w-xs">
              <span className="text-4xl font-bold tracking-tight text-[#091146]/80">
                {version}
              </span>
            </div>
          </div>
        </section>

        <section className="rounded-2xl bg-white p-5">
          <h2 className="text-lg font-semibold text-slate-900">
            {i18n.quickStartTitle}
          </h2>
          <p className="mt-1 text-sm text-slate-500">{quickStartDescription}</p>
          <ul className="mt-5 space-y-3">
            {quickStartSteps.map(
              ({ id, title, description, icon: Icon, iconClass }, index) => (
                <li key={id}>
                  <button
                    type="button"
                    onClick={() => onTabChange(id)}
                    className="flex w-full items-start gap-4 rounded-xl border border-slate-100 px-4 py-3 text-left transition-colors hover:border-violet-200 hover:bg-violet-50/50"
                  >
                    <span
                      className={`flex size-10 shrink-0 items-center justify-center rounded-xl ${iconClass}`}
                    >
                      <Icon className="size-5" strokeWidth={1.75} />
                    </span>
                    <span className="min-w-0 flex-1">
                      <span className="flex items-center gap-2">
                        <span className="text-xs font-semibold text-[#091146]">
                          {t("quickStartStep", index + 1)}
                        </span>
                      </span>
                      <span className="mt-0.5 block text-sm font-semibold text-slate-800">
                        {title}
                      </span>
                      <span className="mt-1 block text-sm text-slate-500">
                        {description}
                      </span>
                    </span>
                  </button>
                </li>
              ),
            )}
          </ul>
        </section>

        <section className="rounded-2xl bg-white p-5">
          <h2 className="text-lg font-semibold text-slate-900">
            {i18n.whatsNewTitle}
          </h2>
          <p className="mt-1 text-sm text-slate-500">
            {i18n.whatsNewDescription}
          </p>
          <ul className="mt-5 space-y-4">
            {recentUpdates.map((entry) => {
              const styles = categoryStyles[entry.category];

              return (
                <li key={entry.id} className="flex gap-3">
                  <span
                    className={`mt-1.5 size-2.5 shrink-0 rounded-full ${styles.dot}`}
                  />
                  <div className="min-w-0">
                    <p className="text-sm font-semibold text-slate-800">
                      {entry.title}
                    </p>
                    <p className="mt-1 text-xs text-slate-500">
                      {entry.date} · {entry.categoryLabel}
                    </p>
                  </div>
                </li>
              );
            })}
          </ul>
        </section>

        <div className="grid gap-4 sm:grid-cols-3">
          {supportCards.map(({ title, href, icon: Icon, iconClass }) => (
            <a
              key={title}
              href={href}
              target="_blank"
              rel="noopener noreferrer"
              className="flex flex-col items-center gap-3 rounded-2xl bg-white p-5 text-center transition-colors hover:bg-slate-50"
            >
              <span
                className={`flex size-12 items-center justify-center rounded-xl ${iconClass}`}
              >
                <Icon className="size-5" strokeWidth={1.75} />
              </span>
              <span className="text-sm font-semibold text-slate-800">
                {title}
              </span>
            </a>
          ))}
        </div>
      </div>

      <div className="flex flex-col gap-5">
        <section className="rounded-2xl bg-white p-5">
          <h3 className="text-sm font-semibold text-slate-900">
            {i18n.widgetsLabel}
          </h3>
          <ul className="mt-4 space-y-4">{statRows("widget", widgetStats)}</ul>

          <h3 className="mt-6 text-sm font-semibold text-slate-900">
            {i18n.extensionsLabel}
          </h3>
          <ul className="mt-4 space-y-4">
            {statRows("ext", extensionStats)}
          </ul>
        </section>

        <section className="rounded-2xl bg-white p-5">
          <h3 className="text-sm font-semibold text-slate-900">
            {i18n.requirementsTitle}
          </h3>
          <ul className="mt-4 space-y-3">
            <li className="flex items-start gap-3">
              <span
                className={`mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full ${
                  elementorActive
                    ? "bg-emerald-50 text-emerald-600"
                    : "bg-amber-50 text-amber-700"
                }`}
              >
                {elementorActive ? (
                  <Check className="size-3.5" strokeWidth={2.5} />
                ) : (
                  <AlertTriangle className="size-3.5" strokeWidth={2.5} />
                )}
              </span>
              <div>
                <p className="text-sm font-semibold text-slate-800">
                  {i18n.elementorTitle}
                </p>
                <p className="mt-0.5 text-xs text-slate-500">
                  {elementorActive
                    ? i18n.elementorActive
                    : i18n.elementorInactive}
                </p>
              </div>
            </li>
            <li className="flex items-start gap-3">
              <span
                className={`mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full ${
                  nestedElementsActive
                    ? "bg-emerald-50 text-emerald-600"
                    : "bg-slate-100 text-slate-500"
                }`}
              >
                {nestedElementsActive ? (
                  <Check className="size-3.5" strokeWidth={2.5} />
                ) : (
                  <AlertTriangle className="size-3.5" strokeWidth={2.5} />
                )}
              </span>
              <div>
                <p className="text-sm font-semibold text-slate-800">
                  {i18n.nestedElementsTitle}
                </p>
                <p className="mt-0.5 text-xs text-slate-500">
                  {nestedElementsActive
                    ? i18n.nestedElementsActive
                    : i18n.nestedElementsInactive}
                </p>
              </div>
            </li>
          </ul>
        </section>

        <section className="rounded-2xl bg-white p-5">
          <div className="flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
            <Star className="size-5" strokeWidth={1.75} />
          </div>
          <h3 className="mt-4 text-base font-semibold text-slate-900">
            {i18n.reviewTitle}
          </h3>
          <p className="mt-2 text-sm leading-relaxed text-slate-500">
            {i18n.reviewDescription}
          </p>
          <a
            href={links.review}
            target="_blank"
            rel="noopener noreferrer"
            className="mt-4 inline-flex text-sm font-semibold text-[#091146] hover:text-[#091146]/80"
          >
            {i18n.leaveReview}
          </a>
        </section>
      </div>
    </div>
  );
};

export default IntroductionPanel;
