import { initialExtensions } from "@/components/dashboard/extensions/data";
import { initialWidgets } from "@/components/dashboard/widgets/data";
import type { SearchResult } from "./types";

export const searchIndex: SearchResult[] = [
  ...initialWidgets.map((widget) => ({
    id: widget.id,
    name: widget.name,
    category: widget.category,
    type: "widget" as const,
    enabled: widget.enabled,
    tab: "widgets" as const,
  })),
  ...initialExtensions.map((extension) => ({
    id: extension.id,
    name: extension.name,
    category: extension.category,
    type: "extension" as const,
    enabled: extension.enabled,
    tab: "extension" as const,
  })),
];
