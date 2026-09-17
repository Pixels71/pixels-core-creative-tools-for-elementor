/* eslint-disable react-refresh/only-export-components */
import { AlertCircle, CheckCircle2, Info, X } from "lucide-react";
import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useRef,
  useState,
  type CSSProperties,
  type ReactNode,
} from "react";
import { createPortal } from "react-dom";
import { getDashboardI18n } from "@/lib/i18n";
import { cn } from "@/lib/utils";

export type ToastType = "success" | "error" | "info";

export type Toast = {
  id: number;
  type: ToastType;
  message: string;
  title?: string;
  duration: number;
  removing?: boolean;
};

type ToastOptions = {
  title?: string;
  /** Milliseconds before the toast auto-dismisses. Pass 0 to keep it open. */
  duration?: number;
};

type ToastApi = {
  show: (type: ToastType, message: string, options?: ToastOptions) => number;
  success: (message: string, options?: ToastOptions) => number;
  error: (message: string, options?: ToastOptions) => number;
  info: (message: string, options?: ToastOptions) => number;
  dismiss: (id: number) => void;
};

const DEFAULT_DURATION: Record<ToastType, number> = {
  success: 4000,
  info: 4500,
  error: 7000,
};

/** How many toasts stay visible in the collapsed deck; older ones fade behind. */
const VISIBLE_STACK = 3;
/** Hard cap on live toasts; the oldest is dropped when exceeded. */
const MAX_TOASTS = 5;
const STACK_OFFSET_PX = 14;
const STACK_SCALE_STEP = 0.05;
const EXPANDED_GAP_PX = 12;
const EXIT_MS = 240;

const ToastContext = createContext<ToastApi | null>(null);

let nextToastId = 1;

type TimerEntry = { timeout: number; startedAt: number; remaining: number };

export const ToastProvider = ({ children }: { children: ReactNode }) => {
  const [toasts, setToasts] = useState<Toast[]>([]);
  const [paused, setPaused] = useState(false);
  const timers = useRef(new Map<number, TimerEntry>());
  const pausedRef = useRef(false);

  const remove = useCallback((id: number) => {
    timers.current.delete(id);
    setToasts((prev) => prev.filter((toast) => toast.id !== id));
  }, []);

  const dismiss = useCallback(
    (id: number) => {
      const timer = timers.current.get(id);
      if (timer) {
        window.clearTimeout(timer.timeout);
        timers.current.delete(id);
      }

      // Flag for the exit animation, then drop it once that has played.
      setToasts((prev) =>
        prev.map((toast) =>
          toast.id === id ? { ...toast, removing: true } : toast,
        ),
      );
      window.setTimeout(() => remove(id), EXIT_MS);
    },
    [remove],
  );

  const arm = useCallback(
    (id: number, remaining: number) => {
      timers.current.set(id, {
        timeout: window.setTimeout(() => dismiss(id), remaining),
        startedAt: Date.now(),
        remaining,
      });
    },
    [dismiss],
  );

  const show = useCallback(
    (type: ToastType, message: string, options: ToastOptions = {}) => {
      const id = nextToastId++;
      const duration = options.duration ?? DEFAULT_DURATION[type];

      setToasts((prev) => {
        const next = [
          ...prev,
          { id, type, message, title: options.title, duration },
        ];
        const live = next.filter((toast) => !toast.removing);

        // Keep the deck short: retire the oldest live toast when over the cap.
        if (live.length > MAX_TOASTS) {
          const oldest = live[0];
          window.setTimeout(() => dismiss(oldest.id), 0);
        }

        return next;
      });

      if (duration > 0 && !pausedRef.current) {
        arm(id, duration);
      } else if (duration > 0) {
        timers.current.set(id, {
          timeout: 0,
          startedAt: Date.now(),
          remaining: duration,
        });
      }

      return id;
    },
    [arm, dismiss],
  );

  const pause = useCallback(() => {
    if (pausedRef.current) return;
    pausedRef.current = true;
    setPaused(true);

    timers.current.forEach((entry, id) => {
      window.clearTimeout(entry.timeout);
      const elapsed = Date.now() - entry.startedAt;
      timers.current.set(id, {
        timeout: 0,
        startedAt: entry.startedAt,
        remaining: Math.max(entry.remaining - elapsed, 800),
      });
    });
  }, []);

  const resume = useCallback(() => {
    if (!pausedRef.current) return;
    pausedRef.current = false;
    setPaused(false);

    timers.current.forEach((entry, id) => arm(id, entry.remaining));
  }, [arm]);

  useEffect(() => {
    const activeTimers = timers.current;
    return () => {
      activeTimers.forEach((entry) => window.clearTimeout(entry.timeout));
      activeTimers.clear();
    };
  }, []);

  const api = useMemo<ToastApi>(
    () => ({
      show,
      success: (message, options) => show("success", message, options),
      error: (message, options) => show("error", message, options),
      info: (message, options) => show("info", message, options),
      dismiss,
    }),
    [show, dismiss],
  );

  return (
    <ToastContext.Provider value={api}>
      {children}
      <ToastViewport
        toasts={toasts}
        paused={paused}
        onDismiss={dismiss}
        onPause={pause}
        onResume={resume}
      />
    </ToastContext.Provider>
  );
};

