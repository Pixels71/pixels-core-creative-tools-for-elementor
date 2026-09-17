export type FeatureItem = {
  id: string;
  name: string;
  category: string;
  enabled: boolean;
  description?: string;
  impact?: string;
  icon?: string;
  available?: boolean;
  /** Whether the module actually loads after code filters are applied. */
  effective: boolean;
  /** True when a filter overrides the stored switch position. */
  forced: boolean;
  tier?: "free" | "pro";
  isPro?: boolean;
};
