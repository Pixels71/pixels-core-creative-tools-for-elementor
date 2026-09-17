export type ExtensionCategory =
  | "performance"
  | "security"
  | "seo"
  | "analytics"
  | "utility";

export type ExtensionItem = {
  id: string;
  name: string;
  category: ExtensionCategory;
  enabled: boolean;
  description?: string;
  available?: boolean;
  tier?: "free" | "pro";
  isPro?: boolean;
  upgradeUrl?: string;
  docsUrl?: string;
  demoUrl?: string;
};
