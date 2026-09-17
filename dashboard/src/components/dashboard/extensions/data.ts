import type { ExtensionItem } from "./types";

export const initialExtensions: ExtensionItem[] = [
  {
    id: "lazy-load",
    name: "Lazy Load",
    category: "performance",
    enabled: true,
  },
  {
    id: "reading-progress",
    name: "Reading Progress Bar",
    category: "performance",
    enabled: false,
  },
  {
    id: "image-optimizer",
    name: "Image Optimizer",
    category: "performance",
    enabled: false,
  },
  {
    id: "asset-minifier",
    name: "Asset Minifier",
    category: "performance",
    enabled: true,
  },
  {
    id: "content-protection",
    name: "Content Protection",
    category: "security",
    enabled: false,
  },
  {
    id: "login-guard",
    name: "Login Guard",
    category: "security",
    enabled: false,
  },
  {
    id: "spam-blocker",
    name: "Spam Blocker",
    category: "security",
    enabled: true,
  },
  {
    id: "schema-markup",
    name: "Schema Markup",
    category: "seo",
    enabled: false,
  },
  {
    id: "open-graph",
    name: "Open Graph Tags",
    category: "seo",
    enabled: false,
  },
  {
    id: "sitemap-boost",
    name: "Sitemap Boost",
    category: "seo",
    enabled: true,
  },
  {
    id: "event-tracker",
    name: "Event Tracker",
    category: "analytics",
    enabled: false,
  },
  {
    id: "heatmap-ready",
    name: "Heatmap Ready",
    category: "analytics",
    enabled: false,
  },
  {
    id: "duplicator",
    name: "Post Duplicator",
    category: "utility",
    enabled: false,
  },
  {
    id: "conditional-display",
    name: "Conditional Display",
    category: "utility",
    enabled: false,
  },
  {
    id: "cross-domain-copy",
    name: "Cross Domain Copy",
    category: "utility",
    enabled: false,
  },
];
