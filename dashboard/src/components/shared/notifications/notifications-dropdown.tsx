import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { type ReactNode } from "react";
import ChangelogTimelineEntry from "./changelog-timeline-entry";
import { changelogEntries as initialEntries } from "./data";

type NotificationsDropdownProps = {
  trigger: ReactNode;
};

const NotificationsDropdown = ({ trigger }: NotificationsDropdownProps) => {
  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>{trigger}</DropdownMenuTrigger>
      <DropdownMenuContent
        align="end"
        sideOffset={8}
        className="z-50 w-[min(100vw-2rem,480px)] overflow-hidden rounded-xl border border-slate-200 bg-white text-slate-900 shadow-xl outline-none"
      >
        <div className="border-b border-slate-100  px-6 py-6 text-center">
          <div className="flex items-center justify-center gap-2">
            <h2 className="text-2xl font-bold text-slate-900">Changelog</h2>
          </div>
          <p className="mx-auto mt-2 max-w-[320px] text-sm leading-relaxed text-slate-500">
            Stay up to date with the latest Pixels Core releases, fixes, and
            improvements.
          </p>
        </div>

        <div className="max-h-105 overflow-y-auto px-6 py-6">
          {initialEntries.length === 0 ? (
            <p className="py-10 text-center text-sm text-slate-400">
              No changelog entries yet.
            </p>
          ) : (
            <ul className="relative border-l border-dashed border-slate-200">
              {initialEntries.map((entry, index) => (
                <ChangelogTimelineEntry
                  key={entry.id}
                  entry={entry}
                  isLast={index === initialEntries.length - 1}
                />
              ))}
            </ul>
          )}
        </div>
      </DropdownMenuContent>
    </DropdownMenu>
  );
};

export default NotificationsDropdown;
