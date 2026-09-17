import type { ExtensionItem } from "@/components/dashboard/extensions/types";
import type { FeatureItem } from "@/components/dashboard/features/types";
import type {
  FormSettings,
} from "@/components/dashboard/widgets/form-settings-types";
import { emptyFormSettings } from "@/components/dashboard/widgets/form-settings-types";
import type { WidgetItem } from "@/components/dashboard/widgets/types";
import type { PixelsCoreDashboardConfig } from "@/types/wordpress";
import { t } from "@/lib/i18n";

type ApiWidget = {
  id: string;
  name: string;
  description?: string;
  category: WidgetItem["category"];
  enabled: boolean;
  available?: boolean;
  tier?: "free" | "pro";
  isPro?: boolean;
  upgradeUrl?: string;
};

type ApiExtension = {
  id: string;
  name: string;
  description?: string;
  category: ExtensionItem["category"];
  enabled: boolean;
  available?: boolean;
  tier?: "free" | "pro";
  isPro?: boolean;
  upgradeUrl?: string;
};

type ApiFeature = {
  id: string;
  name: string;
  description?: string;
  impact?: string;
  icon?: string;
  category?: string;
  enabled: boolean;
  available?: boolean;
  effective?: boolean;
  forced?: boolean;
  tier?: "free" | "pro";
  isPro?: boolean;
};

const stripPixelsSuffix = (name: string) =>
  name.replace(/\s*\(Pixels\)\s*$/i, "").trim();

const mapWidget = (widget: ApiWidget): WidgetItem => ({
  id: widget.id,
  name: stripPixelsSuffix(widget.name),
  category: widget.category,
  enabled: Boolean(widget.enabled),
  description: widget.description,
  available: widget.available !== false,
  tier: widget.tier ?? (widget.isPro ? "pro" : "free"),
  isPro: Boolean(widget.isPro),
  upgradeUrl: widget.upgradeUrl,
});

const mapExtension = (extension: ApiExtension): ExtensionItem => ({
  id: extension.id,
  name: extension.name,
  category: extension.category,
  enabled: Boolean(extension.enabled),
  description: extension.description,
  available: extension.available !== false,
  tier: extension.tier ?? (extension.isPro ? "pro" : "free"),
  isPro: Boolean(extension.isPro),
  upgradeUrl: extension.upgradeUrl,
});

const mapFeature = (feature: ApiFeature): FeatureItem => ({
  id: feature.id,
  name: feature.name,
  category: feature.category ?? "feature",
  enabled: Boolean(feature.enabled),
  description: feature.description,
  impact: feature.impact,
  icon: feature.icon,
  available: feature.available !== false,
  effective: feature.effective ?? Boolean(feature.enabled),
  forced: Boolean(feature.forced),
  tier: feature.tier ?? "pro",
  isPro: feature.isPro ?? true,
});

export const ensureDashboardConfig = (): PixelsCoreDashboardConfig | null => {
  if (window.pixeccteDashboard) {
    return window.pixeccteDashboard;
  }

  // Vite/`yarn dev`: talk to Local WP through the Vite proxy.
  if (import.meta.env.DEV) {
    const restUrl = (
      import.meta.env.VITE_WP_REST_URL ?? "/wp-json/pixeccte/v1"
    ).replace(/\/$/, "");

    window.pixeccteDashboard = {
      restUrl,
      nonce: "",
      adminUrl: "/",
      pluginUrl: "/",
      elementorActive: true,
      nestedElementsActive: true,
      notices: [],
      version: "1.0",
      license: {
        active: true,
        maskedKey: "px-••••-••••-••••-core",
      },
      links: {
        tutorials: "https://pixels71.com",
        help: "https://pixels71.com",
        community: "https://pixels71.com",
        knowledgeBase: "https://pixels71.com",
        review:
          "https://wordpress.org/support/plugin/pixels-core-creative-tools-for-elementor/reviews/",
      },
      widgets: [],
      extensions: [],
      formSettings: emptyFormSettings,
    };

    return window.pixeccteDashboard;
  }

  return null;
};

export const isWordPressDashboard = () => Boolean(ensureDashboardConfig());

export const getBootstrappedWidgets = (): WidgetItem[] => {
  const widgets = ensureDashboardConfig()?.widgets;
  return Array.isArray(widgets) ? widgets.map(mapWidget) : [];
};

export const getBootstrappedExtensions = (): ExtensionItem[] => {
  const extensions = ensureDashboardConfig()?.extensions;
  return Array.isArray(extensions) ? extensions.map(mapExtension) : [];
};

