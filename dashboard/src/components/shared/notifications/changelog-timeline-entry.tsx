import { cn } from "@/lib/utils";
import { ArrowRight } from "lucide-react";
import { type ChangelogEntry } from "./types";

type ChangelogTimelineEntryProps = {
  entry: ChangelogEntry;
  isLast?: boolean;
};

const ChangelogTimelineEntry = ({
  entry,
  isLast = false,
}: ChangelogTimelineEntryProps) => {
  return (
    <li className={cn("relative pl-6", !isLast && "pb-8")}>
      <span
        className="absolute top-1.5 bg-slate-200 left-0 z-10 size-3 -translate-x-1/2 rounded-full ring-4 ring-slate-100"
        aria-hidden="true"
      />

      <p className="text-sm text-slate-500">
        {entry.date} · {entry.categoryLabel}
      </p>

      <article className="mt-3 rounded-xl border border-slate-100 bg-white p-5">
        <h3 className="text-base font-semibold text-slate-900">
          {entry.title}
        </h3>
        <p className="mt-2 text-sm leading-relaxed text-slate-500">
          {entry.description}
        </p>
        <a
          href={entry.href}
          className="mt-4 inline-flex items-center gap-1 text-sm font-medium transition-colors text-slate-500 hover:text-slate-600"
        >
          Learn More
          <ArrowRight className="size-3.5" strokeWidth={2} />
        </a>
      </article>
    </li>
  );
};

export default ChangelogTimelineEntry;
