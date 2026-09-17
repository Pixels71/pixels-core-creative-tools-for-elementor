import type { ChangelogEntry } from "./types";

export const changelogEntries: ChangelogEntry[] = [
  {
    id: "1",
    date: "Jun 22, 2023",
    category: "bug-fixes",
    categoryLabel: "Bug Fixes",
    title: "Widget toggle state now persists correctly",
    description:
      "Fixed an issue where widget enable/disable states reset after page reload. Settings are now saved and restored from the URL and local state.",
    href: "#",
    unread: true,
  },
  {
    id: "2",
    date: "Jun 20, 2023",
    category: "new-feature",
    categoryLabel: "New Feature",
    title: "Extension panel with category filters",
    description:
      "Browse and manage extensions by category. Search, filter, and bulk-enable extensions from the new extension tab.",
    href: "#",
    unread: true,
  },
  {
    id: "3",
    date: "Jun 17, 2023",
    category: "mobile",
    categoryLabel: "Mobile",
    title: "Responsive sidebar and dashboard layout",
    description:
      "The dashboard sidebar and content area now adapt cleanly on smaller screens with improved spacing and touch-friendly controls.",
    href: "#",
  },
  {
    id: "4",
    date: "Jun 14, 2023",
    category: "bug-fixes",
    categoryLabel: "Bug Fixes",
    title: "Notification dropdown positioning fix",
    description:
      "Resolved dropdown alignment issues on narrow viewports. The changelog panel now opens correctly aligned to the bell icon.",
    href: "#",
  },
  {
    id: "5",
    date: "Jun 10, 2023",
    category: "new-feature",
    categoryLabel: "New Feature",
    title: "Sticky sidebar navigation",
    description:
      "Sidebar tabs stay visible while scrolling through long widget and extension lists for faster navigation.",
    href: "#",
  },
];
