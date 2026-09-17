import ExtensionCard from "./extension-card";
import type { ExtensionItem } from "./types";

type ExtensionGridProps = {
  extensions: ExtensionItem[];
  onToggle: (id: string, enabled: boolean) => void;
};

const ExtensionGrid = ({ extensions, onToggle }: ExtensionGridProps) => {
  if (extensions.length === 0) {
    return (
      <p className="rounded-xl border border-dashed border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500">
        No extensions match your filters.
      </p>
    );
  }

  return (
    <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      {extensions.map((extension) => (
        <ExtensionCard
          key={extension.id}
          extension={extension}
          onToggle={onToggle}
        />
      ))}
    </div>
  );
};

export default ExtensionGrid;
