export type SearchResultType = "widget" | "extension" | "feature";

export type SearchResult = {
  id: string;
  name: string;
  category: string;
  type: SearchResultType;
  enabled: boolean;
  tab: "widgets" | "extension" | "features";
};
