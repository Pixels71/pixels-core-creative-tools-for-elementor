import WidgetCard from "./widget-card";
import type { WidgetItem } from "./types";

type WidgetGridProps = {
  widgets: WidgetItem[];
  onToggle: (id: string, enabled: boolean) => void;
};

const WidgetGrid = ({ widgets, onToggle }: WidgetGridProps) => {
  if (widgets.length === 0) {
    return (
      <p className="rounded-xl border border-dashed border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500">
        No widgets match your filters.
      </p>
    );
  }

  return (
    <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      {widgets.map((widget) => (
        <WidgetCard key={widget.id} widget={widget} onToggle={onToggle} />
      ))}
    </div>
  );
};

export default WidgetGrid;
