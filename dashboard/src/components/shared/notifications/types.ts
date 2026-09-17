export type ChangelogCategory = "bug-fixes" | "new-feature" | "mobile";

export type ChangelogEntry = {
  id: string;
  date: string;
  category: ChangelogCategory;
  categoryLabel: string;
  title: string;
  description: string;
  href: string;
  unread?: boolean;
};

export const categoryStyles: Record<
  ChangelogCategory,
  { dot: string; link: string }
> = {
  "bug-fixes": {
    dot: "bg-emerald-500",
    link: "text-emerald-600 hover:text-emerald-700",
  },
  "new-feature": {
    dot: "bg-rose-500",
    link: "text-rose-500 hover:text-rose-600",
  },
  mobile: {
    dot: "bg-sky-500",
    link: "text-sky-500 hover:text-sky-600",
  },
};
