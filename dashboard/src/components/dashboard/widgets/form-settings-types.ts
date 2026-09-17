export type FormSettings = {
  recaptchaV2SiteKey: string;
  recaptchaV2SecretKey: string;
  recaptchaV3SiteKey: string;
  recaptchaV3SecretKey: string;
  mailchimpApiKey: string;
  mailchimpListId: string;
};

export const emptyFormSettings: FormSettings = {
  recaptchaV2SiteKey: "",
  recaptchaV2SecretKey: "",
  recaptchaV3SiteKey: "",
  recaptchaV3SecretKey: "",
  mailchimpApiKey: "",
  mailchimpListId: "",
};
