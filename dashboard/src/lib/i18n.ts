import type { DashboardI18n } from "@/types/wordpress";

const readConfig = () => window.pixeccteDashboard ?? null;

const defaultI18n: DashboardI18n = {
  sidebarIntroduction: "Introduction",
  sidebarWidgets: "Widgets",
  sidebarExtension: "Extension",
  sidebarFormSettings: "Form Settings",
  sidebarFeatures: "Features",
  dashboardNavLabel: "Dashboard",

  tabIntroductionTitle: "Introduction",
  tabIntroductionDescription:
    "Get started with Pixels Core and explore what is available.",
  tabWidgetsTitle: "Widgets",
  tabWidgetsDescription:
    "Browse and manage the widgets available in your dashboard.",
  tabExtensionTitle: "Extension",
  tabExtensionDescription: "Enable extensions to unlock extra capabilities.",
  tabFormSettingsTitle: "Form Settings",
  tabFormSettingsDescription:
    "Configure reCAPTCHA and Mailchimp settings used by the Form widget.",
  tabFeaturesTitle: "Features",
  tabFeaturesDescription:
    "Turn off the Pro features you are not using so they stop loading entirely. Nothing is deleted — switching a feature back on restores it.",

  search: "Search",
  notifications: "Notifications",
  searchByName: "Search by name",
  searchWidgetsExtensions: "Search widgets and extensions...",
  searchModalTitle: "Search widgets and extensions",
  searchModalDescription:
    "Search across widgets and extensions in Pixels Core.",
  loadingSearchIndex: "Loading search index...",
  noSearchResults: "No widgets or extensions match your search.",
  keyboardShortcut: "Ctrl K",
  dismissNotification: "Dismiss notification",
  toastSuccessTitle: "Saved",
  toastErrorTitle: "Something went wrong",
  toastInfoTitle: "Heads up",

  allWidgets: "All Widgets",
  allExtensions: "All Extensions",
  allElements: "All Elements",
  allFeatures: "All Features",
  enabled: "Enabled",
  disabled: "Disabled",
  enableAll: "Enable All",
  enableAllElements: "Enable all elements",
  enableAllWidgets: "Enable all widgets",
  enableAllWidgetsCaption: "Enable All Elements",
  enableAllExtensions: "Enable all extensions",
  enableAllExtensionsCaption: "Enable All Extensions",
  enableAllFeatures: "Enable all features",
  enableAllFeaturesCaption: "Enable All Features",

  saving: "Saving...",
  saveSettings: "Save Settings",
  settingsSaved: "Settings saved.",

  loadingWidgets: "Loading widgets from WordPress...",
  loadingExtensions: "Loading extensions from WordPress...",
  loadingFormSettings: "Loading form settings from WordPress...",
  loadingFeatures: "Loading features from WordPress...",
  failedLoadWidgets: "Failed to load widget settings from WordPress.",
  failedSaveWidgets: "Failed to save widget settings.",
  failedLoadExtensions: "Failed to load extensions from WordPress.",
  failedSaveExtensions: "Failed to save extension settings.",
  failedLoadFormSettings: "Failed to load form settings from WordPress.",
  failedSaveFormSettings: "Failed to save form settings.",
  failedLoadFeatures: "Failed to load features from WordPress.",
  failedSaveFeatures: "Could not save feature settings.",
  featuresSaved: "Feature settings saved.",
  featureForcedOn: "Forced on by code (a filter overrides this switch).",
  featureForcedOff: "Forced off by code (a filter overrides this switch).",
  requestFailed: "Request failed. Please try again.",
  configUnavailable: "Pixels Core dashboard config is not available.",

  noWidgetsFound: "No widgets match your search or filter.",
  noExtensionsFound: "No extensions match your search or filter.",
  noFeaturesFound: "No features match your search or filter.",

  pro: "Pro",
  upgrade: "Upgrade",
  widgetsLabel: "Widgets",
  extensionsLabel: "Extensions",
  featuresLabel: "Features",
  toggleItem: "Toggle %s",
  itemDocumentation: "%s documentation",
  itemDemo: "%s demo",

  welcomeTitle: "Welcome to Pixels Core",
  welcomeDescription:
    "Your dashboard is ready. Explore widgets and extensions to build faster.",
  licenseFree: "Free",
  licenseProActive: "Pro Active",
  licenseProInactive: "Pro Inactive",
  licenseProTeaser: "Pixels Core Pro unlocks premium widgets",
  noLicenseKey: "No license key",
  activate: "Activate",
  deactivate: "Deactivate",
  getPro: "Get Pro",

  heroTitle: "Design Faster With Pixels Core Widgets",
  heroDescription:
    "Learn how to use widgets and extensions to ship polished interfaces without reinventing common UI patterns.",
  watchTutorials: "Watch Tutorials",

  quickStartTitle: "Quick start",
  quickStartDescriptionOne:
    "One step to get the most out of Pixels Core.",
  quickStartDescriptionTwo:
    "Two steps to get the most out of Pixels Core.",
  quickStartDescriptionThree:
    "Three steps to get the most out of Pixels Core.",
  quickStartStep: "Step %d",
  quickStartWidgetsTitle: "Enable widgets you need",
  quickStartWidgetsDescription:
    "Turn on only the Elementor widgets you use to keep the editor lean.",
  quickStartExtensionTitle: "Turn on site utilities",
  quickStartExtensionDescription:
    "Enable performance, SEO, security, and utility extensions for your site.",
  quickStartFormTitle: "Protect and connect forms",
  quickStartFormDescription:
    "Configure reCAPTCHA and Mailchimp for the Contact Form widget.",

  whatsNewTitle: "What's new",
  whatsNewDescription:
    "Recent updates across the Pixels Core dashboard.",

  helpTitle: "Need Any Help?",
  communityTitle: "Join Our Community",
  knowledgeBaseTitle: "View Knowledge Base",

  statsTotal: "Total",
  statsActive: "Active",
  statsInactive: "Inactive",
  statsCountLabel: "%1$d %2$s",

  requirementsTitle: "Requirements",
  elementorTitle: "Elementor",
  elementorActive: "Active — widgets are available in the editor.",
  elementorInactive:
    "Not active — install and activate Elementor to use widgets.",
  nestedElementsTitle: "Nested Elements",
  nestedElementsActive: "Enabled for containers that support nesting.",
  nestedElementsInactive:
    "Optional — enable in Elementor for nested layouts.",

  reviewTitle: "Show Your Love",
  reviewDescription:
    "Enjoying Pixels Core? A quick review helps the project grow.",
  leaveReview: "Leave a Review →",

  logoAlt: "Pixels Core",
};

export const getDashboardI18n = (): DashboardI18n => ({
  ...defaultI18n,
  ...readConfig()?.i18n,
});

export const t = (
  key: keyof DashboardI18n,
  ...args: Array<string | number>
): string => {
  const strings = getDashboardI18n();
  let value = strings[key] ?? defaultI18n[key] ?? String(key);

  args.forEach((arg, index) => {
    value = value.replace(`%${index + 1}$s`, String(arg));
    value = value.replace(`%${index + 1}$d`, String(arg));
    value = value.replace("%s", String(arg));
    value = value.replace("%d", String(arg));
  });

  return value;
};
