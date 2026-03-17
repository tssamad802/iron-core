/* ═══════════════════════════════════════════════════
   GYMFLOW — THEME MANAGER  (theme.js)
   • Reads saved preference from localStorage on load
   • Applies theme before paint to prevent flash
   • Exposes toggleTheme() used by buttons
   • Works across all pages automatically
═══════════════════════════════════════════════════ */

(function () {
  "use strict";

  var STORAGE_KEY = "ironcore_theme";

  /* ── Apply theme immediately (call before DOMContentLoaded) ── */
  function applyTheme(theme) {
    if (theme === "light") {
      document.documentElement.setAttribute("data-theme", "light");
    } else {
      document.documentElement.removeAttribute("data-theme");
    }
  }

  /* ── Read saved preference; fall back to system preference ── */
  function getSavedTheme() {
    try {
      var saved = localStorage.getItem(STORAGE_KEY);
      if (saved === "light" || saved === "dark") return saved;
    } catch (e) {
      /* private browsing */
    }

    /* honour OS preference if nothing saved */
    if (
      window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: light)").matches
    ) {
      return "light";
    }
    return "dark";
  }

  /* ── Save preference ── */
  function saveTheme(theme) {
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch (e) {}
  }

  /* ── Public toggle (called by button onclick) ── */
  window.toggleTheme = function () {
    var current =
      document.documentElement.getAttribute("data-theme") === "light"
        ? "light"
        : "dark";
    var next = current === "light" ? "dark" : "light";
    applyTheme(next);
    saveTheme(next);

    /* optional: show toast if the helper exists on the page */
    if (typeof showToast === "function") {
      var icon = next === "light" ? "☀️" : "🌙";
      var label = next === "light" ? "Light Mode" : "Dark Mode";
      showToast(icon, label + " Activated", "Preference saved");
    }
  };

  /* ── Apply on load (prevents FOUC) ── */
  applyTheme(getSavedTheme());
})();
