import { pcControl } from "@/lib/form-control";
import type { FormSettings } from "./form-settings-types";

type FormSettingsPanelProps = {
  settings: FormSettings;
  onChange: (settings: FormSettings) => void;
};

const formClassName =
  "grid gap-5 rounded-2xl border border-slate-100 bg-white p-8";

const FormSettingsPanel = ({ settings, onChange }: FormSettingsPanelProps) => {
  const updateField = (key: keyof FormSettings, value: string) => {
    onChange({ ...settings, [key]: value });
  };

  return (
    <div className="space-y-6">
      <form className={formClassName} onSubmit={(event) => event.preventDefault()}>
        <h3 className="text-sm font-semibold text-slate-900">reCAPTCHA v2</h3>

        <label className="grid gap-2">
          <span className="text-sm font-medium text-slate-700">Site Key</span>
          <input
            type="text"
            value={settings.recaptchaV2SiteKey}
            onChange={(event) =>
              updateField("recaptchaV2SiteKey", event.target.value)
            }
            className={pcControl}
            autoComplete="off"
            placeholder="Enter your reCAPTCHA v2 Site Key"
          />
        </label>

        <label className="grid gap-2">
          <span className="text-sm font-medium text-slate-700">Secret Key</span>
          <input
            type="password"
            value={settings.recaptchaV2SecretKey}
            onChange={(event) =>
              updateField("recaptchaV2SecretKey", event.target.value)
            }
            className={pcControl}
            autoComplete="new-password"
            placeholder="Enter your reCAPTCHA v2 Secret Key"
          />
        </label>
      </form>

      <form className={formClassName} onSubmit={(event) => event.preventDefault()}>
        <h3 className="text-sm font-semibold text-slate-900">reCAPTCHA v3</h3>

        <label className="grid gap-2">
          <span className="text-sm font-medium text-slate-700">Site Key</span>
          <input
            type="text"
            value={settings.recaptchaV3SiteKey}
            onChange={(event) =>
              updateField("recaptchaV3SiteKey", event.target.value)
            }
            className={pcControl}
            autoComplete="off"
            placeholder="Enter your reCAPTCHA v3 Site Key"
          />
        </label>

        <label className="grid gap-2">
          <span className="text-sm font-medium text-slate-700">Secret Key</span>
          <input
            type="password"
            value={settings.recaptchaV3SecretKey}
            onChange={(event) =>
              updateField("recaptchaV3SecretKey", event.target.value)
            }
            className={pcControl}
            autoComplete="new-password"
            placeholder="Enter your reCAPTCHA v3 Secret Key"
          />
        </label>
      </form>

      <form className={formClassName} onSubmit={(event) => event.preventDefault()}>
        <h3 className="text-sm font-semibold text-slate-900">Mailchimp</h3>

        <label className="grid gap-2">
          <span className="text-sm font-medium text-slate-700">API Key</span>
          <input
            type="password"
            value={settings.mailchimpApiKey}
            onChange={(event) =>
              updateField("mailchimpApiKey", event.target.value)
            }
            className={pcControl}
            autoComplete="new-password"
            placeholder="Enter your Mailchimp API Key"
          />
          <span className="text-xs text-slate-500">
            Find your API key in Mailchimp → Account → Extras → API keys.
          </span>
        </label>

        <label className="grid gap-2">
          <span className="text-sm font-medium text-slate-700">
            Audience ID
          </span>
          <input
            type="text"
            value={settings.mailchimpListId}
            onChange={(event) =>
              updateField("mailchimpListId", event.target.value)
            }
            className={pcControl}
            autoComplete="off"
            placeholder="Enter your Mailchimp Audience ID"
          />
          <span className="text-xs text-slate-500">
            Audience → Settings → Audience name and defaults → Audience ID.
          </span>
        </label>
      </form>
    </div>
  );
};

export default FormSettingsPanel;