export const useToast = (): ToastApi => {
  const context = useContext(ToastContext);

  if (!context) {
    throw new Error("useToast must be used inside <ToastProvider>.");
  }

  return context;
};

type ToastTheme = {
  Icon: typeof CheckCircle2;
  badge: string;
  ring: string;
  bar: string;
  glow: string;
};

const themes: Record<ToastType, ToastTheme> = {
  success: {
    Icon: CheckCircle2,
    badge: "bg-emerald-500 text-white shadow-emerald-500/40",
    ring: "ring-emerald-500/15",
    bar: "bg-emerald-500",
    glow: "from-emerald-50/90",
  },
  error: {
    Icon: AlertCircle,
    badge: "bg-rose-500 text-white shadow-rose-500/40",
    ring: "ring-rose-500/15",
    bar: "bg-rose-500",
    glow: "from-rose-50/90",
  },
  info: {
    Icon: Info,
    badge: "bg-sky-500 text-white shadow-sky-500/40",
    ring: "ring-sky-500/15",
    bar: "bg-sky-500",
    glow: "from-sky-50/90",
  },
};

type ToastViewportProps = {
  toasts: Toast[];
  paused: boolean;
  onDismiss: (id: number) => void;
  onPause: () => void;
  onResume: () => void;
};

const ToastViewport = ({
  toasts,
  paused,
  onDismiss,
  onPause,
  onResume,
}: ToastViewportProps) => {
  const i18n = getDashboardI18n();
  const [heights, setHeights] = useState<Record<number, number>>({});
  const [expanded, setExpanded] = useState(false);

  const container =
    typeof document !== "undefined"
      ? (document.getElementById("pixeccte-dashboard-root") ?? document.body)
      : null;

  // Newest first: index 0 sits at the front of the deck.
  const ordered = useMemo(() => [...toasts].reverse(), [toasts]);

  const measure = useCallback((id: number, element: HTMLDivElement | null) => {
    if (!element) return;
    const height = element.getBoundingClientRect().height;
    setHeights((prev) => (prev[id] === height ? prev : { ...prev, [id]: height }));
  }, []);

  // Once the deck empties, forget the hover state so the next toast starts collapsed.
  if (ordered.length === 0 && expanded) {
    setExpanded(false);
  }

  if (!container || ordered.length === 0) {
    return null;
  }

  const defaultTitles: Record<ToastType, string> = {
    success: i18n.toastSuccessTitle,
    error: i18n.toastErrorTitle,
    info: i18n.toastInfoTitle,
  };

  const frontHeight = heights[ordered[0].id] ?? 0;
  const expandedHeight = ordered.reduce(
    (total, toast, index) =>
      total + (heights[toast.id] ?? 0) + (index > 0 ? EXPANDED_GAP_PX : 0),
    0,
  );
  const stackHeight = expanded
    ? expandedHeight
    : frontHeight + Math.min(ordered.length - 1, VISIBLE_STACK - 1) * STACK_OFFSET_PX;

  // Cumulative distance from the bottom for each card when the deck is fanned out.
  const expandedOffsets = ordered.reduce<number[]>((offsets, _toast, index) => {
    const previous = index === 0 ? 0 : offsets[index - 1] + (heights[ordered[index - 1].id] ?? 0) + EXPANDED_GAP_PX;
    offsets.push(previous);
    return offsets;
  }, []);

  return createPortal(
    <div
      aria-live="polite"
      className="fixed right-5 bottom-5 z-[100] w-[min(100vw-2.5rem,380px)] transition-[height] duration-300 ease-out"
      style={{ height: stackHeight }}
      onMouseEnter={() => {
        setExpanded(true);
        onPause();
      }}
      onMouseLeave={() => {
        setExpanded(false);
        onResume();
      }}
      onFocus={() => {
        setExpanded(true);
        onPause();
      }}
      onBlur={(event) => {
        if (!event.currentTarget.contains(event.relatedTarget as Node | null)) {
          setExpanded(false);
          onResume();
        }
      }}
    >
      {ordered.map((toast, index) => {
        const theme = themes[toast.type];
        const title = toast.title ?? defaultTitles[toast.type];
        const hiddenInStack = !expanded && index >= VISIBLE_STACK;

        const offset = expanded
          ? expandedOffsets[index]
          : index * STACK_OFFSET_PX;

        const scale = expanded ? 1 : 1 - index * STACK_SCALE_STEP;

        const style: CSSProperties & Record<string, string | number> = {
          zIndex: ordered.length - index,
          "--pc-toast-offset": `${-offset}px`,
          "--pc-toast-scale": scale,
          "--pc-toast-duration": `${toast.duration}ms`,
          transformOrigin: "center bottom",
        };

        return (
          <div
            key={toast.id}
            ref={(element) => measure(toast.id, element)}
            role={toast.type === "error" ? "alert" : "status"}
            data-removing={toast.removing ? "true" : undefined}
            data-hidden={hiddenInStack ? "true" : undefined}
            style={style}
            className={cn(
              "pc-toast absolute right-0 bottom-0 w-full overflow-hidden rounded-2xl bg-white ring-1 shadow-xl shadow-slate-900/10",
              theme.ring,
            )}
          >
            <div
              aria-hidden="true"
              className={cn(
                "pointer-events-none absolute inset-0 bg-gradient-to-r to-transparent",
                theme.glow,
              )}
            />

            <div className="relative flex items-start gap-3.5 px-4 pt-4 pb-4">
              <span
                className={cn(
                  "flex size-9 shrink-0 items-center justify-center rounded-full shadow-lg",
                  theme.badge,
                )}
              >
                <theme.Icon className="size-[18px]" strokeWidth={2.25} />
              </span>

              <div className="min-w-0 flex-1 pt-0.5">
                <p className="text-sm font-semibold leading-5 text-slate-900">
                  {title}
                </p>
                <p className="mt-0.5 text-[13px] leading-5 text-slate-500">
                  {toast.message}
                </p>
              </div>

              <button
                type="button"
                onClick={() => onDismiss(toast.id)}
                aria-label={i18n.dismissNotification}
                className="-mt-1.5 -mr-1.5 shrink-0 rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-900/5 hover:text-slate-700"
              >
                <X className="size-4" strokeWidth={2} />
              </button>
            </div>

            {toast.duration > 0 ? (
              <div className="relative h-[3px] w-full bg-slate-900/5">
                <div
                  className={cn("pc-toast-progress h-full", theme.bar)}
                  style={{ animationPlayState: paused ? "paused" : "running" }}
                />
              </div>
            ) : null}
          </div>
        );
      })}
    </div>,
    container,
  );
};
