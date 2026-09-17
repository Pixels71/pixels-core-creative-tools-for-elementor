import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import "./index.css";
import { ToastProvider } from "./components/ui/toast";
import { ensureDashboardConfig } from "./lib/api";
import Home from "./pages/home";

ensureDashboardConfig();

const mountNode =
  document.getElementById("pixeccte-dashboard-root") ??
  document.getElementById("root");

if (mountNode) {
  createRoot(mountNode).render(
    <StrictMode>
      <ToastProvider>
        <Home />
      </ToastProvider>
    </StrictMode>,
  );
}
