export type WidgetCategory =
  | "content"
  | "creative"
  | "marketing"
  | "form"
  | "social";

export type WidgetItem = {
  id: string;
  name: string;
  category: WidgetCategory;
  enabled: boolean;
  description?: string;
  available?: boolean;
  tier?: "free" | "pro";
  isPro?: boolean;
  upgradeUrl?: string;
  docsUrl?: string;
  demoUrl?: string;
};
