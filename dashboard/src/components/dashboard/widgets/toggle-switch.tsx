import { cn } from "../../../lib/utils";

type ToggleSwitchProps = {
  checked: boolean;
  onChange: (checked: boolean) => void;
  label: string;
  caption?: string;
  size?: "sm" | "md";
  className?: string;
  disabled?: boolean;
};

const ToggleSwitch = ({
  checked,
  onChange,
  label,
  caption,
  size = "md",
  className,
  disabled = false,
}: ToggleSwitchProps) => {
  return (
    <button
      type="button"
      role="switch"
      aria-checked={checked}
      aria-label={label}
      disabled={disabled}
      onClick={() => onChange(!checked)}
      className={cn(
        "inline-flex shrink-0 items-center gap-2.5 border-0 bg-transparent p-0 shadow-none outline-none",
        caption && "cursor-pointer",
        disabled && "cursor-not-allowed opacity-50",
        className,
      )}
    >
      <span
        className={cn(
          "relative inline-flex shrink-0 items-center rounded-full transition-colors",
          size === "sm" ? "h-5 w-9" : "h-6 w-11",
          checked ? "bg-[#091146]" : "bg-slate-300",
        )}
      >
        <span
          data-checked={checked}
          className={cn(
            "pointer-events-none block rounded-full bg-white shadow-sm transition-transform",
            size === "sm" ? "size-4" : "size-5",
            size === "sm"
              ? "translate-x-0.5 data-[checked=true]:translate-x-4.5"
              : "translate-x-0.5 data-[checked=true]:translate-x-5.5",
          )}
        />
      </span>

      {caption ? (
        <span className="text-sm font-medium text-slate-600">{caption}</span>
      ) : null}
    </button>
  );
};

export default ToggleSwitch;
