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
  manageLicense: "Manage License",
  getPro: "Get Pro",
  licenseKeyLabel: "License key",
  licenseKeyPlaceholder: "Enter your license key",
  enterLicenseKey: "Please enter a license key.",
  activating: "Activating...",
  deactivating: "Deactivating...",
  checking: "Checking...",
  checkAgain: "Check Again",
  confirmDeactivateLicense:
    "Deactivate this license? This site will stop receiving automatic updates.",
  licenseActivated: "License activated successfully!",
  licenseDeactivated: "License deactivated successfully.",
  licenseLifetime: "Lifetime license",
  licenseExpiresOn: "Expires %s",
  licenseExpiredOn: "Expired on %s",
  licenseInvalidHint:
    "The saved license key is invalid or not active for this site. Enter a valid key to re-activate.",

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

  sidebarMcp: "AI / MCP",
  tabMcpTitle: "AI / MCP",
  tabMcpDescription: "Connect Claude and other AI clients so they can build pages with your widgets.",
  mcpLoading: "Loading MCP status...",
  mcpFailedLoad: "Could not load the MCP status.",
  mcpServerTitle: "MCP Server",
  mcpServerOn: "On. AI clients can connect at this address.",
  mcpServerOff: "Off. Switch it on to let AI clients connect.",
  mcpToggleServer: "Switch the MCP Server on or off",
  mcpServerEnabled: "MCP Server switched on.",
  mcpServerDisabled: "MCP Server switched off.",
  mcpEndpointLabel: "Endpoint",
  mcpCopy: "Copy",
  mcpCopied: "Copied to clipboard.",
  mcpConnectClient: "Connect an AI client",
  mcpSummaryActive: "%d active now",
  mcpSummaryTotal: "%d connections",
  mcpHealthTitle: "Health check",
  mcpHealthDescription: "Checks whether AI clients can reach this site and sign in.",
  mcpRunCheck: "Run check",
  mcpRunningCheck: "Checking...",
  mcpHealthAllGood: "Everything is ready for AI clients.",
  mcpHealthProblems: "Fix the items marked below before connecting.",
  mcpConnectionsTitle: "Connections",
  mcpConnectionsDescription: "Each AI client signs in with its own Application Password. Revoke one to cut that client off.",
  mcpNoConnections: "No AI client has connected yet.",
  mcpStateActive: "Active",
  mcpStateIdle: "Connected",
  mcpStateNever: "Waiting for first use",
  mcpLastSeen: "Last used %s",
  mcpCreatedOn: "Created %s",
  mcpToolCalls: "Tool calls: %d",
  mcpLastTool: "Last tool: %s",
  mcpUser: "Acts as %s",
  mcpRevoke: "Revoke",
  mcpRevokeConfirm: "Revoke \"%s\"? The AI client using it stops working until you connect it again.",
  mcpRevoked: "Connection revoked.",
  mcpWizardTitle: "Connect an AI client",
  mcpWizardChooseClient: "Which app will use this site?",
  mcpWizardCreate: "Create connection",
  mcpWizardCreating: "Creating...",
  mcpWizardSetupTitle: "Add Pixels to %s",
  mcpWizardOnce: "This contains a new password and is shown only once. Keep it private.",
  mcpWizardWaiting: "Waiting for %s to connect...",
  mcpWizardWaitingHint: "Finish the setup, then ask the AI something like \"List my pages\". This updates by itself.",
  mcpWizardConnected: "Connected! %s is using this site.",
  mcpWizardDone: "Done",
  mcpWizardBack: "Back",
  mcpWizardCloseHint: "You can close this window at any time; the connection keeps working.",
  mcpClientHintClaudeCode: "Terminal and IDE",
  mcpClientHintClaudeDesktop: "Desktop app",
  mcpClientHintCursor: "Code editor",
  mcpClientHintOther: "Any client with Streamable HTTP",
  mcpClaudeAiSoon: "claude.ai on the web needs OAuth sign-in, which is coming soon.",
  mcpEnableFirst: "Switch on the MCP Server to connect a client.",
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
