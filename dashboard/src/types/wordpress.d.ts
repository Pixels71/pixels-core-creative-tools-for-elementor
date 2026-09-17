import type { ExtensionItem } from "@/components/dashboard/extensions/types";
import type { FeatureItem } from "@/components/dashboard/features/types";
import type { FormSettings } from "@/components/dashboard/widgets/form-settings-types";
import type { WidgetItem } from "@/components/dashboard/widgets/types";

export type DashboardNotice = {
  type: "success" | "warning" | "error" | "info";
  message: string;
};

export type PixelsCoreLicense = {
  key?: string;
  active?: boolean;
  maskedKey?: string;
  managedBy?: string;
  status?: string;
  expires?: string;
};

export type PixelsCoreDashboardLinks = {
  tutorials?: string;
  help?: string;
  community?: string;
  knowledgeBase?: string;
  review?: string;
  pro?: string;
};

export type DashboardI18n = {
  sidebarIntroduction: string;
  sidebarWidgets: string;
  sidebarExtension: string;
  sidebarFormSettings: string;
  sidebarFeatures: string;
  dashboardNavLabel: string;

  tabIntroductionTitle: string;
  tabIntroductionDescription: string;
  tabWidgetsTitle: string;
  tabWidgetsDescription: string;
  tabExtensionTitle: string;
  tabExtensionDescription: string;
  tabFormSettingsTitle: string;
  tabFormSettingsDescription: string;
  tabFeaturesTitle: string;
  tabFeaturesDescription: string;

  search: string;
  notifications: string;
  searchByName: string;
  searchWidgetsExtensions: string;
  searchModalTitle: string;
  searchModalDescription: string;
  loadingSearchIndex: string;
  noSearchResults: string;
  keyboardShortcut: string;
  dismissNotification: string;
  toastSuccessTitle: string;
  toastErrorTitle: string;
  toastInfoTitle: string;

  allWidgets: string;
  allExtensions: string;
  allElements: string;
  allFeatures: string;
  enabled: string;
  disabled: string;
  enableAll: string;
  enableAllElements: string;
  enableAllWidgets: string;
  enableAllWidgetsCaption: string;
  enableAllExtensions: string;
  enableAllExtensionsCaption: string;
  enableAllFeatures: string;
  enableAllFeaturesCaption: string;

  saving: string;
  saveSettings: string;
  settingsSaved: string;

  loadingWidgets: string;
  loadingExtensions: string;
  loadingFormSettings: string;
  loadingFeatures: string;
  failedLoadWidgets: string;
  failedSaveWidgets: string;
  failedLoadExtensions: string;
  failedSaveExtensions: string;
  failedLoadFormSettings: string;
  failedSaveFormSettings: string;
  failedLoadFeatures: string;
  failedSaveFeatures: string;
  featuresSaved: string;
  featureForcedOn: string;
  featureForcedOff: string;
  requestFailed: string;
  configUnavailable: string;

  noWidgetsFound: string;
  noExtensionsFound: string;
  noFeaturesFound: string;

  pro: string;
  upgrade: string;
  widgetsLabel: string;
  extensionsLabel: string;
  featuresLabel: string;
  toggleItem: string;
  itemDocumentation: string;
  itemDemo: string;

  welcomeTitle: string;
  welcomeDescription: string;
  licenseFree: string;
  licenseProActive: string;
  licenseProInactive: string;
  licenseProTeaser: string;
  noLicenseKey: string;
  activate: string;
  deactivate: string;
  getPro: string;

  heroTitle: string;
  heroDescription: string;
  watchTutorials: string;

  quickStartTitle: string;
  quickStartDescriptionOne: string;
  quickStartDescriptionTwo: string;
  quickStartDescriptionThree: string;
  quickStartStep: string;
  quickStartWidgetsTitle: string;
  quickStartWidgetsDescription: string;
  quickStartExtensionTitle: string;
  quickStartExtensionDescription: string;
  quickStartFormTitle: string;
  quickStartFormDescription: string;

  whatsNewTitle: string;
  whatsNewDescription: string;

  helpTitle: string;
  communityTitle: string;
  knowledgeBaseTitle: string;

  statsTotal: string;
  statsActive: string;
  statsInactive: string;
  statsCountLabel: string;

  requirementsTitle: string;
  elementorTitle: string;
  elementorActive: string;
  elementorInactive: string;
  nestedElementsTitle: string;
  nestedElementsActive: string;
  nestedElementsInactive: string;

  reviewTitle: string;
  reviewDescription: string;
  leaveReview: string;

  logoAlt: string;
};

export type PixelsCoreDashboardConfig = {
  restUrl: string;
  nonce: string;
  adminUrl: string;
  pluginUrl: string;
  elementorActive: boolean;
  nestedElementsActive: boolean;
  notices: DashboardNotice[];
  version?: string;
  proActive?: boolean;
  showProUpsell?: boolean;
  upgradeUrl?: string;
  license?: PixelsCoreLicense;
  links?: PixelsCoreDashboardLinks;
  widgets?: WidgetItem[];
  extensions?: ExtensionItem[];
  modules?: FeatureItem[];
  formSettings?: FormSettings | null;
  i18n?: Partial<DashboardI18n>;
};

declare global {
  interface Window {
    pixeccteDashboard?: PixelsCoreDashboardConfig;
  }
}

export {};
