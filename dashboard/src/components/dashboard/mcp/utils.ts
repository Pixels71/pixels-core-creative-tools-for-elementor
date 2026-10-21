import type { McpClientKey } from "@/lib/api";
import { getDashboardI18n } from "@/lib/i18n";

/** "3 minutes ago" in the admin's language. */
export const relativeTime = (timestamp: number, now: number): string => {
  const seconds = Math.round(timestamp - now);
  const format = new Intl.RelativeTimeFormat(
    document.documentElement.lang || undefined,
    { numeric: "auto" },
  );
  const units: Array<[Intl.RelativeTimeFormatUnit, number]> = [
    ["year", 31536000],
    ["month", 2592000],
    ["day", 86400],
    ["hour", 3600],
    ["minute", 60],
  ];

  for (const [unit, size] of units) {
    if (Math.abs(seconds) >= size) {
      return format.format(Math.round(seconds / size), unit);
    }
  }

  return format.format(seconds, "second");
};

export const clientHints = (): Record<McpClientKey, string> => {
  const i18n = getDashboardI18n();

  return {
    "claude-code": i18n.mcpClientHintClaudeCode,
    "claude-desktop": i18n.mcpClientHintClaudeDesktop,
    cursor: i18n.mcpClientHintCursor,
    other: i18n.mcpClientHintOther,
  };
};

export const copyText = async (text: string): Promise<boolean> => {
  try {
    await navigator.clipboard.writeText(text);
    return true;
  } catch {
    // Clipboard API needs a secure context; fall back for plain-HTTP sites.
    const area = document.createElement("textarea");
    area.value = text;
    area.style.position = "fixed";
    area.style.opacity = "0";
    document.body.appendChild(area);
    area.select();
    const copied = document.execCommand("copy");
    area.remove();
    return copied;
  }
};