export const getBootstrappedFeatures = (): FeatureItem[] => {
  const modules = ensureDashboardConfig()?.modules;
  return Array.isArray(modules) ? modules.map(mapFeature) : [];
};

/** Features (Pro modules) exist only when Pro bootstraps them into the config. */
export const hasFeatures = (): boolean => getBootstrappedFeatures().length > 0;

export const getBootstrappedFormSettings = (): FormSettings => {
  return ensureDashboardConfig()?.formSettings ?? emptyFormSettings;
};

const getConfig = () => {
  const config = ensureDashboardConfig();
  if (!config) {
    throw new Error(t("configUnavailable"));
  }

  return config;
};

const apiFetch = async <T>(
  endpoint: string,
  options: RequestInit = {},
): Promise<T> => {
  const { restUrl, nonce } = getConfig();
  const headers: Record<string, string> = {
    "Content-Type": "application/json",
    ...(options.headers as Record<string, string> | undefined),
  };

  if (nonce) {
    headers["X-WP-Nonce"] = nonce;
  }

  const response = await fetch(`${restUrl}${endpoint}`, {
    ...options,
    headers,
  });

  if (!response.ok) {
    const error = await response.json().catch(() => ({}));
    const message =
      typeof error?.message === "string"
        ? error.message
        : t("requestFailed");
    throw new Error(message);
  }

  return response.json() as Promise<T>;
};

export const fetchWidgets = async (): Promise<WidgetItem[]> => {
  const widgets = await apiFetch<ApiWidget[]>("/widgets");
  return widgets.map(mapWidget);
};

export const saveWidgets = async (
  widgets: WidgetItem[],
  formSettings?: FormSettings,
): Promise<void> => {
  const active = widgets
    .filter((widget) => widget.enabled && widget.available !== false)
    .map((w) => w.id);

  await apiFetch("/widgets", {
    method: "POST",
    body: JSON.stringify({
      active,
      ...(formSettings ? { formSettings } : {}),
    }),
  });
};

export const fetchExtensions = async (): Promise<ExtensionItem[]> => {
  const extensions = await apiFetch<ApiExtension[]>("/extensions");
  return extensions.map(mapExtension);
};

export const saveExtensions = async (
  extensions: ExtensionItem[],
): Promise<void> => {
  const active = extensions
    .filter((extension) => extension.enabled && extension.available !== false)
    .map((e) => e.id);

  await apiFetch("/extensions", {
    method: "POST",
    body: JSON.stringify({ active }),
  });
};

export const fetchFeatures = async (): Promise<FeatureItem[]> => {
  const modules = await apiFetch<ApiFeature[]>("/modules");
  return modules.map(mapFeature);
};

export const saveFeatures = async (
  features: FeatureItem[],
): Promise<FeatureItem[] | null> => {
  const active = features
    .filter((feature) => feature.enabled && feature.available !== false)
    .map((feature) => feature.id);

  const response = await apiFetch<{ modules?: ApiFeature[] }>("/modules", {
    method: "POST",
    body: JSON.stringify({ active }),
  });

  return Array.isArray(response?.modules)
    ? response.modules.map(mapFeature)
    : null;
};

export const fetchFormSettings = async (): Promise<FormSettings> => {
  return apiFetch<FormSettings>("/form-settings");
};

export const saveFormSettings = async (
  settings: FormSettings,
): Promise<void> => {
  await apiFetch("/form-settings", {
    method: "POST",
    body: JSON.stringify(settings),
  });
};

export const fetchDashboardSearchIndex = async () => {
  const [widgets, extensions, features] = await Promise.all([
    fetchWidgets(),
    fetchExtensions(),
    hasFeatures() ? fetchFeatures().catch(() => []) : Promise.resolve([]),
  ]);

  return [
    ...widgets.map((widget) => ({
      id: widget.id,
      name: widget.name,
      category: widget.category,
      type: "widget" as const,
      enabled: widget.enabled,
      tab: "widgets" as const,
    })),
    ...extensions.map((extension) => ({
      id: extension.id,
      name: extension.name,
      category: extension.category,
      type: "extension" as const,
      enabled: extension.enabled,
      tab: "extension" as const,
    })),
    ...features.map((feature) => ({
      id: feature.id,
      name: feature.name,
      category: feature.category,
      type: "feature" as const,
      enabled: feature.enabled,
      tab: "features" as const,
    })),
  ];
};
